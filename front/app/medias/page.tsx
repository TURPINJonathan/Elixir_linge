'use client';

import { MasonryComponent } from '@/components';
import type { MasonryItem } from '@/components/ui/Masonry.component';
import { API_BASE_URL } from '@constants';
import Image from 'next/image';
import { useEffect, useMemo, useState } from 'react';

interface GalleryItem {
  id: number;
  alt: string | null;
  originalName: string;
  createdAt: string;
  hasThumbnail: boolean;
  mimeType: string;
}

function imageUrl(id: number): string {
  return `${API_BASE_URL}/api/public/media/${id}/image`;
}

function galleryToMasonryItems(items: GalleryItem[]): MasonryItem[] {
  return items.map((item) => ({
    id: item.id,
    img: imageUrl(item.id),
  }));
}

export default function MediasPage() {
  const [items, setItems] = useState<GalleryItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [lightboxId, setLightboxId] = useState<number | null>(null);

  const masonryItems = useMemo(() => galleryToMasonryItems(items), [items]);

  useEffect(() => {
    let cancelled = false;
    fetch(`${API_BASE_URL}/api/public/gallery`)
      .then((res) => res.json())
      .then((data: { items: GalleryItem[] }) => {
        if (cancelled) return;
        const images = (data.items ?? []).filter((m) =>
          m.mimeType.startsWith('image/'),
        );
        setItems(images);
      })
      .catch(() => {
        if (!cancelled) setItems([]);
      })
      .finally(() => {
        if (!cancelled) setLoading(false);
      });
    return () => {
      cancelled = true;
    };
  }, []);

  useEffect(() => {
    if (lightboxId === null) return;
    const onKey = (e: KeyboardEvent) => {
      if (e.key === 'Escape') setLightboxId(null);
    };
    document.addEventListener('keydown', onKey);
    document.body.style.overflow = 'hidden';
    return () => {
      document.removeEventListener('keydown', onKey);
      document.body.style.overflow = '';
    };
  }, [lightboxId]);

  return (
    <>
      <section>
        <div className="mx-auto flex w-full max-w-6xl flex-col items-center gap-3 px-4 pb-12 pt-10 text-center md:pb-14 md:pt-14">
          <h1 className="font-display text-4xl font-bold leading-tight tracking-tight text-[var(--text)] md:text-5xl">
            Découvrez notre travail en image !
          </h1>
          <p className="max-w-xl font-body text-base leading-relaxed text-[rgba(65,27,60,0.82)] md:text-lg">
            Quelques instants de notre quotidien et du soin que nous apportons à votre linge.
          </p>
        </div>
      </section>
      
      <section>
        <div className="mx-auto w-full max-w-6xl">
          {loading ? (
            <div className="flex min-h-[280px] items-center justify-center">
              <div
                className="h-10 w-10 animate-spin rounded-full border-2 border-[var(--accent)] border-t-transparent"
                aria-hidden
              />
            </div>
          ) : items.length === 0 ? (
            <div className="rounded-[var(--radius-card)] border border-[rgba(65,27,60,0.12)] bg-[var(--surface)] px-8 py-16 text-center">
              <p className="font-body text-[var(--text)] opacity-80">
                Aucune image à afficher pour le moment. Revenez bientôt !
              </p>
            </div>
          ) : (
            <MasonryComponent
              items={masonryItems}
              ease="sine.out"
              duration={0.6}
              stagger={0.1}
              animateFrom="random"
              scaleOnHover
              hoverScale={0.95}
              blurToFocus
              colorShiftOnHover
              onItemClick={(item) => setLightboxId(Number(item.id))}
            />
          )}
        </div>
      </section>

      {lightboxId !== null && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm"
          onClick={() => setLightboxId(null)}
          role="dialog"
          aria-modal="true"
          aria-label="Image en grand"
        >
          <button
            type="button"
            onClick={() => setLightboxId(null)}
            className="absolute right-4 top-4 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white"
            aria-label="Fermer"
          >
            <svg className="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          <Image
            src={imageUrl(lightboxId)}
            alt=""
            width={1600}
            height={1200}
            quality={75}
            className="max-h-[92vh] max-w-[92vw] rounded-[var(--radius-card)] object-contain"
            onClick={(e) => e.stopPropagation()}
            style={{ height: 'auto' }}
          />
        </div>
      )}
    </>
  );
}
