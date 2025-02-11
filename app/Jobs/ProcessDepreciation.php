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
        $fixedAssets = FixedAsset::selectRaw("fixed_assets.id,fixed_assets.value,residual_value,period,date_acquisition,SUM(d.accumulated) AS sum_accumulated")
            ->leftJoin('depreciations AS d', 'fixed_asset_id', 'fixed_assets.id')
            ->where('company_id', $company->id)
            ->where('is_depretation_a', true)
            ->where('period', '>', 0)
            ->groupBy('fixed_assets.id', 'fixed_assets.value', 'residual_value', 'period', 'date_acquisition')
            ->get();

        // Determinar el valor a Depreciar y Registrar
        foreach ($fixedAssets as $fixedAsset) {

            $base = $fixedAsset->value - $fixedAsset->residual_value;
            $baseMonths = $fixedAsset->period * 12;
            $valueDepreciation = $base / $baseMonths;

            if ($fixedAsset->sum_accumulated !== null) {
                $valueDepreciation = ($valueDepreciation / 30) * $this->calDays($fixedAsset->date_acquisition);
            }


            $data = [
                "company_id" => $company->id,
            ];
    
            
            $fixedAsset->depreciations()->create([
                'date' => $date,
                'percentage' => 20.0,
                'amount' => $base,
                'value' => $valueDepreciation,
                'accumulated' => $valueDepreciation + $fixedAsset->sum_accumulated,
                'data_additional' => $data,
            ]);
        }

        // De las Depreciaciones extraer agrupado por Tipo de Activo para Registrar el Asiento Contable
        $activeTypes = ActiveType::selectRaw('active_types.*,SUM(d.value) AS value_depreciation')
            ->join('fixed_assets AS fa', 'active_type_id', 'active_types.id')
            ->join('depreciations AS d', 'fixed_asset_id', 'fa.id')
            ->where("d.date", $date)
            ->groupBy('active_types.id')
            ->get();

        foreach ($activeTypes as $activeType) {
            $journal = $company->journals()->create([
                'date' => $date,
                'description' => 'Dep Mensual ' . $activeType->name,
                // NOTA preguntar si es deducible
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

    private function calDays($date)
    {
        $diasDelMesActual = Carbon::now()->daysInMonth;

        return $diasDelMesActual - $date->day;
    }
}
