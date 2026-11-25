export interface DictionaryItem {
    id: number;
    code: string;
    name: string;
}

export interface UserDictionaryItem {
    id: number;
    name: string;
    description: string | null;
    dosage?: string | null;
}

export interface AttackDictionaries {
    symptoms: DictionaryItem[];
    user_symptoms: UserDictionaryItem[];
    triggers: DictionaryItem[];
    user_triggers: UserDictionaryItem[];
    meds: DictionaryItem[];
    user_meds: UserDictionaryItem[];
}
