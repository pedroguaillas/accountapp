export interface Hour {
    id?: number;
    company_id?: number;
    detail: string;
    employee_id: number;
    amount: number | string;
    type: string;
    date: string;
    processing?: boolean;
    [key: string]: any;
}