export interface Cash {
    id?: number;
    box_id: number;
    close_employee_id:number;
    initial_value: number;
    ingress:number;
    egress:number;
    balance: number;
    state_box: string;
    real_balance: number;
    cash_difference: number;
    processing?: boolean;
    [key: string]: any;
}