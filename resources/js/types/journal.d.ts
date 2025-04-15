import { JournalEntry } from "./journal-entry";

export interface Journal {
    id?: number;
    date: string;
    reference: string;
    is_deductible: boolean;
    prefix?: string;
    document_id?: string;
    table?: string;
    user_id?: number;
    cost_center_id?: number|string;
    processing?: boolean;
    journalEntries: JournalEntry[];
    [key: string]: any;
}