export interface TransactionBank {
    id?: number;
    company_id?: number;
    movement_type_id: string;
    bank_account_id: string;
    transaction_date?: string;
    amount: string;
    description: string;
    payment_method: string;
    beneficiary_id?: string;
    cheque_date?: string|undefined;
    transfer_account?:string;
    voucher_number?: string;
    state_transaction?: string;
    data_additional?: string;
    processing?: boolean;
    [key: string]: any;
}

