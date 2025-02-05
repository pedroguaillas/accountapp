<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transactions';

    protected $fillable = [
        'movement_type_id',
        'bank_account_id',
        'company_id',
        'transaction_date',
        'amount',
        'description',
        'payment_method',
        'beneficiary_id',
        'cheque_date',
        'transfer_account',
        'voucher_number',
        'state_transaction',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'cheque_date' => 'date',
    ];

    // Relación con la tabla MovementType
    public function movementType()
    {
        return $this->belongsTo(MovementType::class, 'movement_type_id');
    }

    // Relación con la tabla BankAccount
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    // Relación con la tabla Company
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
