import {useEffect, useRef, useState} from 'react';
import {Check, Pencil, X} from 'lucide-react';
import type {Attack} from '../../../types/attack';
import type {AttackDictionaries, DictionaryItem, UserDictionaryItem} from '../../../types/dictionary';
import {updateAttack} from '../../../api/migraineDiaryApi';
import type {UpdateAttackPayload} from '../../../api/migraineDiaryApi';
import {dashboardLocale, dashboardT} from '../translate';

interface AttackDetailsDialogProps {
    attack: Attack;
    dictionaries: AttackDictionaries;
    onClose: () => void;
    onSaved: (attack: Attack) => void;
    onEndAttack: (attack: Attack) => void;
    isEnding: boolean;
    endError: string | null;
}

interface AttackForm {
    pain_level: number;
    notes: string;
    symptoms: number[];
    userSymptoms: number[];
    triggers: number[];
    userTriggers: number[];
    meds: number[];
    userMeds: number[];
    userSymptomsNew: string[];
    userTriggersNew: string[];
    userMedsNew: string[];
}

function formatDate(value: string | null): string {
    if (!value) return dashboardT('not_set', 'Not set');
    return new Intl.DateTimeFormat(dashboardLocale(), {dateStyle: 'medium', timeStyle: 'short'}).format(new Date(value));
}

