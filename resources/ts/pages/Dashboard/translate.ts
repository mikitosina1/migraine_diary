import TranslationService from '@/services/TranslationService';
import BootstrapService from '@/services/BootstrapService';

export function dashboardT(key: string, fallback: string): string {
    const translation = TranslationService.t(`migrainediary.${key}`);
    return translation === `migrainediary.${key}` ? fallback : translation;
}

export function dashboardLocale(): string {
    return BootstrapService.getLocale();
}
