<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Box;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\MovementType;
use App\Models\CashSession;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BoxController extends Controller
{
    public function index()
    {
        $company = Company::first();

        $movements = MovementType::selectRaw(
            "movement_types.*, CONCAT(ap.code, ' - ', ap.name) AS ap_info"
        )
            ->leftJoin('accounts AS ap', 'movement_types.account_id', '=', 'ap.id')
            ->whereIn('movement_types.type', ["Ingreso", "Egreso"])
            ->where(function ($query) {
                $query->where('venue', 'ambos')
                    ->orWhere('venue', 'caja')
                    ->whereNotIn('movement_types.code', ['RCC', 'DCG', 'RFA', 'PF']);
            })
            ->orderBy('movement_types.id')
            ->get();

        // Filtrar ingresos y egresos después de la consulta
        $ingress = $movements->where('type', 'Ingreso');
        $egress = $movements->where('type', 'Egreso');

        $boxes = Box::selectRaw(
            "boxes.name ,boxes.id," .
            "CONCAT(ap.code, ' - ', ap.name) AS ap_info "
        )
            ->leftJoin('accounts AS ap', 'boxes.account_id', '=', 'ap.id')
            ->where('boxes.company_id', $company->id)
            ->get();

        $accounts = Account::select('id', 'code', 'name')
            ->where('is_detail', true)
            ->where('company_id', $company->id)
            ->orderBy("code")
            ->get();

        return Inertia::render('Business/AccountLink/Boxes/Index', [
            'accounts' => $accounts,
            'boxes' => $boxes,
            'ingres' => $ingress,
            'egres' => $egress,
        ]);
    }

    public function updateBoxAccount(Request $request, $id)
    {
        $box = $request->table === 'Box' ? Box::find($id) : MovementType::find($id);

        if ($box instanceof Box) {
            $cashgeneral = CashSession::where('box_id', $box->id)
                ->where('state_box', 'open')
                ->latest()
                ->first();
            if ($box->type === 'general' && !$cashgeneral) {
                $journal = Journal::where('description', 'ASIENTO DE SITUACION INICIAL')->first();

                if (!$journal) {
                    return redirect()->route('journal.create')->withErrors([
                        'warning' => 'Debes crear el asiento inicial antes de continuar.',
                    ]);
                }

                $valueBox = JournalEntry::where('journal_id', $journal->id)
                    ->where('account_id', $request->account_id)
                    ->first();

                if (!$valueBox) {
                    $box->cashSessions()->create([
                        'open_employee_id' => 1,//TODO revisar id
                        'close_employee_id' => null,
                        'initial_value' => 0,
                        'ingress' => 0,
                        'egress' => 0,
                        'balance' => 0,
                        'state_box' => 'open',//poner en espaniol
                    ]);
                } else {
                    $box->cashSessions()->create([
                        'open_employee_id' => 1,//TODO revisar id
                        'close_employee_id' => null,
                        'initial_value' => $valueBox->debit,
                        'ingress' => 0,
                        'egress' => 0,
                        'balance' => $valueBox->debit,
                        'state_box' => 'open',//poner en espaniol
                    ]);
                }

                $model = $request->table === 'Box' ? Box::find($id) : MovementType::find($id);

                $model->update(['account_id' => $request->account_id]);
            } else {
                if ($box->type === 'general') {
                    $cashgeneral->update([
                        'balance' => $cashgeneral->initial_value + $cashgeneral->ingress - $cashgeneral->egress
                    ]);
                }else
                {
                    $box->update(['account_id' => $request->account_id]);
                }
            }
        } else {
            

            $box->update(['account_id' => $request->account_id]);
        }

    }
}