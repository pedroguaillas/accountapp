export interface FixedAsset {
    id?: number;
    company_id?: number;
    pay_method_id: number;
    is_depretation_a: boolean;
    is_legal: boolean;
    vaucher?: string;
    date_acquisition: string;
    detail: string;
    code: string;
    active_type_id: number;
    address: string;
    period: string|number;
    value: numer;
    residual_value: number;
    date_end: string;
    processing?: boolean;
    [key: string]: any;
}