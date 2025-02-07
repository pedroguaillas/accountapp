<?php

namespace App\Http\Controllers;

use App\Models\MovementType;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;


class MovementTypeController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::first();

        // Construimos la consulta base
        $movementtypes = MovementType::query()
            ->where('company_id', $company->id);

        // Aplicamos el filtro si existe
        if ($request->has('search') && !empty($request->search)) {
            $movementtypes->where('name', 'LIKE', '%' . $request->search . '%');
        }

        // Paginamos los resultados y preservamos los filtros en la URL
        $movementtypes = $movementtypes->paginate(10)->withQueryString();

        // Renderizamos la vista con los datos necesarios
        return Inertia::render('MovementType/Index', [
            'filters' => [
                'search' => $search,
            ],
            'movementtypes' => $movementtypes,
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

        //creacion de los estableicmientos 
        $company->movementtypes()->create($request->all());

    }

    public function update(Request $request, MovementType $movementtype)
    {
        $request->validate([
            'name' => 'nullable|min:3|max:300',
        ]);

        //actulizar los establecimientos
        $movementtype->update($request->all());
    }

    public function destroy(MovementType $movementtype)
    {

        $movementtype->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Movimientos eliminado correctamente.',
        ]);
    }
}
