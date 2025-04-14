export interface RoleEgress {
    id?: number;
    company_id?: number;
    account_departure_id?: number;
    account_counterpart_id?: number;
    name: string;
    type: string;
    processing?: boolean;
    [key: string]: any;
}