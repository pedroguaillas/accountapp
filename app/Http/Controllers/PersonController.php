<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;


class PersonController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::first();

        // Construimos la consulta base
        $people = Person::query()
            ->where('company_id', $company->id);

        // Aplicamos el filtro si existe
        if ($request->has('search') && !empty($request->search)) {
            $people->where('name', 'LIKE', '%' . $request->search . '%');
        }

        // Paginamos los resultados y preservamos los filtros en la URL
        $people = $people->paginate(10)->withQueryString();



        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Person/Index', [
            'filters' => $request->search,
            'people' => $people,
        ]);
    }


    public function store(Request $request)
    {
        //validacion de datos
        $request->validate([
            'name' => 'nullable|min:3|max:300',
        ]);
        //llamada a la compania
        $company = Company::first();

        //creacion de las personas
        $company->people()->create($request->all());

    }

    public function update(Request $request, Person $person)
    {
        $request->validate([
            'name' => 'nullable|min:3|max:300',
        ]);

        //actulizar los establecimientos
        $person->update($request->all());
    }

    public function destroy(Person $person)
    {
        $person->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Persona eliminada correctamente.',
        ]);
    }

}
