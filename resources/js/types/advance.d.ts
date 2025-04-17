export interface Advance {
    id?: number;
    company_id?: number;
    detail: string;
    payment_type: string;
    receipt_number?: string;
    payment_method_id: number | string;
    movement_type_id: number | string;
    employee_id: number;
    amount: number|string;
    date: string;
    processing?: boolean;
    [key: string]: any;
}