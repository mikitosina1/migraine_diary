import type {Dashboard} from '../types/dashboard';
import type {Attack} from '../types/attack';
import axios from 'axios';
import api from '@/services/ApiClient';
import BootstrapService from '@/services/BootstrapService';

export interface CreateAttackPayload {
    start_time: string;
    pain_level: number;
    notes?: string;
    symptoms: number[];
    userSymptoms: number[];
    userSymptomsNew: string[];
    triggers: number[];
    userTriggers: number[];
    userTriggersNew: string[];
    meds: {id: number; dosage: string | null}[];
    userMeds: number[];
    userMedsDosages: Record<number, string | null>;
    userMedsNew: string[];
    userMedsNewDosages?: string[];
}

export interface UpdateAttackPayload extends Omit<CreateAttackPayload, 'start_time' | 'userMedsDosages' | 'userMedsNewDosages'> {}

const BASE_URL = '/api/v1/migraine-diary';

interface DashboardResponse {
    data: Dashboard;
}

export async function getDashboard(): Promise<Dashboard> {
    const response = await fetch(`${BASE_URL}/dashboard`, {
        headers: {
            Accept: 'application/json',
            'Accept-Language': BootstrapService.getLocale(),
        },
        credentials: 'same-origin',
    });

    if (!response.ok) {
        throw new Error('Failed to load migraine diary dashboard.');
    }

    const payload: DashboardResponse = await response.json();

    return payload.data;
}

export async function createAttack(data: CreateAttackPayload): Promise<Attack> {
    try {
        const response = await api.post<{data: Attack}>(`${BASE_URL}/attacks`, data);
        return response.data.data;
    } catch (error) {
        if (axios.isAxiosError(error)) {
            const payload = error.response?.data as {
                message?: string;
                errors?: Record<string, string[]>;
            } | undefined;
            const message = payload?.errors
                ? Object.values(payload.errors).flat()[0]
                : payload?.message;

            throw new Error(message ?? 'Failed to create migraine attack.');
        }

        throw error;
    }
}

export async function endAttack(attackId: number): Promise<Attack> {
    try {
        const response = await api.post<{data: Attack}>(`${BASE_URL}/attacks/${attackId}/end`);
        return response.data.data;
    } catch (error) {
        if (axios.isAxiosError(error)) {
            const payload = error.response?.data as {
                message?: string;
                errors?: Record<string, string[]>;
            } | undefined;
            const message = payload?.errors
                ? Object.values(payload.errors).flat()[0]
                : payload?.message;

            throw new Error(message ?? 'Failed to end migraine attack.');
        }

        throw error;
    }
}

export async function updateAttack(attackId: number, data: UpdateAttackPayload): Promise<Attack> {
    try {
        const response = await api.put<{data: Attack}>(`${BASE_URL}/attacks/${attackId}`, data);
        return response.data.data;
    } catch (error) {
        if (axios.isAxiosError(error)) {
            const payload = error.response?.data as {
                message?: string;
                errors?: Record<string, string[]>;
            } | undefined;
            const message = payload?.errors
                ? Object.values(payload.errors).flat()[0]
                : payload?.message;

            throw new Error(message ?? 'Failed to update migraine attack.');
        }

        throw error;
    }
}

export async function getTranslations(): Promise<Record<string, string>> {
    const response = await fetch(`${BASE_URL}/translations`, {
        headers: {
            Accept: 'application/json',
            'Accept-Language': BootstrapService.getLocale(),
        },
        credentials: 'same-origin',
    });

    if (!response.ok) {
        throw new Error('Failed to load migraine diary translations.');
    }

    const payload = await response.json();
    return payload.data as Record<string, string>;
}
