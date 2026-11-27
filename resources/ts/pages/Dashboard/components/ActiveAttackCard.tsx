import { Activity, Clock3 } from 'lucide-react';
import type { Attack } from '../../../types/attack';
import {dashboardLocale, dashboardT} from '../translate';

interface ActiveAttackCardProps {
    attack: Attack | null;
    onEndAttack?: (attack: Attack) => void;
    isEnding?: boolean;
    error?: string | null;
}

function formatDate(value: string | null): string {
    if (!value) {
        return '-';
    }

    return new Intl.DateTimeFormat(dashboardLocale(), {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
}

export default function ActiveAttackCard({
                                             attack,
                                             onEndAttack,
                                             isEnding = false,
                                             error,
                                         }: ActiveAttackCardProps) {
    return (
        <article className="migraine-dashboard__card">
            <div className="migraine-dashboard__card-header">
                <div>
                    <span className="migraine-dashboard__card-eyebrow">
                        {dashboardT('current', 'Current')}
                    </span>

                    <h2>{dashboardT('active_attack', 'Active attack')}</h2>
                </div>

                <Activity size={20} />
            </div>

            {attack ? (
                <div className="migraine-dashboard__active">
                    <div className="migraine-dashboard__active-row">
                        <Clock3 size={18} />

                        <div>
                            <span>{dashboardT('started', 'Started')}</span>

                            <strong>
                                {formatDate(attack.start_time)}
                            </strong>
                        </div>
                    </div>

                    <div className="migraine-dashboard__pain">
                        <span>{dashboardT('pain_level', 'Pain level')}</span>

                        <strong>
                            {attack.pain_level ?? '-'}

                            {attack.pain_level !== null && (
                                <small> {dashboardT('out_of_10', 'out of 10')}</small>
                            )}
                        </strong>
                    </div>

                    <button
                        type="button"
                        className="migraine-dashboard__secondary-action"
                        onClick={() => onEndAttack?.(attack)}
                        disabled={isEnding}
                    >
                        {isEnding
                            ? dashboardT('ending_attack', 'Ending…')
                            : dashboardT('end_attack', 'End attack')}
                    </button>
                    {error && <p className="migraine-dashboard__action-error" role="alert">{error}</p>}
                </div>
            ) : (
                <div className="migraine-dashboard__empty">
                    <span>{dashboardT('no_active_attack', 'No active attack.')}</span>
                </div>
            )}
        </article>
    );
}
