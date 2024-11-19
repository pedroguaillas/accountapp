<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Company;
use App\Models\Account;
use App\Models\CostCenter;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JournalController extends Controller
{
    public function index()
    {
        $company = Company::first();
        // Subconsulta para journals
        $journal = DB::table("journals")->select('id', 'date', 'description')
            ->where(
                'company_id',
                $company->id
            );

        // Consulta principal
        $journalsRaw = DB::table('journal_entries')
            ->select(
                'journals_sub.id as idjournal',
                'journals_sub.date',
                'journals_sub.description',
                'journal_entries.id as entry_id',
                'accounts.code',
                'accounts.name',
                'journal_entries.debit',
                'journal_entries.have'
            )
            ->join('accounts', 'accounts.id', '=', 'journal_entries.account_id') // Relación con accounts
            ->joinSub($journal, 'journals_sub', function ($join) {
                $join->on('journal_entries.journal_id', '=', 'journals_sub.id'); // Relación entre journal_entries y journals
            })
            ->orderBy('journals_sub.id', 'asc')
            ->orderBy('journal_entries.id', 'asc')
            ->get();

        // Agrupar los resultados por journal
        $journals = $journalsRaw->groupBy('idjournal')->map(function ($entries, $idjournal) {
            $journalData = $entries->first(); // Obtener la primera entrada del grupo
            return [
                'id' => $idjournal,
                'date' => $journalData->date,
                'description' => $journalData->description,
                'journal_entries' => $entries->map(function ($entry) {
                    return [
                        'id' => $entry->entry_id,
                        'code' => $entry->code,
                        'name' => $entry->name,
                        'debit' => $entry->debit,
                        'have' => $entry->have,
                    ];
                })->values(), // Asegurarse de que las claves sean numéricas
            ];
        })->values(); // Eliminar las claves generadas por groupBy

     
        // Retornar a la vista con los datos procesados
        return Inertia::render('Journal/Index', [
            'journals' => $journals,
        ]);
    }



    public function create()
    {
        $company = Company::first();
        $accounts = Account::select('id', 'code', 'name')->where('company_id', $company->id)->get();
        $costCenters = CostCenter::select('id', 'code', 'name')->where('company_id', $company->id)->get();


        return Inertia::render('Journal/Create', [
            'accounts' => $accounts,
            'costCenters' => $costCenters,

        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|min:1|max:999',
            'journalEntries.*.account_id' => 'required|min:1', // validar que exista esa cuenta 
            'journalEntries.*.debit' => 'required|min:0',
            'journalEntries.*.have' => 'required|min:0',
            //'is_deductible'=> '',
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

        // return Inertia::render('journal.index');

        return to_route('journal.index');

    }
    public function delete(Request $request, Journal $journal)
    {

        $journal->delete();
    }
}
