export interface Branch {
    id?: number;
    company_id?: number;
    name: string;
    number: number|string;
    city: string;
    phone: string;
    address: string;
    logo_path: string;
    is_matriz: boolean;
    enviroment_type: number|string;
    email: string;
    pass_email: string;
    state: boolean;
    processing?: boolean;
    [key: string]: any;
}