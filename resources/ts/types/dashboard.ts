import type { Attack } from './attack';
import type { DashboardStatistics } from './statistics';

export interface Dashboard {
    active_attack: Attack | null;
    recent_attacks: Attack[];

    dictionaries: {
        symptoms: unknown[];
        user_symptoms: unknown[];
        triggers: unknown[];
        user_triggers: unknown[];
        meds: unknown[];
        user_meds: unknown[];
    };

    statistics: DashboardStatistics;

    meta: {
        locale: string;
    };
}