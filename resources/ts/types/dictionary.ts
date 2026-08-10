export interface DictionaryItem {
    id: number;
    code: string;
    name: string;
}

export interface UserDictionaryItem {
    id: number;
    name: string;
    description: string | null;
}