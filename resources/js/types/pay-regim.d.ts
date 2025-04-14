export interface PayRegim {
    id?: number;
    company_id?: number;
    region:string;
    months_xiii?: string;
    months_xiv?: string;
    months_reserve_funds?: string;
    processing?: boolean;
    [key: string]: any;
}