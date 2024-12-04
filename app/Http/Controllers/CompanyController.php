<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ContributorType;
use App\Models\EconomicActivity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el término de búsqueda desde la solicitud
        $search = $request->input('search', ''); // Valor predeterminado vacío si no hay búsqueda

        // Consultar las empresas y aplicar filtro si hay término de búsqueda
        $companies = Company::query()
            ->when($search, function ($query, $search) {
                $query->where('ruc', 'like', '%' . $search . '%'); // Filtrar por RUC
            })
            ->get(); // Puedes usar paginate() si necesitas paginación

        // Obtener actividades económicas y tipos de contribuyente
        $economyActivities = EconomicActivity::all();
        $contributorTypes = ContributorType::all();

        // Retornar datos a la vista
        return Inertia::render('Company/Index', [
            'companies' => $companies,
            'economyActivities' => $economyActivities,
            'contributorTypes' => $contributorTypes,
            'search' => $search, // Pasar el término de búsqueda a la vista (opcional para el frontend)
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'ruc' => 'required|min:13|max:13|unique:companies,ruc', // Verificar unicidad en la tabla 'companies'
            'company' => 'required|min:3|max:300',
            'economic_activity_id' => 'required',
            'contributor_type_id' => 'required',
        ]);
    
        Company::create($request->all());
    }
    
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'ruc' => [
                'required',
                'min:13',
                'max:13',
                // Verificar unicidad, pero excluir el registro actual del RUC
                Rule::unique('companies', 'ruc')->ignore($company->id),
            ],
            'company' => 'required|min:3|max:300',
            'economic_activity_id' => 'required',
            'contributor_type_id' => 'required',
        ]);
    
        $company->update($request->all());
    }

    public function destroy(int $companyId)
    {
        $company= Company::findOrFail($companyId);
        $company->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Compania eliminado correctamente.',
        ]);
    }
    
}
