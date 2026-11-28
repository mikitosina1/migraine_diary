import {useEffect, useMemo, useState} from 'react';
import {CalendarDays, ChevronLeft, ChevronRight} from 'lucide-react';
import {getCalendarAttacks} from '../../../api/migraineDiaryApi';
import type {Attack} from '../../../types/attack';
import {dashboardLocale, dashboardT} from '../translate';

interface Props {
    onSelectAttack: (attack: Attack) => void;
    refreshToken: number;
}

const dateKey = (date: Date) => `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
const monthKey = (date: Date) => `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;

export default function MigraineCalendar({onSelectAttack, refreshToken}: Props) {
    const [month, setMonth] = useState(() => new Date(new Date().getFullYear(), new Date().getMonth(), 1));
    const [attacks, setAttacks] = useState<Attack[]>([]);
    const [selectedDay, setSelectedDay] = useState<string | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(false);

    useEffect(() => {
        let cancelled = false;
        setLoading(true);
        setError(false);
        getCalendarAttacks(monthKey(month)).then((items) => {
            if (!cancelled) setAttacks(items);
        }).catch(() => {
            if (!cancelled) setError(true);
        }).finally(() => {
            if (!cancelled) setLoading(false);
        });
        return () => { cancelled = true; };
    }, [month, refreshToken]);

    const grouped = useMemo(() => {
        const map = new Map<string, Attack[]>();
        attacks.forEach((attack) => {
            if (!attack.start_time) return;
            const key = dateKey(new Date(attack.start_time));
            map.set(key, [...(map.get(key) ?? []), attack]);
        });
        return map;
    }, [attacks]);

    const days = useMemo(() => {
        const first = new Date(month.getFullYear(), month.getMonth(), 1);
        const count = new Date(month.getFullYear(), month.getMonth() + 1, 0).getDate();
        const offset = (first.getDay() + 6) % 7;
        return [...Array(offset).fill(null), ...Array.from({length: count}, (_, i) => new Date(month.getFullYear(), month.getMonth(), i + 1))];
    }, [month]);
    const selectedAttacks = selectedDay ? grouped.get(selectedDay) ?? [] : [];
    const monthLabel = new Intl.DateTimeFormat(dashboardLocale(), {month: 'long', year: 'numeric'}).format(month);
    const weekdays = Array.from({length: 7}, (_, i) => new Intl.DateTimeFormat(dashboardLocale(), {weekday: 'short'}).format(new Date(2024, 0, 1 + i)));

    return (
        <section className="migraine-calendar">
            <div className="migraine-calendar__main">
                <div className="migraine-calendar__toolbar">
                    <h2><CalendarDays size={20}/>{dashboardT('calendar', 'Calendar')}</h2>
                    <div className="migraine-calendar__month-controls">
                        <button type="button" aria-label={dashboardT('previous_month', 'Previous month')} onClick={() => { setMonth(new Date(month.getFullYear(), month.getMonth() - 1, 1)); setSelectedDay(null); }}><ChevronLeft size={18}/></button>
                        <strong>{monthLabel}</strong>
                        <button type="button" aria-label={dashboardT('next_month', 'Next month')} onClick={() => { setMonth(new Date(month.getFullYear(), month.getMonth() + 1, 1)); setSelectedDay(null); }}><ChevronRight size={18}/></button>
                        <button type="button" onClick={() => { setMonth(new Date(new Date().getFullYear(), new Date().getMonth(), 1)); setSelectedDay(null); }}>{dashboardT('today', 'Today')}</button>
                    </div>
                </div>
                {error && <p className="migraine-calendar__message migraine-calendar__message--error">{dashboardT('calendar_load_error', 'Could not load calendar attacks.')}</p>}
                <div className={`migraine-calendar__grid${loading ? ' is-loading' : ''}`}>
                    {weekdays.map((day, index) => <div className="migraine-calendar__weekday" key={`${day}-${index}`}>{day}</div>)}
                    {days.map((day, index) => {
                        if (!day) return <div className="migraine-calendar__day migraine-calendar__day--empty" key={`empty-${index}`}/>;
                        const key = dateKey(day);
                        const dayAttacks = grouped.get(key) ?? [];
                        return <button type="button" key={key} className={`migraine-calendar__day${selectedDay === key ? ' is-selected' : ''}${dayAttacks.length ? ' has-attacks' : ''}`} onClick={() => setSelectedDay(key)} aria-pressed={selectedDay === key}>
                            <span>{day.getDate()}</span>{dayAttacks.length > 0 && <span className="migraine-calendar__day-count">{dayAttacks.length}</span>}
                        </button>;
                    })}
                </div>
            </div>
            <aside className={`migraine-calendar__selection${selectedDay ? ' has-selection' : ''}`}>
                {selectedDay ? <>
                    <div className="migraine-calendar__selection-header">
                        <h3>{new Intl.DateTimeFormat(dashboardLocale(), {dateStyle: 'full'}).format(new Date(`${selectedDay}T12:00:00`))}</h3>
                        <span>{selectedAttacks.length} {dashboardT('attacks', 'attacks')}</span>
                    </div>
                    {selectedAttacks.length === 0 ? <div className="migraine-calendar__empty-day">{dashboardT('no_attacks_on_day', 'No attacks on this day.')}</div> :
                        <div className="migraine-calendar__attack-list">{selectedAttacks.map((attack) => <button type="button" className="migraine-calendar__attack" key={attack.id} onClick={() => onSelectAttack(attack)}>
                            <span className="migraine-calendar__attack-time">{attack.start_time ? new Intl.DateTimeFormat(dashboardLocale(), {hour: '2-digit', minute: '2-digit'}).format(new Date(attack.start_time)) : '—'}</span>
                            <span className="migraine-calendar__attack-pain">{dashboardT('pain_level', 'Pain level')} <strong>{attack.pain_level ?? '—'}/10</strong></span>
                            <span className="migraine-calendar__attack-status">{attack.is_active ? dashboardT('active_attack', 'Active attack') : dashboardT('attack_details', 'Attack details')}</span>
                        </button>)}</div>
                    }
                </> : <div className="migraine-calendar__empty-selection"><CalendarDays size={30}/><span>{dashboardT('select_calendar_day', 'Select a day to view attacks')}</span></div>}
            </aside>
        </section>
    );
}
