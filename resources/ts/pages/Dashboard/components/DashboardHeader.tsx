import { Plus } from 'lucide-react';

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
                    Migraine Diary
                </span>

                <h1 className="migraine-dashboard__title">
                    Overview
                </h1>

                <p className="migraine-dashboard__description">
                    Your recent migraine activity.
                </p>
            </div>

            <button
                type="button"
                className="migraine-dashboard__primary-action"
                onClick={onNewAttack}
            >
                <Plus size={18} />
                <span>New attack</span>
            </button>
        </header>
    );
}
