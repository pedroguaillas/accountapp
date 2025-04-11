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

//barril de los tipos de datos del modelo
export * from './active-type';
export * from './advance';
export * from './bank';
export * from './bank-account';
export * from './box';
export * from './branch';
export * from './cash';
export * from './company';
export * from './contributor-type';
export * from './economic-activity';
export * from './employee';
export * from './fixed-asset';
export * from './hour';
export * from './pay-method';
export * from './person';
