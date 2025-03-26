<?php

namespace App\Services;

use App\Models\{Company, Journal, Box, MovementType, TransactionBox, CashSession};
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionService
{
    public function storeTransaction($transactionStoreBoxRequest)
    {
        $company = Company::first();
        $journal = Journal::where('description', 'ASIENTO DE SITUACION INICIAL')
            ->where('company_id', $company->id)
            ->first();

        if (!$journal) {
            return redirect()->route('journal.create')->withErrors([
                'warning' => 'Debes crear el asiento inicial antes de continuar.',
            ]);
        }

        $this->validateAccounts($company);

        $user = Auth::user();
        $date = Carbon::now();
        $data = ["company_id" => $company->id];

        $transaction = TransactionBox::create([...$transactionStoreBoxRequest->all(), 'data_additional' => $data]);


        $journalEntries = $this->processTransaction($transaction);

        $cash = CashSession::find($transaction->cash_session_id);
        $cash->update([
            'balance' => $cash->initial_value + $cash->ingress - $cash->egress
        ]);

        $journal = $company->journals()->create([
            'description' => "Movimiento Caja " . $transaction->description,
            'date' => $date,
            'user_id' => $user->id,
            'document_id' => $transaction->id,
            'table' => 'transaction_boxes',
        ]);

        $journal->journalentries()->createMany($journalEntries);
    }

    private function validateAccounts($company)
    {
        $boxAccountValidate = Box::whereNull('account_id')
            ->where('boxes.company_id', $company->id)
            ->exists();

        if ($boxAccountValidate) {
            return redirect()->route('setting.account.box.index')->withErrors([
                'warning' => 'Debes vincular las cuentas antes de continuar.',
            ]);
        }

        $movementTypeValidate = MovementType::whereNull('account_id')
            ->where(fn($query) => $query->where('venue', 'ambos')->orWhere('venue', 'caja'))
            ->whereNotIn('code', ['RCC', 'PF', 'DCG', 'RFA'])
            ->exists();

        if ($movementTypeValidate) {
            return redirect()->route('setting.account.box.index')->withErrors([
                'warning' => 'Debes vincular las cuentas antes de continuar.',
            ]);
        }
    }

    private function processTransaction($transaction)
    {
        $journalEntries = [];
        $movementType = MovementType::find($transaction->movement_type_id);
        $cash = CashSession::find($transaction->cash_session_id);
        $box = Box::find($cash->box_id);

        if ($box->type === 'general') {
            $this->handleGeneralBoxTransaction($transaction, $cash, $box, $movementType, $journalEntries);
        } elseif ($box->type === 'chica') {
            $this->handleChicaBoxTransaction($transaction, $cash, $box, $movementType, $journalEntries);
        } elseif ($box->type === 'otras') {
            $this->handleOtrasBoxTransaction($transaction, $cash, $box, $movementType, $journalEntries);
        }
        return $journalEntries;
    }

    private function handleGeneralBoxTransaction($transaction, $cash, $box, $movementType, &$journalEntries)
    {
        if ($movementType->type === 'Ingreso') {
            $cash->increment('ingress', $transaction->amount);
            $journalEntries[] = ['account_id' => $box->account_id, 'debit' => $transaction->amount, 'have' => 0];
            $journalEntries[] = ['account_id' => $movementType->account_id, 'debit' => 0, 'have' => $transaction->amount];
        } elseif ($movementType->type === 'Egreso' && !in_array($movementType->code, ['RCC', 'PF'])) {
            $cash->increment('egress', $transaction->amount);
            $journalEntries[] = ['account_id' => $movementType->account_id, 'debit' => $transaction->amount, 'have' => 0];
            $journalEntries[] = ['account_id' => $box->account_id, 'debit' => 0, 'have' => $transaction->amount];
        } else {
            if (in_array($movementType->code, ['RCC', 'PF'])) {
                $cash->increment('egress', $transaction->amount);
                $this->transferFunds($transaction, $box, $journalEntries);
            }
        }
    }

    private function handleChicaBoxTransaction($transaction, $cash, $box, $movementType, &$journalEntries)
    {
        if ($movementType->code === 'RFA') {
            $cash->increment('egress', $transaction->amount);
            $this->transferFunds($transaction, $box, $journalEntries);
        }
    }

    private function handleOtrasBoxTransaction($transaction, $cash, $box, $movementType, &$journalEntries)
    {
        if ($movementType->code === 'DCG') {
            $cash->increment('egress', $transaction->amount);
            $this->transferFunds($transaction, $box, $journalEntries);
        }
    }

    private function transferFunds($transaction, $box, &$journalEntries)
    {
        $boxAux = Box::find($transaction->box_id);
        $cashSessionAux = CashSession::where('box_id', $boxAux->id)->latest()->first();
        $cashSessionAux->increment('ingress', $transaction->amount);
        $cashSessionAux->update(['balance' => $cashSessionAux->initial_value + $cashSessionAux->ingress - $cashSessionAux->egress]);

        $journalEntries[] = ['account_id' => $boxAux->account_id, 'debit' => $transaction->amount, 'have' => 0];
        $journalEntries[] = ['account_id' => $box->account_id, 'debit' => 0, 'have' => $transaction->amount];
    }
}