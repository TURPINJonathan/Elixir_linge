'use client';

import { ICON_SIZE } from '@constants';
import { ColorVariant, SizeVariant } from '@types';
import { type ReactNode } from 'react';

export interface ChipProps {
  label: string;
  icon?: ReactNode;
  trailingIcon?: ReactNode;
  variant?: ColorVariant;
  outline?: boolean;
  dense?: boolean;
  size?: SizeVariant;
  disabled?: boolean;
  onClick?: () => void;
  onDelete?: () => void;
  href?: string;
  dot?: boolean;
  elevated?: boolean;
  className?: string;
}

function CloseIcon({ size }: { size: number }) {
  return (
    <svg width={size} height={size} viewBox="0 0 10 10" fill="none" aria-hidden="true">
      <path d="M2 2L8 8M8 2L2 8" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" />
    </svg>
  );
}

function buildCn({
  variant = 'default',
  size = 'md',
  outline,
  dense,
  disabled,
  elevated,
  interactive,
  className,
}: {
  variant?: ColorVariant;
  size?: SizeVariant;
  outline?: boolean;
  dense?: boolean;
  disabled?: boolean;
  elevated?: boolean;
  interactive?: boolean;
  className?: string;
}): string {
  return [
    'chip',
    `chip--${variant}`,
    size !== 'md' && `chip--${size}`,
    outline && 'chip--outline',
    dense && 'chip--dense',
    disabled && 'chip--disabled',
    elevated && 'chip--elevated',
    interactive && 'chip--interactive',
    className,
  ]
    .filter(Boolean)
    .join(' ');
}

export default function Chip({
  label,
  icon,
  trailingIcon,
  variant = 'default',
  outline = false,
  dense = false,
  size = 'md',
  disabled = false,
  onClick,
  onDelete,
  href,
  dot = false,
  elevated = false,
  className,
}: ChipProps) {
  const interactive = Boolean(href ?? onClick);
  const iSize = ICON_SIZE[size];

  const cn = buildCn({ variant, size, outline, dense, disabled, elevated, interactive, className });

  const inner = (
    <>
      {dot && <span className="chip__dot" aria-hidden="true" />}

      {icon && <span className="chip__icon">{icon}</span>}

      <span className="chip__label">{label}</span>

      {trailingIcon && !onDelete && <span className="chip__icon">{trailingIcon}</span>}

      {onDelete && (
        <button
          type="button"
          className="chip__delete"
          disabled={disabled}
          aria-label={`Supprimer ${label}`}
          onClick={(e) => {
            e.stopPropagation();
            onDelete();
          }}
        >
          <CloseIcon size={iSize - 2} />
        </button>
      )}
    </>
  );

  if (href) {
    return (
      <a href={href} className={cn} aria-disabled={disabled || undefined}>
        {inner}
      </a>
    );
  }

  if (onClick) {
    return (
      <button type="button" className={cn} disabled={disabled} onClick={onClick}>
        {inner}
      </button>
    );
  }

  return (
    <span className={cn} aria-disabled={disabled || undefined}>
      {inner}
    </span>
  );
}
