export interface MovementType {
    id?: number;
    company_id?: number;
    account_id?: number;
    name: string;
    code: string;
    type: string;
    venue: string;
    processing?: boolean;
    [key: string]: any;
}