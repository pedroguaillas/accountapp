export interface Depresation {
    id?: number;
    fixed_asset_id?: number;
    depreciation_time: string;
    date?: string;
    percentaje: number;
    amount:number; 
    value: number;
    accumulates: number;
    data_additional?: string;
    processing?: boolean;
    [key: string]: any;
}