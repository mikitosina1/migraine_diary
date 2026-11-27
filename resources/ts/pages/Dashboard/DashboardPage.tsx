import { useCallback, useEffect, useState } from 'react';

import { endAttack, getDashboard } from '../../api/migraineDiaryApi';
import type { Dashboard } from '../../types/dashboard';
import type { Attack } from '../../types/attack';

import DashboardHeader from './components/DashboardHeader';
import DashboardStatistics from './components/DashboardStatistics';
import ActiveAttackCard from './components/ActiveAttackCard';
import RecentAttacksCard from './components/RecentAttacksCard';
import CreateAttackDialog from './components/CreateAttackDialog';
import AttackDetailsDialog from './components/AttackDetailsDialog';
import {dashboardT} from './translate';

import './DashboardPage.scss';
import './CreateAttackDialog.scss';

export default function DashboardPage() {
    const [dashboard, setDashboard] = useState<Dashboard | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [createDialogOpen, setCreateDialogOpen] = useState(false);
    const [endingAttackId, setEndingAttackId] = useState<number | null>(null);
    const [endAttackError, setEndAttackError] = useState<string | null>(null);
    const [selectedAttack, setSelectedAttack] = useState<Attack | null>(null);

    const loadDashboard = useCallback(async (cancelled?: () => boolean, showLoading = true) => {
        try {
            if (showLoading) setLoading(true);
            setError(null);
            const data = await getDashboard();
            if (!cancelled || !cancelled()) setDashboard(data);
        } catch {
            if (!cancelled || !cancelled()) {
                setError(dashboardT('dashboard_load_error', 'Failed to load migraine diary.'));
            }
        } finally {
            if (!cancelled || !cancelled()) setLoading(false);
        }
    }, []);

    useEffect(() => {
        let cancelled = false;
        void loadDashboard(() => cancelled);

        return () => {
            cancelled = true;
        };
    }, [loadDashboard]);

    const handleNewAttack = () => {
        setCreateDialogOpen(true);
    };

    const closeCreateDialog = useCallback(() => setCreateDialogOpen(false), []);
    const closeAttackDetails = useCallback(() => setSelectedAttack(null), []);

    const handleEndAttack = async (attack: Attack) => {
        setEndingAttackId(attack.id);
        setEndAttackError(null);

        try {
            await endAttack(attack.id);
            setSelectedAttack(null);
            await loadDashboard(undefined, false);
        } catch {
            setEndAttackError(dashboardT('end_attack_error', 'Could not end the attack. Please try again.'));
        } finally {
            setEndingAttackId(null);
        }
    };

    const handleSelectAttack = (attack: Attack) => {
        setEndAttackError(null);
        setSelectedAttack(attack);
    };

    if (loading) {
        return (
            <section className="migraine-dashboard">
                <div className="migraine-dashboard__state">
                    {dashboardT('loading', 'Loading')}…
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
                    isEnding={endingAttackId === dashboard.active_attack?.id}
                    error={endAttackError}
                />

                <RecentAttacksCard
                    attacks={dashboard.recent_attacks}
                    onSelectAttack={handleSelectAttack}
                />
            </div>

            {createDialogOpen && (
                <CreateAttackDialog
                    dictionaries={dashboard.dictionaries}
                    onClose={closeCreateDialog}
                    onCreated={() => { void loadDashboard(undefined, false); }}
                />
            )}

            {selectedAttack && (
                <AttackDetailsDialog
                    key={selectedAttack.id}
                    attack={selectedAttack}
                    dictionaries={dashboard.dictionaries}
                    onClose={closeAttackDetails}
                    onSaved={(updated) => {
                        setSelectedAttack(updated);
                        void loadDashboard(undefined, false);
                    }}
                    onEndAttack={(attack) => { void handleEndAttack(attack); }}
                    isEnding={endingAttackId === selectedAttack.id}
                    endError={endAttackError}
                />
            )}
        </section>
    );
}
