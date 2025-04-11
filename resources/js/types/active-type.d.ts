export interface ActiveType {
    id?: number;
    company_id?: number;
    name: string;
    depreciation_time: string;
    account_id?: number;
    account_dep_id?: number;
    account_dep_spent_id?: number;
    processing?: boolean;
    [key: string]: any;
}