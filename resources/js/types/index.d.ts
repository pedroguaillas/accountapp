import { Config } from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
};

//import general (request)
export * from './general-request';
export * from './user';

//barril de los tipos de datos del modelo
export * from './account';
export * from './active-type';
export * from './advance';
export * from './bank';
export * from './bank-account';
export * from './box';
export * from './branch';
export * from './cash';
export * from './company';
export * from './contributor-type';
export * from './cost-center';
export * from './depresation';
export * from './economic-activity';
export * from './employee';
export * from './expense';
export * from './fixed-asset';
export * from './ice';
export * from './iess';
export * from './iva';
export * from './hour';
export * from './journal';
export * from './journal-entry';
export * from './movement-type';
export * from './pay-method';
export * from './pay-regim';
export * from './pay-rol';
export * from './person';
export * from './role-egress';
export * from './role-ingress';
export * from './transaction-box';
export * from './transaction-bank';
export * from './transaction-expense';
export * from './withholding';
