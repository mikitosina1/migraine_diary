import type { Attack } from '../../../types/attack';

interface RecentAttacksCardProps {
    attacks: Attack[];
    onSelectAttack?: (attack: Attack) => void;
}

function formatDate(value: string | null): string {
    if (!value) {
        return '—';
    }

    return new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
    }).format(new Date(value));
}

function formatTime(value: string | null): string {
    if (!value) {
        return '';
    }

    return new Intl.DateTimeFormat(undefined, {
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
                        History
                    </span>

                    <h2>Recent attacks</h2>
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
                                    : '—'}
                            </span>
                        </button>
                    ))
                ) : (
                    <div className="migraine-dashboard__empty">
                        <span>No recent attacks.</span>
                    </div>
                )}
            </div>
        </article>
    );
}
