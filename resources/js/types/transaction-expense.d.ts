export interface TransactionExpense {
    id?: number;
    expense_id?: number;
    payment_type: string;
    payment_method_id: string;
    amount: string|number;
    description: string;
    data_additional?: string;
    date_expense?: string;
    document?: string;
    processing?: boolean;
    [key: string]: any;

}

