import Image from 'next/image';
import Link from 'next/link';

interface CardProps {
  title?: string;
  content: string;
  icon?: string;
  actionButton?: {
    primary?: { label: string; href: string };
    secondary?: { label: string; href: string };
  };
  className?: string;
}

export default function CardComponent({ title, content, icon, actionButton, className }: CardProps) {
  return (
    <div className={`card overflow-hidden ${className ?? ''}`}>
      <div className="relative p-7 md:p-10">
        <div className="pointer-events-none absolute -right-10 -top-10 h-56 w-56 rounded-full bg-[rgba(184,123,164,0.25)] blur-3xl" />
        <div className="pointer-events-none absolute -bottom-10 -left-10 h-56 w-56 rounded-full bg-[rgba(119,45,99,0.14)] blur-3xl" />

        {icon && (
          <div className="pointer-events-none absolute bottom-0 right-0 w-[50%] select-none opacity-10">
            <Image src={icon} alt="" aria-hidden="true" width={0} height={0} sizes="100vw" className="h-auto w-full" />
          </div>
        )}

        <div className="relative">
          {title && <h2 className="font-display text-3xl font-bold tracking-tight md:text-4xl">{title}</h2>}
          <p className="font-body mt-3 max-w-2xl text-[rgba(65,27,60,0.80)]">{content}</p>

          {(actionButton?.primary ?? actionButton?.secondary) && (
            <div className="mt-6 flex flex-wrap gap-3">
              {actionButton?.primary && (
                <Link className="btn btn-primary" href={actionButton.primary.href}>
                  {actionButton.primary.label} <span aria-hidden="true">›</span>
                </Link>
              )}
              {actionButton?.secondary && (
                <Link className="btn btn-secondary" href={actionButton.secondary.href}>
                  {actionButton.secondary.label} <span aria-hidden="true">›</span>
                </Link>
              )}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
