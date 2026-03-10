import { LucideIcon } from 'lucide-react';

interface FeatureItemProps {
  icon: LucideIcon;
  title: string;
  text: string;
}

export default function FeatureItemComponent({ icon: Icon, title, text }: FeatureItemProps) {
  return (
    <div className="flex gap-8 items-center">
      <div
        className="step-icon btn-primary rounded-xl flex items-center justify-center"
        style={{
          width: '75px',
          height: '75px',
          backgroundColor: 'var(--text)',
        }}
      >
        <Icon size={32} />
      </div>

      <div className="flex-1 flex flex-col justify-center">
        <h3 className="font-display text-xl font-bold tracking-tight">{title}</h3>

        <p className="font-body text-base leading-relaxed text-[rgba(65,27,60,0.82)]">{text}</p>
      </div>
    </div>
  );
}
