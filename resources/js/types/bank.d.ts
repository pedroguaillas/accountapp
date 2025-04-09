export interface Bank {
    id?: number;
    company_id?: number;
    name: string;
    description?: string;
    state?: boolean;
    processing?: boolean;
    [key: string]: any;
}