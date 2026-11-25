import { Plus } from 'lucide-react';
import {dashboardT} from '../translate';

interface DashboardHeaderProps {
    onNewAttack?: () => void;
}

export default function DashboardHeader({
                                            onNewAttack,
                                        }: DashboardHeaderProps) {
    return (
        <header className="migraine-dashboard__header">
            <div>
                <span className="migraine-dashboard__eyebrow">
                    {dashboardT('widget_title', 'Migraine Diary')}
                </span>

                <h1 className="migraine-dashboard__title">
                    {dashboardT('dashboard_overview', 'Overview')}
                </h1>

                <p className="migraine-dashboard__description">
                    {dashboardT('dashboard_description', 'A summary of your migraine activity.')}
                </p>
            </div>

            <button
                type="button"
                className="migraine-dashboard__primary-action"
                onClick={onNewAttack}
            >
                <Plus size={18} />
                <span>{dashboardT('new_attack', 'New attack')}</span>
            </button>
        </header>
    );
}
