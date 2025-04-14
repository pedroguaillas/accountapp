export interface Account {
    id?: number;
    company_id?: number;
    code: string;
    name: string;
    parent_id?: number;
    type?: string;
    is_active?: boolean;
    is_detail?: boolean;
    processing?: boolean;
    [key: string]: any;
}