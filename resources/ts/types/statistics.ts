export interface DashboardStatistics {
    period: string;
    total_attacks: number;
    active_attack_exists: boolean;
    average_pain_level: number | null;
    max_pain_level: number | null;
    attacks_this_week: number;
}

export interface PeriodStatistics {
    filters: { range: 'month' | '3months' | 'year'; pain_level: string };
    summary: {
        period: string;
        total_attacks: number;
        active_attack_exists: boolean;
        average_pain_level: number | null;
        max_pain_level: number | null;
        attacks_this_week: number;
    };
    chart: Array<{ name: string; count: number; dates: Array<{ date: string; pain_level: number }> }>;
}
