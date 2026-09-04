export interface DashboardStatistics {
    period: string;
    total_attacks: number;
    active_attack_exists: boolean;
    average_pain_level: number | null;
    max_pain_level: number | null;
    attacks_this_week: number;
}