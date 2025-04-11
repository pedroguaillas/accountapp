export interface BankAccount {
    id?: number;
    account_number: string;
    account_type: string;
    account_id?: number;
    current_balance: number;
    state: boolean;
    processing?: boolean;
    data_additional?:any[];
    [key: string]: any;
}