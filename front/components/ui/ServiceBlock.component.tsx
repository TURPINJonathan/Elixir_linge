import Image from 'next/image';

export type ServiceBlockImagePosition = 'left' | 'right';

export interface ServiceBlockProps {
  imageSrc: string;
  imageAlt: string;
  title: string;
  firstContent: string;
  secondContent: string;
  imagePosition: ServiceBlockImagePosition;
}

export default function ServiceBlockComponent({
  imageSrc,
  imageAlt,
  title,
  firstContent,
  secondContent,
  imagePosition,
}: ServiceBlockProps) {
  const isImageLeft = imagePosition === 'left';

  const textBlock = (
    <div
      className={`flex-1 flex flex-col gap-2 justify-center items-center basis-[300px] ${
        isImageLeft ? 'md:order-2 md:items-start' : 'md:items-end'
      }`}
    >
      <h2
        className={`font-display text-2xl font-bold border-b border-[#411B3C] w-full ${isImageLeft ? 'md:text-start text-center' : 'md:text-end text-center'}`}
      >
        {title}
      </h2>

      <p
        className={`font-body text-base text-center leading-relaxed text-[rgba(65,27,60,0.82)] ${
          isImageLeft ? 'md:text-start md:pr-10' : 'md:text-end md:pl-10'
        }`}
      >
        {firstContent}
      </p>

      <p
        className={`font-body text-base text-center leading-relaxed text-[rgba(65,27,60,0.82)] ${
          isImageLeft ? 'md:text-start md:pr-10' : 'md:text-end md:pl-10'
        }`}
      >
        {secondContent}
      </p>
    </div>
  );

  const imageBlock = (
    <div
      className={`flex-1 basis-[300px] flex justify-center items-center ${
        isImageLeft ? 'order-1 md:justify-end' : 'md:justify-start'
      }`}
    >
      <Image
        src={imageSrc}
        alt={imageAlt}
        width={250}
        height={250}
        className="object-cover rounded-[var(--radius-card)] shadow-2xl"
      />
    </div>
  );

  return (
    <div className="flex flex-wrap items-center gap-4 md:gap-10 justify-center">
      {isImageLeft ? imageBlock : textBlock}
      {isImageLeft ? textBlock : imageBlock}
    </div>
  );
}
