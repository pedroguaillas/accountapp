export interface CostCenter {
    id?: number;
    company_id?: number;
    name: string;
    parent_id?: number;
    state?: boolean;
    processing?: boolean;
    [key: string]: any;
}