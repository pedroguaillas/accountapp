export interface Advance {
    id?: number;
    company_id?: number;
    detail: string;
    payment_type: string;
    receipt_number?: string;
    payment_method_id: number;
    movement_type_id: number;
    employee_id: number;
    amount: number|string;
    date: string;
    processing?: boolean;
    [key: string]: any;
}