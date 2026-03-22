export type PriceItemCategory =
    | 'Paznokcie'
    | 'Przedłużanie'
    | 'Korekta paznokci (do 4 tygodni)'
    | 'Uzupełnienie'
    | 'Zdobienia';

export type PriceItem = {
    id: number;
    category: PriceItemCategory;
    name: string;
    price_min: number;
    price_max: number | null;
    formatted_price: string; //accessor
    created_at: string;
    updated_at: string;
};
