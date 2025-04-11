export interface Company {
    id?: number;
    ruc: string;
    company: string;
    economic_activity_id: string | number;
    contributor_type_id: string | number;
    restart_activities?: string;
    special?: string;
    accounting?: boolean;
    retention_agent?: number;
    declaration_type?: string;
    certificate_path?: string;
    certificate_pass?: string;
    sign_valid_from?: string;
    sign_valid_to?: string;
    date: string;
    processing?: boolean;
    [key: string]: any;
}