export default function AttackDetailsDialog({attack, dictionaries, onClose, onSaved, onEndAttack, isEnding, endError}: AttackDetailsDialogProps) {
    const dialogRef = useRef<HTMLDialogElement>(null);
    const [editing, setEditing] = useState(false);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [form, setForm] = useState<AttackForm>(() => ({
        pain_level: attack.pain_level ?? 1,
        notes: attack.notes ?? '',
        symptoms: attack.symptoms.map(item => item.id),
        userSymptoms: attack.user_symptoms.map(item => item.id),
        triggers: attack.triggers.map(item => item.id),
        userTriggers: attack.user_triggers.map(item => item.id),
        meds: attack.meds.map(item => item.id),
        userMeds: attack.user_meds.map(item => item.id),
        userSymptomsNew: [],
        userTriggersNew: [],
        userMedsNew: [],
    }));
    const [customDrafts, setCustomDrafts] = useState({userSymptomsNew: '', userTriggersNew: '', userMedsNew: ''});

    useEffect(() => {
        const dialog = dialogRef.current;
        if (!dialog) return;
        dialog.showModal();
        const handleClose = () => onClose();
        dialog.addEventListener('close', handleClose);
        return () => dialog.removeEventListener('close', handleClose);
    }, [onClose]);

    const toggle = (key: keyof Pick<AttackForm, 'symptoms' | 'userSymptoms' | 'triggers' | 'userTriggers' | 'meds' | 'userMeds'>, id: number) => {
        setForm(current => ({...current, [key]: current[key].includes(id) ? current[key].filter(value => value !== id) : [...current[key], id]}));
    };

    const t = (key: string, fallback: string) => dashboardT(key, fallback);

    const renderChoices = (title: string, predefined: DictionaryItem[], userDefined: UserDictionaryItem[], predefinedKey: 'symptoms' | 'triggers' | 'meds', userKey: 'userSymptoms' | 'userTriggers' | 'userMeds', customKey: 'userSymptomsNew' | 'userTriggersNew' | 'userMedsNew') => (
        <section className="attack-form__category">
            <h3>{title}</h3>
            <div className="attack-form__chips">
                {[...predefined.map(item => ({...item, custom: false})), ...userDefined.map(item => ({...item, custom: true}))].map(item => {
                    const key = item.custom ? userKey : predefinedKey;
                    const checked = form[key].includes(item.id);
                    return <label key={`${key}-${item.id}`} className={`attack-form__chip${checked ? ' is-selected' : ''}`}>
                        <input type="checkbox" checked={checked} onChange={() => toggle(key, item.id)} />
                        <span>{item.name}</span>
                    </label>;
                })}
                {form[customKey].map(name => <button key={name} type="button" className="attack-form__chip is-selected" onClick={() => setForm(current => ({...current, [customKey]: current[customKey].filter(value => value !== name)}))}>{name}<X size={14}/></button>)}
                {predefined.length + userDefined.length === 0 && form[customKey].length === 0 && <span className="attack-details__muted">{t('no_options', 'No options available.')}</span>}
            </div>
            <form className="attack-form__custom-entry" onSubmit={event => {
                event.preventDefault();
                const value = customDrafts[customKey].trim();
                if (value && !form[customKey].some(name => name.toLocaleLowerCase() === value.toLocaleLowerCase())) {
                    setForm(current => ({...current, [customKey]: [...current[customKey], value]}));
                }
                setCustomDrafts(current => ({...current, [customKey]: ''}));
            }}>
                <input maxLength={255} value={customDrafts[customKey]} onChange={event => setCustomDrafts(current => ({...current, [customKey]: event.target.value}))} placeholder={`${t('name', 'Name')} *`} aria-label={`${title} ${t('name', 'Name')}`} />
                <button type="submit" aria-label={t('add_custom', 'Add')}><Check size={16}/></button>
            </form>
        </section>
    );

    const save = async () => {
        setSaving(true);
        setError(null);
        const payload: UpdateAttackPayload = {
            pain_level: form.pain_level,
            notes: form.notes.trim(),
            symptoms: form.symptoms,
            userSymptoms: form.userSymptoms,
            userSymptomsNew: form.userSymptomsNew,
            triggers: form.triggers,
            userTriggers: form.userTriggers,
            userTriggersNew: form.userTriggersNew,
            meds: form.meds.map(id => ({id, dosage: attack.meds.find(med => med.id === id)?.dosage ?? null})),
            userMeds: form.userMeds,
            userMedsNew: form.userMedsNew,
        };

        try {
            const updated = await updateAttack(attack.id, payload);
            setForm(current => ({
                ...current,
                userSymptoms: updated.user_symptoms.map(item => item.id),
                userSymptomsNew: [],
                userTriggers: updated.user_triggers.map(item => item.id),
                userTriggersNew: [],
                userMeds: updated.user_meds.map(item => item.id),
                userMedsNew: [],
            }));
            onSaved(updated);
            setEditing(false);
        } catch (saveError) {
            setError(saveError instanceof Error ? saveError.message : t('form_send_err', 'Could not save the attack.'));
        } finally {
            setSaving(false);
        }
    };

    return <dialog className="attack-dialog attack-details" ref={dialogRef} onClick={event => {if (event.target === dialogRef.current && !saving) dialogRef.current?.close();}}>
        <div className="attack-dialog__panel">
            <header className="attack-dialog__header">
                <div>
                    <span className="attack-dialog__eyebrow">{t('attack_details', 'Attack details')}</span>
                    <h2>{attack.is_active ? t('active_attack', 'Active attack') : t('past_attack', 'Past attack')}</h2>
                </div>
                <button type="button" className="attack-dialog__close" onClick={() => dialogRef.current?.close()} aria-label={t('close', 'Close')}><X size={20}/></button>
            </header>

            <div className="attack-form__body attack-details__body">
                <div className="attack-details__dates">
                    <div><span>{t('started', 'Started')}</span><strong>{formatDate(attack.start_time)}</strong></div>
                    <div><span>{t('ended', 'Ended')}</span><strong>{formatDate(attack.end_time)}</strong></div>
                </div>

                {editing ? <>
                    <label className="attack-form__label" htmlFor="attack-detail-pain">{t('pain_level', 'Pain level')}</label>
                    <select id="attack-detail-pain" className="attack-details__select" value={form.pain_level} onChange={event => setForm(current => ({...current, pain_level: Number(event.target.value)}))}>
                        {Array.from({length: 10}, (_, index) => index + 1).map(level => <option key={level} value={level}>{level} - {t(`pain_level_${level}`, `Level ${level}`)}</option>)}
                    </select>
                    <label className="attack-form__label attack-details__notes-label" htmlFor="attack-detail-notes">{t('notes', 'Notes')}</label>
                    <textarea id="attack-detail-notes" className="attack-details__notes" maxLength={1000} value={form.notes} onChange={event => setForm(current => ({...current, notes: event.target.value}))}/>
                    {renderChoices(t('symptoms', 'Symptoms'), dictionaries.symptoms, dictionaries.user_symptoms, 'symptoms', 'userSymptoms', 'userSymptomsNew')}
                    {renderChoices(t('triggers', 'Triggers'), dictionaries.triggers, dictionaries.user_triggers, 'triggers', 'userTriggers', 'userTriggersNew')}
                    {renderChoices(t('meds', 'Medication'), dictionaries.meds, dictionaries.user_meds, 'meds', 'userMeds', 'userMedsNew')}
                </> : <>
                    <div className="attack-details__field"><span>{t('pain_level', 'Pain level')}</span><strong>{attack.pain_level ?? '-'} / 10</strong></div>
                    {(['symptoms', 'triggers', 'meds'] as const).map(key => {
                        const names = key === 'symptoms'
                            ? [...attack.symptoms.map(item => item.name), ...attack.user_symptoms.map(item => item.name)]
                            : key === 'triggers'
                                ? [...attack.triggers.map(item => item.name), ...attack.user_triggers.map(item => item.name)]
                                : [...attack.meds.map(item => item.name), ...attack.user_meds.map(item => item.name)];
                        return <div className="attack-details__field" key={key}><span>{t(key, key)}</span><strong>{names.join(', ') || '-'}</strong></div>;
                    })}
                    <div className="attack-details__field"><span>{t('notes', 'Notes')}</span><strong className="attack-details__notes-value">{attack.notes || '-'}</strong></div>
                </>}
                {(error || endError) && <p className="attack-form__error" role="alert">{error ?? endError}</p>}
            </div>

            <footer className="attack-form__footer">
                {editing ? <>
                    <button type="button" className="attack-form__button attack-form__button--secondary" onClick={() => {setEditing(false); setError(null);}} disabled={saving}>{t('cancel', 'Cancel')}</button>
                    <button type="button" className="attack-form__button attack-form__button--primary" onClick={() => void save()} disabled={saving}>{saving ? t('loading', 'Saving...') : <><Check size={17}/>{t('save_changes', 'Save changes')}</>}</button>
                </> : <>
                    {attack.is_active && <button type="button" className="attack-form__button attack-form__button--secondary" onClick={() => onEndAttack(attack)} disabled={isEnding}>{isEnding ? t('ending_attack', 'Ending...') : t('end_attack', 'End attack')}</button>}
                    <button type="button" className="attack-form__button attack-form__button--secondary" onClick={() => setEditing(true)}><Pencil size={16}/>{t('edit', 'Edit')}</button>
                    <button type="button" className="attack-form__button attack-form__button--primary" onClick={() => dialogRef.current?.close()}>{t('close', 'Close')}</button>
                </>}
            </footer>
        </div>
    </dialog>;
}
