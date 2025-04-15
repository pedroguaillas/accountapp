export interface PaymentRol {
    id?: number;
    company_id?: number;
    employee_id?: number;
    position?: string;
    sector_code?: string;
    days?: number;
    salary?: number;
    salary_receive?: number;
    date?: string;
    state?: string;
    processing?: boolean;
    [key: string]: any;
}