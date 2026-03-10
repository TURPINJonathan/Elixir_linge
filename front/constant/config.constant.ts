export const DEFAULT_LOCALE = process.env.NEXT_PUBLIC_DEFAULT_LOCALE ?? 'fr-FR';
export const DEFAULT_TIME_ZONE = process.env.NEXT_PUBLIC_DEFAULT_TIME_ZONE ?? 'Europe/Paris';

/** URL de base du backend (ex. http://localhost:8000). À définir via NEXT_PUBLIC_API_BASE_URL en prod. */
export const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL ?? 'http://localhost:8000';
