export interface Person {
    id?: number;
    company_id?: number;
    identification?: string;
    name: string;
    email: string;
    phone: string;
    address?: string;
    data_additional?: any[];
    processing?: boolean;
    [key: string]: any;
}

export interface PeopleResponse {
    data: Person[];
    last_page: number;
    current_page: number;
    total: number;
  }