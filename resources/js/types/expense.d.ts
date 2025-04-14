export interface Expense {
    id?: number;
    company_id?: number;
    name: string;
    account_id?: number;
    processing?: boolean;
    [key: string]: any;
}