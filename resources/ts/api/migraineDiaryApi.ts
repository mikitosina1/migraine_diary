import type { Dashboard } from '../types/dashboard';

const BASE_URL = '/api/v1/migraine-diary';

export async function getDashboard(): Promise<Dashboard> {
    const response = await fetch(`${BASE_URL}/dashboard`, {
        headers: {
            Accept: 'application/json',
        },
        credentials: 'same-origin',
    });

    if (!response.ok) {
        throw new Error('Failed to load migraine diary dashboard.');
    }

    return response.json();
}