import type { DashboardStatistics as Statistics } from '../../../types/statistics';
import {dashboardT} from '../translate';

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
                    {dashboardT('total_attacks', 'Total attacks')}
                </span>

                <strong className="migraine-dashboard__stat-value">
                    {statistics.total_attacks}
                </strong>

                <span className="migraine-dashboard__stat-meta">
                    {dashboardT('dashboard_month', 'this month')}
                </span>
            </article>

            <article className="migraine-dashboard__stat">
                <span className="migraine-dashboard__stat-label">
                    {dashboardT('average_pain', 'Average pain')}
                </span>

                <strong className="migraine-dashboard__stat-value">
                    {statistics.average_pain_level ?? '-'}
                </strong>

                <span className="migraine-dashboard__stat-meta">
                    {dashboardT('out_of_10', 'out of 10')}
                </span>
            </article>

            <article className="migraine-dashboard__stat">
                <span className="migraine-dashboard__stat-label">
                    {dashboardT('this_week', 'This week')}
                </span>

                <strong className="migraine-dashboard__stat-value">
                    {statistics.attacks_this_week}
                </strong>

                <span className="migraine-dashboard__stat-meta">
                    {dashboardT('attacks', 'attacks')}
                </span>
            </article>

            <article className="migraine-dashboard__stat">
                <span className="migraine-dashboard__stat-label">
                    {dashboardT('maximum_pain', 'Maximum pain')}
                </span>

                <strong className="migraine-dashboard__stat-value">
                    {statistics.max_pain_level ?? '-'}
                </strong>

                <span className="migraine-dashboard__stat-meta">
                    {dashboardT('out_of_10', 'out of 10')}
                </span>
            </article>
        </div>
    );
}
