export interface Iess {
    id?: number;
    company_id?: number;
    name: string;
    code: string;
    percentage: number;
    processing?: boolean;
    [key: string]: any;
}