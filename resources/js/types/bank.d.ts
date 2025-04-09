export interface ResponseBankProps {
    errors?: Errors;
    filters: Filters;
    bankPaginate: BankPaginate;
    [key: string]: any;  // Agregar un índice de tipo string
}

export interface BankPaginate {
    current_page: number;
    data: Bank[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: Link[];
    next_page_url: null;
    path: string;
    per_page: number;
    prev_page_url: null;
    to: number;
    total: number;
}

export interface Bank {
    id?: number;
    company_id?: number;
    name: string;
    description?: string;
    state?: boolean;
    processing?: boolean;
    [key: string]: any; 
}

export interface Link {
    url: null | string;
    label: string;
    active: boolean; 
}

export interface Errors {
    [key: string]: any;
}

export interface Filters {
    search: string;
}