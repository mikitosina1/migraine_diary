import type { Attack } from '../../../types/attack';
import {dashboardLocale, dashboardT} from '../translate';

interface RecentAttacksCardProps {
    attacks: Attack[];
    onSelectAttack?: (attack: Attack) => void;
}

function formatDate(value: string | null): string {
    if (!value) {
        return '-';
    }

    return new Intl.DateTimeFormat(dashboardLocale(), {
        dateStyle: 'medium',
    }).format(new Date(value));
}

function formatTime(value: string | null): string {
    if (!value) {
        return '';
    }

    return new Intl.DateTimeFormat(dashboardLocale(), {
        timeStyle: 'short',
    }).format(new Date(value));
}

export default function RecentAttacksCard({
                                              attacks,
                                              onSelectAttack,
                                          }: RecentAttacksCardProps) {
    return (
        <article className="migraine-dashboard__card">
            <div className="migraine-dashboard__card-header">
                <div>
                    <span className="migraine-dashboard__card-eyebrow">
                        {dashboardT('history', 'History')}
                    </span>

                    <h2>{dashboardT('recent_attacks', 'Recent attacks')}</h2>
                </div>
            </div>

            <div className="migraine-dashboard__attacks">
                {attacks.length > 0 ? (
                    attacks.map((attack) => (
                        <button
                            key={attack.id}
                            type="button"
                            className="migraine-dashboard__attack"
                            onClick={() => onSelectAttack?.(attack)}
                        >
                            <div>
                                <strong>
                                    {formatDate(attack.start_time)}
                                </strong>

                                <span>
                                    {formatTime(attack.start_time)}
                                </span>
                            </div>

                            <span className="migraine-dashboard__attack-pain">
                                {attack.pain_level !== null
                                    ? `${attack.pain_level}/10`
                                    : '-'}
                            </span>
                        </button>
                    ))
                ) : (
                    <div className="migraine-dashboard__empty">
                        <span>{dashboardT('no_recent_attacks', 'No recent attacks.')}</span>
                    </div>
                )}
            </div>
        </article>
    );
}
