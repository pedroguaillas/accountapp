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
        $payMethods = PayMethod::all();
        $activeTypes = ActiveType::all();

        return Inertia::render('FixedAsset/Create', [
            'payMethods' => $payMethods,
            'activeTypes' => $activeTypes,
        ]);
    }

    public function store(FixedAssetStoreRequest $fixedAssetStoreRequest)
    {
        $company = Company::first(); // Asegúrate de tener la empresa disponible

        if (Journal::count() === 1) {

            // Sumar los valores de los activos fijos
            $fixedAsset = FixedAsset::selectRaw('a.id, SUM(value) AS sumAmount')
                ->join('accounts AS a', 'a.id', 'account_id')
                ->groupBy('id')->get();

            // Sumar los activos fijos del ASIENTO DE SITUACION INICIAL
            $journalEntryInitial = JournalEntry::selectRaw('a.id, SUM(debit) AS debit, SUM(have) AS have')
                ->join('accounts AS a', 'a.id', 'account_id')
                ->where('a.name', 'LIKE', "ASIENTO DE SITUACION INICIAL")
                ->groupBy('id')->get();
        }
        // En caso que este registrado solo el ASIENTO DE SITUACION INICIAL
        // Y
        // La suma de los activos fijos sean <= que la suma de los activos fijos del ASIENTO DE SITUACION INICIAL
        // Registrar solo el activo fijo

        $company->fixedassets()->create($fixedAssetStoreRequest->all());
        // caso contrario generar ademas el asiento

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
