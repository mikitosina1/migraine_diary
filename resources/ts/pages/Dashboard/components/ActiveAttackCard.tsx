import { Activity, Clock3 } from 'lucide-react';
import type { Attack } from '../../../types/attack';

interface ActiveAttackCardProps {
    attack: Attack | null;
    onEndAttack?: (attack: Attack) => void;
}

function formatDate(value: string | null): string {
    if (!value) {
        return '—';
    }

    return new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
}

export default function ActiveAttackCard({
                                             attack,
                                             onEndAttack,
                                         }: ActiveAttackCardProps) {
    return (
        <article className="migraine-dashboard__card">
            <div className="migraine-dashboard__card-header">
                <div>
                    <span className="migraine-dashboard__card-eyebrow">
                        Current
                    </span>

                    <h2>Active attack</h2>
                </div>

                <Activity size={20} />
            </div>

            {attack ? (
                <div className="migraine-dashboard__active">
                    <div className="migraine-dashboard__active-row">
                        <Clock3 size={18} />

                        <div>
                            <span>Started</span>

                            <strong>
                                {formatDate(attack.start_time)}
                            </strong>
                        </div>
                    </div>

                    <div className="migraine-dashboard__pain">
                        <span>Pain level</span>

                        <strong>
                            {attack.pain_level ?? '—'}

                            {attack.pain_level !== null && (
                                <small>/10</small>
                            )}
                        </strong>
                    </div>

                    <button
                        type="button"
                        className="migraine-dashboard__secondary-action"
                        onClick={() => onEndAttack?.(attack)}
                    >
                        End attack
                    </button>
                </div>
            ) : (
                <div className="migraine-dashboard__empty">
                    <span>No active attack.</span>
                </div>
            )}
        </article>
    );
}
