import { page } from '@inertiajs/svelte';

/**
 * Timezone label map for common Indonesian timezones.
 */
const TIMEZONE_LABELS: Record<string, string> = {
    'Asia/Makassar': 'WITA',
    'Asia/Jakarta': 'WIB',
    'Asia/Jayapura': 'WIT',
};

/**
 * Get the server timezone abbreviation label (e.g., 'WITA').
 */
export function getTimezoneLabel(): string {
    const tz = (page.props.serverTimezone as string) || 'Asia/Makassar';

    return TIMEZONE_LABELS[tz] || tz;
}

/**
 * Calculate the offset (in ms) between server time and client time.
 * A positive offset means the server is ahead of the client.
 *
 * Usage: `const serverNow = Date.now() + getServerOffset()`
 */
let cachedOffset: number | null = null;

export function getServerOffset(): number {
    if (cachedOffset !== null) {
        return cachedOffset;
    }

    const serverTimeStr = page.props.serverTime as string | undefined;

    if (!serverTimeStr) {
        return 0;
    }

    const serverMs = new Date(serverTimeStr).getTime();
    // page.props is set when the page was received, so use the page load timestamp
    // We approximate by using the current time since the offset is small
    const clientMs = Date.now();

    cachedOffset = serverMs - clientMs;

    return cachedOffset;
}

/**
 * Get the current server time as a Date (corrected for client clock drift).
 */
export function getServerNow(): Date {
    return new Date(Date.now() + getServerOffset());
}

/**
 * Format a datetime string for display.
 * E.g., "25 Juli 2026, 09:00 WITA"
 */
export function formatDateTime(isoString: string | null | undefined): string {
    if (!isoString) {
        return 'Belum ditentukan';
    }

    const date = new Date(isoString);
    const tz = (page.props.serverTimezone as string) || 'Asia/Makassar';

    const formatted = date.toLocaleString('id-ID', {
        dateStyle: 'long',
        timeStyle: 'short',
        timeZone: tz,
    });

    return `${formatted} ${getTimezoneLabel()}`;
}

/**
 * Format only the time portion.
 * E.g., "09:00"
 */
export function formatTime(isoString: string | null | undefined): string {
    if (!isoString) {
        return '-';
    }

    const date = new Date(isoString);
    const tz = (page.props.serverTimezone as string) || 'Asia/Makassar';

    return date.toLocaleString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        timeZone: tz,
    });
}

/**
 * Format only the date portion.
 * E.g., "25 Juli 2026"
 */
export function formatDate(isoString: string | null | undefined): string {
    if (!isoString) {
        return '-';
    }

    const date = new Date(isoString);
    const tz = (page.props.serverTimezone as string) || 'Asia/Makassar';

    return date.toLocaleString('id-ID', {
        dateStyle: 'long',
        timeZone: tz,
    });
}

/**
 * Format a date as relative time string with timezone-aware display.
 * E.g., "25 Jul 2026"
 */
export function formatDateShort(isoString: string | null | undefined): string {
    if (!isoString) {
        return '-';
    }

    const date = new Date(isoString);
    const tz = (page.props.serverTimezone as string) || 'Asia/Makassar';

    return date.toLocaleString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        timeZone: tz,
    });
}
