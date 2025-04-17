export interface Cash {
    id?: number;
    box_id: number | string;
    close_employee_id?: number | string;
    initial_value: number | string;
    ingress: number | string;
    egress: number | string;
    balance: number;
    state_box: string;
    real_balance: number;
    cash_difference: number;
    processing?: boolean;
    [key: string]: any;
}