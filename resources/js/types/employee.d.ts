export interface Employee {
    id?: number;
    company_id?: number;
    cuit: string;
    name: string;
    sector_code: string;
    position: string;
    days: number;
    salary: number;
    porcent_aportation?: number;
    is_a_parnert: boolean;
    is_a_qualified_craftsman: boolean;
    affiliated_with_spouse: boolean;
    xiii: boolean;
    xiv: boolean;
    reserve_funds: boolean;
    date_start: string;
    email?: string;
    processing: boolean;
    [key: string]: any;
}