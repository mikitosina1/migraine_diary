import { useEffect, useState } from 'react';

import { getDashboard } from '../../api/migraineDiaryApi';
import type { Dashboard } from '../../types/dashboard';
import type { Attack } from '../../types/attack';

import DashboardHeader from './components/DashboardHeader';
import DashboardStatistics from './components/DashboardStatistics';
import ActiveAttackCard from './components/ActiveAttackCard';
import RecentAttacksCard from './components/RecentAttacksCard';

import './DashboardPage.scss';

export default function DashboardPage() {
    const [dashboard, setDashboard] = useState<Dashboard | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        let cancelled = false;

        async function loadDashboard() {
            try {
                setLoading(true);
                setError(null);

                const data = await getDashboard();

                if (!cancelled) {
                    setDashboard(data);
                }
            } catch (error) {
                if (!cancelled) {
                    setError(
                        error instanceof Error
                            ? error.message
                            : 'Failed to load migraine diary.'
                    );
                }
            } finally {
                if (!cancelled) {
                    setLoading(false);
                }
            }
        }

        void loadDashboard();

        return () => {
            cancelled = true;
        };
    }, []);

    const handleNewAttack = () => {
        // TODO: open create attack flow
    };

    const handleEndAttack = (attack: Attack) => {
        // TODO: end attack
        console.log('End attack', attack.id);
    };

    const handleSelectAttack = (attack: Attack) => {
        // TODO: open attack details/edit
        console.log('Select attack', attack.id);
    };

    if (loading) {
        return (
            <section className="migraine-dashboard">
                <div className="migraine-dashboard__state">
                    Loading…
                </div>
            </section>
        );
    }

    if (error || !dashboard) {
        return (
            <section className="migraine-dashboard">
                <div className="migraine-dashboard__state migraine-dashboard__state--error">
                    {error ?? 'Failed to load migraine diary.'}
                </div>
            </section>
        );
    }

    return (
        <section className="migraine-dashboard">
            <DashboardHeader onNewAttack={handleNewAttack} />

            <DashboardStatistics
                statistics={dashboard.statistics}
            />

            <div className="migraine-dashboard__grid">
                <ActiveAttackCard
                    attack={dashboard.active_attack}
                    onEndAttack={handleEndAttack}
                />

                <RecentAttacksCard
                    attacks={dashboard.recent_attacks}
                    onSelectAttack={handleSelectAttack}
                />
            </div>
        </section>
    );
}
