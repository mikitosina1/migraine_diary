import {useEffect, useMemo, useRef, useState} from 'react';
import type {CSSProperties} from 'react';
import {Check, ChevronLeft, ChevronRight, Plus, X} from 'lucide-react';
import TranslationService from '@/services/TranslationService';

import {createAttack} from '../../../api/migraineDiaryApi';
import type {CreateAttackPayload} from '../../../api/migraineDiaryApi';
import type {AttackDictionaries, DictionaryItem, UserDictionaryItem} from '../../../types/dictionary';

interface CreateAttackDialogProps {
    dictionaries: AttackDictionaries;
    onClose: () => void;
    onCreated: () => void;
}

interface FormState {
    start_time: string;
    pain_level: number | null;
    symptoms: number[];
    userSymptoms: number[];
    userSymptomsNew: string[];
    triggers: number[];
    userTriggers: number[];
    userTriggersNew: string[];
    meds: number[];
    userMeds: number[];
    userMedsNew: string[];
}

const steps = ['details', 'symptoms', 'triggers', 'meds', 'review'];

function localDateTime(): string {
    const date = new Date();
    const offset = date.getTimezoneOffset();
    return new Date(date.getTime() - offset * 60_000).toISOString().slice(0, 16);
}

export default function CreateAttackDialog({dictionaries, onClose, onCreated}: CreateAttackDialogProps) {
    const dialogRef = useRef<HTMLDialogElement>(null);
    const [step, setStep] = useState(0);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [activeCustomType, setActiveCustomType] = useState<'userSymptomsNew' | 'userTriggersNew' | 'userMedsNew' | null>(null);
    const [customDraft, setCustomDraft] = useState('');
    const [form, setForm] = useState<FormState>({
        start_time: localDateTime(), pain_level: null,
        symptoms: [], userSymptoms: [], userSymptomsNew: [],
        triggers: [], userTriggers: [], userTriggersNew: [],
        meds: [], userMeds: [], userMedsNew: [],
    });

    useEffect(() => {
        const dialog = dialogRef.current;
        if (!dialog) return;
        dialog.showModal();
        const handleClose = () => onClose();
        dialog.addEventListener('close', handleClose);
        return () => dialog.removeEventListener('close', handleClose);
    }, [onClose]);

    const t = (key: string, fallback: string) => {
        const translation = TranslationService.t(`migrainediary.${key}`);
        return translation === `migrainediary.${key}` ? fallback : translation;
    };
    const update = <K extends keyof FormState>(key: K, value: FormState[K]) =>
        setForm(current => ({...current, [key]: value}));

    const toggleId = (key: 'symptoms' | 'userSymptoms' | 'triggers' | 'userTriggers' | 'meds' | 'userMeds', id: number) => {
        update(key, form[key].includes(id) ? form[key].filter(item => item !== id) : [...form[key], id]);
    };

    const addCustom = (key: 'userSymptomsNew' | 'userTriggersNew' | 'userMedsNew') => {
        setActiveCustomType(key);
        setCustomDraft('');
    };

    const saveCustom = (key: 'userSymptomsNew' | 'userTriggersNew' | 'userMedsNew') => {
        const value = customDraft.trim();
        if (value && !form[key].some(item => item.toLocaleLowerCase() === value.toLocaleLowerCase())) {
            update(key, [...form[key], value]);
        }
        setActiveCustomType(null);
        setCustomDraft('');
    };

    const setCustom = (key: 'userSymptomsNew' | 'userTriggersNew' | 'userMedsNew', name: string) => {
        update(key, form[key].filter(item => item !== name));
    };

    const selectedNames = useMemo(() => ({
        symptoms: [...dictionaries.symptoms.filter(item => form.symptoms.includes(item.id)).map(item => item.name),
            ...dictionaries.user_symptoms.filter(item => form.userSymptoms.includes(item.id)).map(item => item.name), ...form.userSymptomsNew],
        triggers: [...dictionaries.triggers.filter(item => form.triggers.includes(item.id)).map(item => item.name),
            ...dictionaries.user_triggers.filter(item => form.userTriggers.includes(item.id)).map(item => item.name), ...form.userTriggersNew],
        meds: [...dictionaries.meds.filter(item => form.meds.includes(item.id)).map(item => item.name),
            ...dictionaries.user_meds.filter(item => form.userMeds.includes(item.id)).map(item => item.name), ...form.userMedsNew],
    }), [dictionaries, form]);

    const goNext = () => {
        if (step === 0 && !form.pain_level) {
            setError(t('pain_level_required', 'Select a pain level to continue.'));
            return;
        }
        setError(null);
        setStep(current => Math.min(current + 1, steps.length - 1));
    };

    const submit = async () => {
        if (!form.pain_level) return;
        setSaving(true);
        setError(null);
        const payload: CreateAttackPayload = {
            start_time: form.start_time,
            pain_level: form.pain_level,
            symptoms: form.symptoms,
            userSymptoms: form.userSymptoms,
            userSymptomsNew: form.userSymptomsNew,
            triggers: form.triggers,
            userTriggers: form.userTriggers,
            userTriggersNew: form.userTriggersNew,
            meds: form.meds.map(id => ({id, dosage: null})),
            userMeds: form.userMeds,
            userMedsDosages: {},
            userMedsNew: form.userMedsNew,
        };
        try {
            await createAttack(payload);
            onCreated();
            dialogRef.current?.close();
        } catch (submitError) {
            setError(submitError instanceof Error ? submitError.message : t('form_send_err', 'Could not save the attack.'));
        } finally {
            setSaving(false);
        }
    };

    const checkboxGroup = (
        items: (DictionaryItem | UserDictionaryItem)[],
        selected: number[],
        key: 'symptoms' | 'userSymptoms' | 'triggers' | 'userTriggers' | 'meds' | 'userMeds',
    ) => (
        <div className="attack-form__chips">
            {items.map(item => <div className="attack-form__choice" key={item.id}>
                <label className={`attack-form__chip${selected.includes(item.id) ? ' is-selected' : ''}`}>
                    <input type="checkbox" checked={selected.includes(item.id)} onChange={() => toggleId(key, item.id)} />
                    <span>{item.name}</span>
                </label>
            </div>
            )}
        </div>
    );

    const customChips = (names: string[], key: 'userSymptomsNew' | 'userTriggersNew' | 'userMedsNew') => (
        <div className="attack-form__chips">
            {names.map(name => <button type="button" className="attack-form__chip is-selected" key={name} onClick={() => setCustom(key, name)} title={t('delete', 'Remove')}>{name}<X size={14}/></button>)}
        </div>
    );

    const renderCategory = (
        title: string,
        base: DictionaryItem[], user: UserDictionaryItem[], baseKey: 'symptoms' | 'triggers' | 'meds',
        userKey: 'userSymptoms' | 'userTriggers' | 'userMeds', customKey: 'userSymptomsNew' | 'userTriggersNew' | 'userMedsNew',
    ) => (
        <section className="attack-form__category">
            <div className="attack-form__category-heading"><h3>{title}</h3><button className="attack-form__add" type="button" onClick={() => addCustom(customKey)}><Plus size={15}/>{t('add_custom', 'Add')}</button></div>
            {checkboxGroup(base, form[baseKey], baseKey)}
            {checkboxGroup(user, form[userKey], userKey)}
            {customChips(form[customKey], customKey)}
            {activeCustomType === customKey && <form className="attack-form__custom-entry" onSubmit={event => {event.preventDefault(); saveCustom(customKey);}}>
                <input autoFocus maxLength={255} required value={customDraft} onChange={event => setCustomDraft(event.target.value)} placeholder={`${t(customKey === 'userMedsNew' ? 'medication_name' : 'name', 'Name')} *`} aria-label={`${t(customKey === 'userMedsNew' ? 'medication_name' : 'name', 'Name')} *`} />
                <button type="submit" aria-label={t('next', 'Add')}><Check size={16}/></button>
                <button type="button" aria-label={t('back', 'Cancel')} onClick={() => setActiveCustomType(null)}><X size={16}/></button>
            </form>}
        </section>
    );

    return (
        <dialog className="attack-dialog" ref={dialogRef} onClick={event => {if (event.target === dialogRef.current) dialogRef.current?.close();}}>
            <div className="attack-dialog__panel">
                <header className="attack-dialog__header">
                    <div><span className="attack-dialog__eyebrow">{t('new_attack', 'New attack')}</span><h2>{t(steps[step], ['Details', 'Symptoms', 'Triggers', 'Medication', 'Review'][step])}</h2></div>
                    <button type="button" className="attack-dialog__close" onClick={() => dialogRef.current?.close()} aria-label="Close"><X size={20}/></button>
                </header>
                <nav className="attack-form__steps" aria-label="Form progress" style={{'--step-progress': step / (steps.length - 1)} as CSSProperties}>
                    {steps.map((name, index) => <div key={name} className={`attack-form__step${index === step ? ' is-current' : ''}${index < step ? ' is-done' : ''}`}><span>{index < step ? <Check size={14}/> : index + 1}</span><small>{t(name, ['Details', 'Symptoms', 'Triggers', 'Medication', 'Review'][index])}</small></div>)}
                </nav>
                <div className="attack-form__body">
                    {step === 0 && <>
                        <label className="attack-form__label" htmlFor="attack-start-time">{t('start_time', 'Start time')}</label>
                        <input id="attack-start-time" className="attack-form__datetime" type="datetime-local" value={form.start_time} onChange={event => update('start_time', event.target.value)} required />
                        <fieldset className="attack-form__pain"><legend>{t('pain_level', 'Pain level')}</legend><div className="attack-form__pain-options">{Array.from({length: 10}, (_, index) => 10 - index).map(level => <label key={level} className={`attack-form__pain-option${form.pain_level === level ? ' is-selected' : ''}`}><input type="radio" name="pain_level" checked={form.pain_level === level} onChange={() => update('pain_level', level)}/><span className="attack-form__pain-number">{level}</span><span className="attack-form__pain-label">{t(`pain_level_${level}`, `Level ${level}`)}</span></label>)}</div></fieldset>
                    </>}
                    {step === 1 && renderCategory(t('symptoms', 'Symptoms'), dictionaries.symptoms, dictionaries.user_symptoms, 'symptoms', 'userSymptoms', 'userSymptomsNew')}
                    {step === 2 && renderCategory(t('triggers', 'Triggers'), dictionaries.triggers, dictionaries.user_triggers, 'triggers', 'userTriggers', 'userTriggersNew')}
                    {step === 3 && renderCategory(t('meds', 'Medication'), dictionaries.meds, dictionaries.user_meds, 'meds', 'userMeds', 'userMedsNew')}
                    {step === 4 && <div className="attack-form__review">
                        <div><span>{t('start_time', 'Start time')}</span><strong>{new Date(form.start_time).toLocaleString()}</strong></div>
                        <div><span>{t('pain_level', 'Pain level')}</span><strong>{form.pain_level} / 10 - {t(`pain_level_${form.pain_level}`, '')}</strong></div>
                        {(['symptoms', 'triggers'] as const).map(key => <div key={key}><span>{t(key, key)}</span><strong>{selectedNames[key].length ? selectedNames[key].join(', ') : '-'}</strong></div>)}
                        <div><span>{t('meds', 'Meds')}</span><strong>{[
                            ...dictionaries.meds.filter(item => form.meds.includes(item.id)).map(item => item.name),
                            ...dictionaries.user_meds.filter(item => form.userMeds.includes(item.id)).map(item => item.name),
                            ...form.userMedsNew,
                        ].join(', ') || '-'}</strong></div>
                    </div>}
                    {error && <p className="attack-form__error" role="alert">{error}</p>}
                </div>
                <footer className="attack-form__footer">
                    <button type="button" className="attack-form__button attack-form__button--secondary" onClick={() => step === 0 ? dialogRef.current?.close() : setStep(current => current - 1)}><ChevronLeft size={17}/>{t('back', 'Back')}</button>
                    {step < steps.length - 1
                        ? <button type="button" className="attack-form__button attack-form__button--primary" onClick={goNext}>{t('next', 'Next')}<ChevronRight size={17}/></button>
                        : <button type="button" className="attack-form__button attack-form__button--primary" onClick={() => void submit()} disabled={saving}>{saving ? t('loading', 'Saving…') : t('add_attack', 'Save attack')}</button>}
                </footer>
            </div>
        </dialog>
    );
}
