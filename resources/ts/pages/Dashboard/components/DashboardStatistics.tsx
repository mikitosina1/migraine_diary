import type { DashboardStatistics as Statistics } from '../../../types/statistics';

interface DashboardStatisticsProps {
    statistics: Statistics;
}

export default function DashboardStatistics({
                                                statistics,
                                            }: DashboardStatisticsProps) {
    return (
        <div className="migraine-dashboard__stats">
            <article className="migraine-dashboard__stat">
                <span className="migraine-dashboard__stat-label">
                    Attacks
                </span>

                <strong className="migraine-dashboard__stat-value">
                    {statistics.total_attacks}
                </strong>

                <span className="migraine-dashboard__stat-meta">
                    month
                </span>
            </article>

            <article className="migraine-dashboard__stat">
                <span className="migraine-dashboard__stat-label">
                    Average pain
                </span>

                <strong className="migraine-dashboard__stat-value">
                    {statistics.average_pain_level ?? '—'}
                </strong>

                <span className="migraine-dashboard__stat-meta">
                    out of 10
                </span>
            </article>

            <article className="migraine-dashboard__stat">
                <span className="migraine-dashboard__stat-label">
                    This week
                </span>

                <strong className="migraine-dashboard__stat-value">
                    {statistics.attacks_this_week}
                </strong>

                <span className="migraine-dashboard__stat-meta">
                    attacks
                </span>
            </article>

            <article className="migraine-dashboard__stat">
                <span className="migraine-dashboard__stat-label">
                    Maximum pain
                </span>

                <strong className="migraine-dashboard__stat-value">
                    {statistics.max_pain_level ?? '—'}
                </strong>

                <span className="migraine-dashboard__stat-meta">
                    out of 10
                </span>
            </article>
        </div>
    );
}
