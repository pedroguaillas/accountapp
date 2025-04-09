export interface BankAccount {
    id?: number;
    name: string;
    account_number: number;
    account_type: string;
    account_id?: number;
    current_balance: number;
    state?: boolean;
    processing?: boolean;
    data_additional?:any[];
    [key: string]: any;
}