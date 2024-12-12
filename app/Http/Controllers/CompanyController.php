<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ContributorType;
use App\Models\EconomicActivity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        // Construimos la consulta base para las empresas
        $companies = Company::query()
            ->when($request->has('search') && !empty($request->search), function ($query) use ($request) {
                $query->where('ruc', 'LIKE', '%' . $request->search . '%'); // Filtrar por RUC
            });

        // Paginamos los resultados y preservamos los filtros en la URL
        $companies = $companies->paginate(10)->withQueryString();

        // Obtener actividades económicas y tipos de contribuyente
        $economyActivities = EconomicActivity::all();
        $contributorTypes = ContributorType::all();



        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Company/Index', [
            'companies' => $companies, // Datos de empresas paginados
            'filters' => $request->search, // Pasar el término de búsqueda como filtro
            'economyActivities' => $economyActivities, // Actividades económicas
            'contributorTypes' => $contributorTypes, // Tipos de contribuyente
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
        $company = Company::findOrFail($companyId);
        $company->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Compania eliminado correctamente.',
        ]);
    }

}
