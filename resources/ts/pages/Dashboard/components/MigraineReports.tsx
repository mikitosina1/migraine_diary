import {useEffect, useState} from 'react';
import type {FormEvent} from 'react';
import {Download, FileSpreadsheet, FileText, Mail} from 'lucide-react';
import {downloadReport, getStatistics, sendReport} from '../../../api/migraineDiaryApi';
import type {PeriodStatistics} from '../../../types/statistics';
import {dashboardLocale, dashboardT} from '../translate';

type Range = 'month' | '3months' | 'year';

function saveBlob(blob: Blob, filename: string) {
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.setTimeout(() => URL.revokeObjectURL(url), 1000);
}

export default function MigraineReports() {
    const [range, setRange] = useState<Range>('month');
    const [statistics, setStatistics] = useState<PeriodStatistics | null>(null);
    const [loading, setLoading] = useState(true);
    const [busy, setBusy] = useState(false);
    const [error, setError] = useState('');
    const [notice, setNotice] = useState('');
    const [recipient, setRecipient] = useState<'self' | 'doctor'>('self');
    const [doctorEmail, setDoctorEmail] = useState('');
    const [userName, setUserName] = useState('');
    const [userLastname, setUserLastname] = useState('');

    useEffect(() => {
        let cancelled = false;
        setLoading(true);
        setError('');
        getStatistics(range).then((data) => { if (!cancelled) setStatistics(data); }).catch(() => {
            if (!cancelled) setError(dashboardT('statistics_load_error', 'Could not load statistics.'));
        }).finally(() => { if (!cancelled) setLoading(false); });
        return () => { cancelled = true; };
    }, [range]);

    const exportReport = async (format: 'pdf' | 'excel') => {
        setBusy(true); setError(''); setNotice('');
        try {
            const blob = await downloadReport(format, range);
            saveBlob(blob, `migraine-report-${range}.${format === 'excel' ? 'xlsx' : 'pdf'}`);
        } catch {
            setError(dashboardT('report_export_error', 'Could not download the report.'));
        } finally { setBusy(false); }
    };

    const emailReport = async (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault(); setBusy(true); setError(''); setNotice('');
        try {
            const message = await sendReport({recipient_type: recipient, period: range, doctor_email: recipient === 'doctor' ? doctorEmail : undefined, user_name: userName || undefined, user_lastname: userLastname || undefined, formats: ['pdf', 'excel']});
            setNotice(message || dashboardT('email_sent_success', 'Report sent.'));
        } catch {
            setError(dashboardT('email_sent_error', 'Could not send the report.'));
        } finally { setBusy(false); }
    };

    return <section className="migraine-reports">
        <div className="migraine-reports__header">
            <div><h2>{dashboardT('statistic', 'Statistics')}</h2><p>{dashboardT('statistics_description', 'Review your attacks and share a report.')}</p></div>
            <label>{dashboardT('period', 'Period')}<select value={range} onChange={(event) => setRange(event.target.value as Range)}>
                <option value="month">{dashboardT('last_month', 'This month')}</option><option value="3months">{dashboardT('last_3_months', 'Last 3 months')}</option><option value="year">{dashboardT('last_year', 'Last year')}</option>
            </select></label>
        </div>
        {error && <p className="migraine-reports__message migraine-reports__message--error">{error}</p>}
        {notice && <p className="migraine-reports__message">{notice}</p>}
        {loading ? <p className="migraine-reports__message">{dashboardT('loading', 'Loading')}…</p> : statistics && <div className="migraine-reports__body">
            <section className="migraine-reports__analysis">
                <div className="migraine-reports__summary">
                    <article><span>{dashboardT('total_attacks', 'Total attacks')}</span><strong>{statistics.summary.total_attacks}</strong></article>
                    <article><span>{dashboardT('average_pain', 'Average pain')}</span><strong>{statistics.summary.average_pain_level ?? '—'}<small>/10</small></strong></article>
                    <article><span>{dashboardT('maximum_pain', 'Maximum pain')}</span><strong>{statistics.summary.max_pain_level ?? '—'}<small>/10</small></strong></article>
                </div>
                <div className="migraine-reports__chart" aria-label={dashboardT('attack_frequency', 'Attack frequency')}>
                    {statistics.chart.map((point, index) => {
                        const max = Math.max(1, ...statistics.chart.map((item) => item.count));
                        return <div className="migraine-reports__bar-column" key={`${point.name}-${index}`} title={`${point.name}: ${point.count}`}>
                            <strong>{point.count}</strong><div className="migraine-reports__bar" style={{height: `${Math.max(point.count ? 6 : 2, point.count / max * 78)}px`}}/><span>{point.name}</span>
                        </div>;
                    })}
                </div>
            </section>
            <aside className="migraine-reports__actions">
                <div className="migraine-reports__exports">
                    <button type="button" disabled={busy} onClick={() => void exportReport('excel')}><FileSpreadsheet size={18}/>{dashboardT('download_excel', 'Download Excel')}</button>
                    <button type="button" disabled={busy} onClick={() => void exportReport('pdf')}><FileText size={18}/>{dashboardT('download_pdf', 'Download PDF')}</button>
                </div>
                <form className="migraine-reports__email" onSubmit={(event) => void emailReport(event)}>
                    <h3><Mail size={18}/>{dashboardT('send_report', 'Email report')}</h3>
                    <div className="migraine-reports__recipient">
                        <label><input type="radio" name="recipient" checked={recipient === 'self'} onChange={() => setRecipient('self')}/>{dashboardT('to_your_email', 'Send to your email')}</label>
                        <label><input type="radio" name="recipient" checked={recipient === 'doctor'} onChange={() => setRecipient('doctor')}/>{dashboardT('to_docs_email', 'Send to doctor')}</label>
                    </div>
                    {recipient === 'doctor' && <div className="migraine-reports__doctor-fields">
                        <label>{dashboardT('doctor_email', 'Doctor email')}<input required type="email" value={doctorEmail} onChange={(event) => setDoctorEmail(event.target.value)}/></label>
                        <label>{dashboardT('user_name', 'Your first name')}<input value={userName} onChange={(event) => setUserName(event.target.value)}/></label>
                        <label>{dashboardT('user_lastname', 'Your last name')}<input value={userLastname} onChange={(event) => setUserLastname(event.target.value)}/></label>
                    </div>}
                    <button type="submit" disabled={busy}><Download size={17}/>{busy ? dashboardT('sending', 'Working…') : dashboardT('send', 'Send')}</button>
                </form>
            </aside>
        </div>}
    </section>;
}
