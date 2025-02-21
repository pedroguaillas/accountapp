<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Models\Journal;
use App\Models\Company;
use App\Models\Account;
use App\Models\CostCenter;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $company = Company::first();

        // Si tiene solo un Asiento validar que este cuadrado los Activos Fijos
        $validateFixedAsset = true;
        $journalCount = Journal::where('company_id', $company->id)->count();

        if ($journalCount === 1) {
            $sumFixedAssetValue = FixedAsset::where('company_id', $company->id)->sum('value');
            $sumJournalItemDebit = JournalEntry::join('accounts AS a', 'a.id', 'account_id')
                ->where('a.code', 'LIKE', '1.2.1%')
                ->where('company_id', $company->id)->sum('debit');
            if ($sumFixedAssetValue < $sumJournalItemDebit) {
                $validateFixedAsset = false;
            }
        }

        $journals = Journal::with('journalentries')
            ->when($search, function ($query, $search) {
                $query->where('description', 'LIKE', "%$search%"); // Filtro de búsqueda
            })
            ->where('company_id', $company->id) // Filtrar por compañía
            ->paginate(10) // Paginación con 10 registros por página
            ->withQueryString() // Mantener la Query de search (paramenter)
            ->through(function ($journal) {
                return [
                    'id' => $journal->id,
                    'date' => $journal->date->format('d-m-Y'),
                    'description' => $journal->description,
                    'total' => $journal->journalentries->reduce(function ($sum, $entry) {
                        return $sum + $entry->debit; // Acumular los valores de debit
                    }, 0),
                    'journal_entries' => $journal->journalentries->map(function ($item) {
                        if ($item->account) { // Verifica si la cuenta está asociada
                            return [
                                'debit' => $item->debit ?? 0, // Valor predeterminado si es nulo
                                'have' => $item->have ?? 0,  // Valor predeterminado si es nulo
                                'code' => $item->account->code,
                                'name' => $item->account->name,
                            ];
                        }
                        return null; // Excluye items sin cuentas asociadas
                    })->filter(), // Filtra los elementos nulos
                ];
            });

        return inertia('Journal/Index', [
            'journals' => $journals,
            'validateFixedAsset' => $validateFixedAsset,
            'filters' => [
                'search' => $search
            ], // Mantener los filtros
        ]);
    }

    public function create()
    {
        $company = Company::first();

        $accounts = Account::select('id', 'code', 'name', 'is_detail')
            ->where('company_id', $company->id)
            ->where('is_detail', true) // Solo incluir cuentas donde is_detail es true
            ->get();
        
        if ($accounts->count()===0)
        {
            return to_route('accounts');
        }

        $costCenters = CostCenter::select('id', 'code', 'name')
            ->where('company_id', $company->id)->get();

        $countJournals = Journal::count();

        return Inertia::render('Journal/Create', [
            'accounts' => $accounts,
            'costCenters' => $costCenters,
            'countJournals' => $countJournals,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|min:1|max:300',
            'date' => 'required',
            'journalEntries.*.account_id' => 'required|min:1', // validar que exista esa cuenta 
            'journalEntries.*.debit' => 'required|min:0',
            'journalEntries.*.have' => 'required|min:0',
        ]);

        $company = Company::first();
        $user = auth()->user();

        $inputs = [
            ...$request->except('journalEntries'),
            'user_id' => $user->id, // Aquí se asigna el ID del usuario autenticado
        ];

        $journal = $company->journals()->create($inputs);

        $requestJournalEntries = $request->journalEntries;
        $journalEntries = [];

        foreach ($requestJournalEntries as $journalEntry) {
            $journalEntries[] = [
                'account_id' => $journalEntry['account_id'],
                'debit' => $journalEntry['debit'],
                'have' => $journalEntry['have'],
            ];
        }

        $journal->journalentries()->createMany($journalEntries);

        return to_route('journal.index');
    }

    public function edit(int $journalId)
    {
        // Obtén la información del asiento contable
        $company = Company::first();
        $journal = DB::table("journals")
            ->selectRaw("
            id,
            to_char(date, 'YYYY-MM-DD') as date,
            description,
            is_deductible,
            prefix,
            document_id,
            cost_center_id
        ")
            ->where('id', $journalId)
            ->whereNull('deleted_at')
            ->first();

        // Si el asiento no existe, redirige con un mensaje de error
        if (!$journal) {
            return redirect()->route('journal.index')->withErrors([
                'message' => 'El asiento contable no existe o fue eliminado.',
            ]);
        }

        // Obtén las entradas relacionadas con el asiento contable, excluyendo las eliminadas
        $journalEntries = DB::table("journal_entries")
            ->select(
                'id',
                'account_id',
                'debit',
                'have'
            )
            ->where('journal_id', $journalId)
            ->whereNull('deleted_at') // Excluir entradas eliminadas
            ->get();

        // Obtén todas las cuentas con sus detalles
        $accounts = Account::select('id', 'code', 'name', 'is_detail')
            ->where('company_id', $company->id)
            ->where('is_detail', true) // Solo incluir cuentas donde is_detail es true
            ->get();

        // Asocia la información de la cuenta a cada entrada del diario
        $journalEntries->each(function ($entry) use ($accounts) {
            $account = $accounts->get($entry->account_id); // Buscar la cuenta por su 'id'
            if ($account) {
                $entry->code = $account->code; // Asignar el código de la cuenta
                $entry->name = $account->name; // Asignar el nombre de la cuenta
            }
        });

        // Asocia las entradas al objeto principal
        $journal->journalEntries = $journalEntries;

        // Obtén los centros de costo disponibles
        $costCenters = CostCenter::select('id', 'code', 'name')
            ->where('company_id', Company::first()->id)
            ->get();

        // Renderiza la vista con los datos necesarios
        return Inertia::render('Journal/Edit', [
            'journal' => $journal,
            'costCenters' => $costCenters,
            'accounts' => $accounts
        ]);
    }

    public function update(Request $request, int $journal_id)
    {
        // Validación de los datos
        $request->validate([
            'description' => 'required|min:1|max:300',
            'date' => 'required',
            'journalEntries.*.account_id' => 'required|min:1', // Validar que exista esa cuenta
            'journalEntries.*.debit' => 'required|min:0',
            'journalEntries.*.have' => 'required|min:0',
        ]);

        $company = Company::first();
        $user = auth()->user();

        // Buscar el asiento contable a actualizar
        $journal = Journal::find($journal_id);

        // Si no se encuentra el asiento, redirigir con error
        if (!$journal) {
            return redirect()->route('journal.index')->withErrors([
                'message' => 'El asiento contable no existe o fue eliminado.',
            ]);
        }

        // Actualizar los detalles del asiento contable
        $journal->update([
            'description' => $request->description,
            'date' => $request->date,
            'user_id' => $user->id,
            // Aquí puedes añadir más campos que necesiten ser actualizados
        ]);

        // Eliminar las entradas anteriores del asiento
        $journal->journalentries()->delete();

        // Obtener las nuevas entradas del request
        $requestJournalEntries = $request->journalEntries;
        $journalEntries = [];

        // Preparar las nuevas entradas
        foreach ($requestJournalEntries as $journalEntry) {
            $journalEntries[] = [
                'account_id' => $journalEntry['account_id'],
                'debit' => $journalEntry['debit'],
                'have' => $journalEntry['have'],
            ];
        }

        // Guardar las nuevas entradas en el asiento
        $journal->journalentries()->createMany($journalEntries);

        // Redirigir con un mensaje de éxito
        return to_route('journal.index')->with('success', 'Asiento contable actualizado correctamente.');
    }

    public function destroy(Journal $journal)
    {
        $journal->delete(); // Esto usará SoftDeletes

        JournalEntry::where('journal_id', $journal->id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Asiento  contable eliminado correctamente.',
        ]);
    }
}