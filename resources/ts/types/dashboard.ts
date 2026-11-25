import type {Attack} from './attack';
import type {DashboardStatistics} from './statistics';
import type {AttackDictionaries} from './dictionary';

export interface Dashboard {
    active_attack: Attack | null;
    recent_attacks: Attack[];

    dictionaries: AttackDictionaries;

    statistics: DashboardStatistics;

    meta: {
        locale: string;
    };
}
