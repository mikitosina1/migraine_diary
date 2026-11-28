import {useEffect, useState} from 'react';
import type {MouseEvent} from 'react';
import {CalendarClock, Trash2} from 'lucide-react';
import {deleteAttack, getAttacks} from '../../../api/migraineDiaryApi';
import type {Attack} from '../../../types/attack';
import {dashboardLocale, dashboardT} from '../translate';

type Range = 'month' | '3months' | 'year';

interface Props {
    onSelectAttack: (attack: Attack) => void;
    refreshToken: number;
    onChanged: () => void;
}

export default function MigraineAttackList({onSelectAttack, refreshToken, onChanged}: Props) {
    const [range, setRange] = useState<Range>('month');
    const [painLevel, setPainLevel] = useState('all');
    const [attacks, setAttacks] = useState<Attack[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [deletingId, setDeletingId] = useState<number | null>(null);

    useEffect(() => {
        let cancelled = false;
        setLoading(true); setError('');
        getAttacks(range, painLevel).then((items) => { if (!cancelled) setAttacks(items); }).catch(() => {
            if (!cancelled) setError(dashboardT('attacks_load_error', 'Could not load attacks.'));
        }).finally(() => { if (!cancelled) setLoading(false); });
        return () => { cancelled = true; };
    }, [range, painLevel, refreshToken]);

    const removeAttack = async (event: MouseEvent<HTMLButtonElement>, attack: Attack) => {
        event.stopPropagation();
        if (!window.confirm(dashboardT('delete_confirm', 'Delete this attack?'))) return;
        setDeletingId(attack.id); setError('');
        try {
            await deleteAttack(attack.id);
            setAttacks((items) => items.filter((item) => item.id !== attack.id));
            onChanged();
        } catch {
            setError(dashboardT('delete_error', 'Could not delete the attack.'));
        } finally { setDeletingId(null); }
    };

    return <section className="migraine-attack-list">
        <div className="migraine-attack-list__toolbar">
            <h2>{dashboardT('list', 'Attack diary')}</h2>
            <div>
                <label>{dashboardT('period', 'Period')}<select value={range} onChange={(event) => setRange(event.target.value as Range)}>
                    <option value="month">{dashboardT('last_month', 'This month')}</option><option value="3months">{dashboardT('last_3_months', 'Last 3 months')}</option><option value="year">{dashboardT('last_year', 'Last year')}</option>
                </select></label>
                <label>{dashboardT('pain_level', 'Pain level')}<select value={painLevel} onChange={(event) => setPainLevel(event.target.value)}>
                    <option value="all">{dashboardT('all_pain_lvl', 'All pain levels')}</option>{Array.from({length: 10}, (_, i) => <option key={i + 1} value={i + 1}>{dashboardT('level', 'Level')} {i + 1}</option>)}
                </select></label>
            </div>
        </div>
        {error && <p className="migraine-attack-list__error">{error}</p>}
        {loading ? <p className="migraine-attack-list__empty">{dashboardT('loading', 'Loading')}…</p> : attacks.length === 0 ? <p className="migraine-attack-list__empty">{dashboardT('no_rec_found', 'No attacks found.')}</p> :
            <div className="migraine-attack-list__items">{attacks.map((attack) => <article className="migraine-attack-list__item" key={attack.id}>
                <button type="button" className="migraine-attack-list__open" onClick={() => onSelectAttack(attack)}>
                    <span className="migraine-attack-list__date"><CalendarClock size={17}/>{attack.start_time ? new Intl.DateTimeFormat(dashboardLocale(), {dateStyle: 'medium', timeStyle: 'short'}).format(new Date(attack.start_time)) : '—'}</span>
                    <span className="migraine-attack-list__pain">{dashboardT('pain_level', 'Pain level')}: {attack.pain_level ?? '—'}/10</span>
                    <span className="migraine-attack-list__status">{attack.is_active ? dashboardT('active_attack', 'Active attack') : attack.end_time ? `${dashboardT('ended', 'Ended')} ${new Intl.DateTimeFormat(dashboardLocale(), {dateStyle: 'short', timeStyle: 'short'}).format(new Date(attack.end_time))}` : ''}</span>
                </button>
                <button type="button" className="migraine-attack-list__delete" disabled={deletingId === attack.id} aria-label={dashboardT('delete', 'Delete')} onClick={(event) => void removeAttack(event, attack)}><Trash2 size={17}/></button>
            </article>)}</div>
        }
    </section>;
}
