import type {DictionaryItem, UserDictionaryItem,} from './dictionary';

export interface AttackMedication extends DictionaryItem {
    dosage?: string | null;
}

export interface Attack {
    id: number;
    start_time: string | null;
    end_time: string | null;
    pain_level: number | null;
    notes: string | null;
    is_active: boolean;

    symptoms: DictionaryItem[];
    user_symptoms: UserDictionaryItem[];

    triggers: DictionaryItem[];
    user_triggers: UserDictionaryItem[];

    meds: AttackMedication[];
    user_meds: UserDictionaryItem[];

    created_at: string | null;
    updated_at: string | null;
}
