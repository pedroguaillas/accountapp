<?php

namespace App\Http\Controllers;

use App\Http\Requests\FixedAssetStoreRequest;
use App\Http\Requests\FixedAssetUpdateRequest;
use App\Models\Company;
use App\Models\FixedAsset;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\PayMethod;
use App\Models\ActiveType;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class FixedAssetController extends Controller
{
    public function index(Request $request)
    {
        // Obtener la compañía actual
        $company = Company::first();

        // Consulta para activos fijos con filtros opcionales
        $fixedAssetssQuery = DB::table('fixed_assets')
            ->selectRaw("id, code, to_char(date_acquisition, 'DD-MM-YYYY') as date_acquisition, detail, value")
            ->where('company_id', $company->id)
            ->whereNull('deleted_at'); // Excluir registros eliminados

        // Aplicar filtro por código si existe
        if ($request->search) {
            $fixedAssetssQuery->where('code', 'LIKE', '%' . $request->search . '%');
        }

        // Aplicar paginación a activos fijos
        $fixedAssetss = $fixedAssetssQuery->paginate(10)->withQueryString();

        // Retornar la vista con los datos y filtros actuales
        return Inertia::render('FixedAsset/Index', [
            'fixedAssetss' => $fixedAssetss,
            'filters' => [
                'search' => $request->search, // Retornar el filtro de código
            ],
        ]);
    }

    public function create()
    {
        $company = Company::first();
        if (!$company) {
            return redirect()->back()->with('error', 'No company found.');
        }

        $payMethods = PayMethod::all();

        // Base query for ActiveType
        $activeTypeQueryAll = ActiveType::select('active_types.*');

        // Check the journal count
        $journalCount = Journal::where('company_id', $company->id)->count();

        $activeTypes = collect(); // Initialize empty collection

        if ($journalCount === 1) {
            // Build query with joins and conditions
            $activeTypeQuery = $activeTypeQueryAll->where('company_id', $company->id)
                ->join('accounts AS a', 'a.id', '=', 'active_types.account_id')
                ->join('journal_entries AS je', 'a.id', '=', 'je.account_id')
                ->groupBy('active_types.id', 'active_types.name')
                ->havingRaw("(SELECT COALESCE(SUM(value), 0) FROM fixed_assets WHERE active_types.id = active_type_id) < COALESCE(SUM(je.debit), 0)");

            // Fetch results with join
            $activeTypes = $activeTypeQuery->get();
        }

        // Fallback: if $activeTypes is empty, fetch only base ActiveType data
        if ($activeTypes->isEmpty()) {
            $activeTypes = ActiveType::all();
        }

        return Inertia::render('FixedAsset/Create', [
            'payMethods' => $payMethods,
            'activeTypes' => $activeTypes,
        ]);
    }

    public function store(FixedAssetStoreRequest $fixedAssetStoreRequest)
    {
        $company = Company::first(); // Asegúrate de tener la empresa disponible

        // Registro del Activo Fijo
        $fixedAsset = $company->fixedassets()->create($fixedAssetStoreRequest->all());

        // Siempre journalCount > 0 porque registra antes
        $sumFixedAssetValue = FixedAsset::where('company_id', $company->id)->sum('value');
        $sumJournalItemDebit = JournalEntry::join('accounts AS a', 'a.id', 'account_id')
            ->where('a.code', 'LIKE', '1.2.1%')
            ->where('company_id', $company->id)->sum('debit');

        // En este caso se genera el Asiento Contable
        if ($sumFixedAssetValue > $sumJournalItemDebit) {
            $journal = $company->journals()->create([
                'date' => Carbon::now()->toDateString(),
                'description' => 'Compra de ' . $fixedAssetStoreRequest->detail,
                'is_deductible' => true,
                'document_id' => $fixedAsset->id,
                'table' => $fixedAsset->getTable(),
                'user_id' => auth()->user()->id,
            ]);

            $activeType = ActiveType::find($fixedAssetStoreRequest->active_type_id);
            $journal->journalentries()->createMany([
                [
                    'account_id' => $activeType->account_id,
                    'debit' => $fixedAssetStoreRequest->value,
                    'have' => 0,
                ],
                [
                    // FALTA: analizar el vinculo de las Cuentas Por Pagar
                    'account_id' => 58,
                    'debit' => 0,
                    'have' => $fixedAssetStoreRequest->value,
                ],
            ]);
        }

        return to_route('fixedassets.index');
    }

    public function edit(int $fixedAsset_id)
    {
        $fixedAsset = DB::table("fixed_assets")
            ->selectRaw("
            id, 
            pay_method_id,
            is_depretation_a, 
            is_legal, 
            vaucher, 
            to_char(date_acquisition, 'YYYY-MM-DD') as date_acquisition, 
            detail, 
            code, 
            active_type_id, 
            address, 
            period, 
            CAST(value AS FLOAT) as value, 
            CAST(residual_value AS FLOAT) as residual_value, 
            to_char(date_end, 'YYYY-MM-DD') as date_end
        ")
            ->where('id', $fixedAsset_id)
            ->first();

        $payMethods = PayMethod::all();
        $activeTypes = ActiveType::all();

        return Inertia::render('FixedAsset/Edit', [
            'fixedAsset' => $fixedAsset,
            'payMethods' => $payMethods,
            'activeTypes' => $activeTypes,
        ]);
    }

    public function update(FixedAssetUpdateRequest $fixedAssetUpdateRequest, FixedAsset $fixedAsset)
    {
        // Actualización del activo fijo con los datos validados
        $fixedAsset->update($fixedAssetUpdateRequest->all());
        // Redirigir a la vista de listado de activos fijos
        return to_route('fixedassets.index')->with('success', 'Activo fijo actualizado exitosamente.');
    }

    public function destroy(FixedAsset $fixedAsset)
    {
        $fixedAsset->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Activo fijo eliminado correctamente.',
        ]);
    }
}
