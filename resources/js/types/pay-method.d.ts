export interface PayMethod {
    id?: number;
    name: string;
    code: integer;
    max?: number;
    processing?: boolean;
    [key: string]: any;
}