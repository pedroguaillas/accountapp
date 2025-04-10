export interface Person {
    id?: number;
    company_id?: number;
    identification?: number;
    name: string;
    email: string;
    phone: string;
    adress: string;
    data_additional?: any[];
    processing?: boolean;
    [key: string]: any;
}