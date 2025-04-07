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
        $search = $request->input('search', '');

        // Construimos la consulta base
        $people = Person::query()
            ->where('company_id', $company->id)
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            }); // No olvides llamar a get() para ejecutar la consulta

        // $additional_information = Person::select('additional_information')
        //     ->where('company_id', $company->id)
        //     ->when($search, function ($query, $search) {
        //         return $query->where('name', 'like', '%' . $search . '%');
        //     })
        //     ->latest()
        //     ->first(); // Recupera solo el último registro

        // // Verificar el contenido
        // // dd( $additional_information->additional_information); codificar a json
        // $json = json_encode($additional_information->additional_information, JSON_PRETTY_PRINT);

        // //  dd($json); convertir a objeto o array
        // $data = json_decode($json);

        //dd($data->edad);
        // Paginamos los resultados y preservamos los filtros en la URL
        $people = $people->paginate(10)->withQueryString();

        //dd($people);
        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Person/Index', [
            'filters' => [
                'search' => $request->search, // Retornar el filtro de búsqueda
            ],
            'people' => $people,
        ]);
    }

    public function getPeople(Request $request)
    {
        $company = Company::first();

        // Obtener el término de búsqueda si existe
        $search = $request->input('search');
        $paginate = $request->input('paginate', 10);
        // Obtener los datos con filtro y paginación
        $people = Person::where('company_id', $company->id)
            ->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('identification', 'LIKE', "%{$search}%");
            })
            ->paginate($paginate); // Cambia el número de elementos por página si es necesario

        return response()->json($people);
    }

    public function store(Request $request)
    {
        // Validación de datos
        $request->validate([
            'name' => 'nullable|min:3|max:300',
            'email' => 'required|email|unique:people,email',
            'identification' => 'required|string|unique:people,identification',
        ]);

        // Llamada a la compañía
        $company = Company::first();

        // Datos adicionales en formato JSON
        $additionalInfo = [
            'hobbies' => ['leer', 'programar', 'fútbol'],
            'edad' => 28,
            'estado_civil' => 'soltero',
            'redes_sociales' => [
                'twitter' => '@usuario',
                'facebook' => 'facebook.com/usuario'
            ]
        ];
        //$additionalInfo  = json_encode($additionalInfo , JSON_PRETTY_PRINT);

        // Creación de la persona
        $company->people()->create([
            'identification' => $request->identification,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'data_additional' => $additionalInfo, // Se pasa el JSON aquí
        ]);
    }

    public function update(Request $request, Person $person)
    {
        $request->validate([
            'name' => 'nullable|min:3|max:300',
            'email' => 'required|email|unique:people,email',
            'identification' => 'required|string|unique:people,identification',
        ]);
        //actulizar los establecimientos
        $person->update($request->all());
    }

    public function destroy(Person $person)
    {
        $person->delete(); // Esto usará SoftDeletes

        return redirect()->route('people.index');
    }
}