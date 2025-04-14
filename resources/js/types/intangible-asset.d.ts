export interface IntangibleAsset {
    id?: number;
    company_id?: number;
    pay_method_id: number;
    is_legal: boolean;
    vaucher: string;
    date_acquisition: string;
    detail: string;
    code: string;
    type: string;
    period: string|number;
    value: numer|string;
    date_end: string;
    processing?: boolean;
    [key: string]: any;
}