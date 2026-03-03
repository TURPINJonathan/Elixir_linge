import { DEFAULT_LOCALE, DEFAULT_TIME_ZONE } from '@constants';

export interface IDateFormatOptions {
  locale?: string;
  timeZone?: string;
}

export type MonthNameLength = 'long' | 'short' | 'narrow';

export interface IGetMonthStringOptions extends IDateFormatOptions {
  length?: MonthNameLength;
  capitalize?: boolean;
}

export function getCurrentYear(): number {
  return new Date().getFullYear();
}

export function getCurrentMonthNumber(): number {
  return new Date().getMonth() + 1;
}

export function getMonth(format: 'number', isCurrent?: boolean, month?: number): number;
export function getMonth(
  format: 'string',
  isCurrent?: boolean,
  month?: number,
  options?: IGetMonthStringOptions,
): string;
export function getMonth(
  format: 'string' | 'number',
  isCurrent: boolean = false,
  month?: number,
  options: IGetMonthStringOptions = {},
): string | number {
  const date = isCurrent ? new Date() : new Date(2000, (month ?? 1) - 1, 1);

  if (!isCurrent) {
    const normalizedMonth = month ?? 1;
    if (normalizedMonth < 1 || normalizedMonth > 12) {
      throw new RangeError('month must be between 1 and 12');
    }
  }

  if (format === 'number') return date.getMonth() + 1;

  const locale = options.locale ?? DEFAULT_LOCALE;
  const timeZone = options.timeZone ?? DEFAULT_TIME_ZONE;
  const length = options.length ?? 'long';
  const shouldCapitalize = options.capitalize ?? true;

  const raw = new Intl.DateTimeFormat(locale, { month: length, timeZone }).format(date);
  if (!raw) return raw;
  if (!shouldCapitalize) return raw;
  return raw[0].toUpperCase() + raw.slice(1);
}
