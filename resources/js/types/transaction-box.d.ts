export interface TransactionBox {
    id?: number;
    cash_session_id: string;
    amount: string;
    movement_type_id: string;
    beneficiary_id?: string;
    description: string;
    data_additional?: string;
    box_id: string;
    document?: string;
    processing?: boolean;
    [key: string]: any;
}

