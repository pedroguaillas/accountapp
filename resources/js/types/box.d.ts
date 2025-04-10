export interface Box {
    id?: number;
    company_id?: number;
    name: string;
    owner_id: number;
    type: string;
    account_id?: number;
    processing?: boolean;
    [key: string]: any;
}