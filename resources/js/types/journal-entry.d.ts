export interface JournalEntry {
    id?: number;
    journal_id?: number;
    account_id?: number;
    debit: number;
    have: number;
    data_additional?: string;
    processing?: boolean;
    [key: string]: any;
}