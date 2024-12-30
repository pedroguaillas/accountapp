<?php

namespace App\Jobs;

use App\Models\ActiveType;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;
use App\Models\FixedAsset;
use Carbon\Carbon;

class ProcessDepreciation implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $company = Company::first();
        $date = Carbon::now()->toDateString();
        $userId = auth()->user()->id;

        // Extraer cada Activo Fijo
        $fixedAssets = FixedAsset::where('company_id', $company->id)
            ->where('is_depretation_a', true)
            ->where('period', '>', 0)
            ->get();

        // Determinar el valor a Depreciar y Registrar
        foreach ($fixedAssets as $fixedAsset) {

            $base = $fixedAsset->value - $fixedAsset->residual_value;
            $baseMonths = $fixedAsset->period * 12;
            $valueDepreciation = $base / $baseMonths;

            $fixedAsset->depreciations()->create([
                'date' => $date,
                'percentage' => 20.0,
                'amount' => $base,
                'value' => $valueDepreciation,
                'acumulation' => $valueDepreciation,
            ]);
        }

        // De las Depreciaciones extraer agrupado por Tipo de Activo para Registrar el Asiento Contable
        $activeTypes = ActiveType::selectRaw('active_types.id,account_id,name,SUM(d.value) AS value_depreciation')
            ->join('fixed_assets AS fa', 'active_type_id', 'active_types.id')
            ->join('depreciations AS d', 'fixed_asset_id', 'fixed_assets.id')
            ->where('date')
            ->groupBy('id', 'account_id', 'name')
            ->get();

        foreach ($activeTypes as $activeType) {
            $journal = $company->journals()->create([
                'date' => $date,
                'description' => 'DEP MENSUAL ' . $activeType->name,
                'is_deductible' => true,
                'user_id' => $userId,
            ]);

            $journal->journalentries()->createMany([
                [
                    'account_id' => $activeType->account_dep_spent_id,
                    'debit' => $activeType->value_depreciation,
                    'have' => 0,
                ],
                [
                    'account_id' => $activeType->account_dep_id,
                    'debit' => 0,
                    'have' => $activeType->value_depreciation,
                ],
            ]);
        }
    }
}
