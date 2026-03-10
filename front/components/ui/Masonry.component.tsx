'use client';

import React, { useCallback, useEffect, useLayoutEffect, useMemo, useRef, useState } from 'react';
import { gsap } from 'gsap';

const useMeasure = <T extends HTMLElement>() => {
  const ref = useRef<T | null>(null);
  const [size, setSize] = useState({ width: 0, height: 0 });

  useLayoutEffect(() => {
    if (!ref.current) return;
    const ro = new ResizeObserver(([entry]) => {
      const { width, height } = entry.contentRect;
      setSize({ width, height });
    });
    ro.observe(ref.current);
    return () => ro.disconnect();
  }, []);

  return [ref, size] as const;
};

export type ItemDimensions = Record<string, { w: number; h: number }>;

function loadImageDimensions(
  items: { id: string | number; img: string }[]
): Promise<ItemDimensions> {
  return new Promise(resolve => {
    const result: ItemDimensions = {};
    let pending = items.length;
    if (pending === 0) {
      resolve(result);
      return;
    }
    const onDone = () => {
      pending--;
      if (pending === 0) resolve(result);
    };
    items.forEach(item => {
      const img = new Image();
      img.onload = () => {
        result[String(item.id)] = { w: img.naturalWidth, h: img.naturalHeight };
        onDone();
      };
      img.onerror = onDone;
      img.src = item.img;
    });
  });
}

export interface MasonryItem {
  id: string | number;
  img: string;
  url?: string;
  /** Hauteur en px pour le layout. Si absent, dérivée de la largeur de colonne (ratio 4/3). */
  height?: number;
}

interface GridItem extends MasonryItem {
  x: number;
  y: number;
  w: number;
  h: number;
}

const DEFAULT_ASPECT_RATIO = 4 / 3;
/** Hauteur de ligne fixe ; la largeur de chaque cellule varie selon le ratio de l’image. */
const ROW_HEIGHT = 240;

interface MasonryProps {
  items: MasonryItem[];
  ease?: string;
  duration?: number;
  stagger?: number;
  animateFrom?: 'bottom' | 'top' | 'left' | 'right' | 'center' | 'random';
  scaleOnHover?: boolean;
  hoverScale?: number;
  blurToFocus?: boolean;
  colorShiftOnHover?: boolean;
  /** Si fourni, appelé au clic au lieu d’ouvrir `url`. */
  onItemClick?: (item: MasonryItem) => void;
}

const Masonry: React.FC<MasonryProps> = ({
  items,
  ease = 'power3.out',
  duration = 0.6,
  stagger = 0.05,
  animateFrom = 'bottom',
  scaleOnHover = true,
  hoverScale = 0.95,
  blurToFocus = true,
  colorShiftOnHover = false,
  onItemClick
}) => {
  const [containerRef, { width }] = useMeasure<HTMLDivElement>();
  const [dimensions, setDimensions] = useState<ItemDimensions>({});
  const imagesReady = Object.keys(dimensions).length === items.length && items.length > 0;

  const getInitialPosition = useCallback(
    (item: GridItem) => {
      const containerRect = containerRef.current?.getBoundingClientRect();
      if (!containerRect) return { x: item.x, y: item.y };

      let direction = animateFrom;
      if (animateFrom === 'random') {
        const dirs = ['top', 'bottom', 'left', 'right'];
        direction = dirs[Math.floor(Math.random() * dirs.length)] as typeof animateFrom;
      }

      switch (direction) {
        case 'top':
          return { x: item.x, y: -200 };
        case 'bottom':
          return { x: item.x, y: window.innerHeight + 200 };
        case 'left':
          return { x: -200, y: item.y };
        case 'right':
          return { x: window.innerWidth + 200, y: item.y };
        case 'center':
          return {
            x: containerRect.width / 2 - item.w / 2,
            y: containerRect.height / 2 - item.h / 2
          };
        default:
          return { x: item.x, y: item.y + 100 };
      }
    },
    [animateFrom, containerRef]
  );

  useEffect(() => {
    loadImageDimensions(items).then(setDimensions);
  }, [items]);

  const grid = useMemo<GridItem[]>(() => {
    if (!width) return [];
    const gap = 16;
    const result: GridItem[] = [];
    let rowY = 0;
    let currentX = 0;

    items.forEach(child => {
      const dim = dimensions[String(child.id)];
      const w =
        child.height != null
          ? (child.height / 2) * DEFAULT_ASPECT_RATIO
          : dim
            ? ROW_HEIGHT * (dim.w / dim.h)
            : ROW_HEIGHT * DEFAULT_ASPECT_RATIO;

      if (currentX + w + gap > width && result.length > 0) {
        rowY += ROW_HEIGHT + gap;
        currentX = 0;
      }
      result.push({ ...child, x: currentX, y: rowY, w, h: ROW_HEIGHT });
      currentX += w + gap;
    });

    if (result.length === 0) return result;

    const rowsByY = new Map<number, GridItem[]>();
    result.forEach(item => {
      const row = rowsByY.get(item.y) ?? [];
      row.push(item);
      rowsByY.set(item.y, row);
    });

    rowsByY.forEach(rowItems => {
      const first = rowItems[0];
      const last = rowItems[rowItems.length - 1];
      const rowWidth = last.x + last.w - first.x;
      const offsetX = (width - rowWidth) / 2;
      rowItems.forEach(item => {
        item.x += offsetX;
      });
    });

    return result;
  }, [items, width, dimensions]);

  const totalHeight =
    grid.length > 0 ? Math.max(...grid.map(i => i.y + i.h)) + 16 : 400;

  const hasMounted = useRef(false);

  useLayoutEffect(() => {
    if (!imagesReady) return;

    grid.forEach((item, index) => {
      const selector = `[data-key="${item.id}"]`;
      const animProps = { x: item.x, y: item.y, width: item.w, height: item.h };

      if (!hasMounted.current) {
        const start = getInitialPosition(item);
        gsap.fromTo(
          selector,
          {
            opacity: 0,
            x: start.x,
            y: start.y,
            width: item.w,
            height: item.h,
            ...(blurToFocus && { filter: 'blur(10px)' })
          },
          {
            opacity: 1,
            ...animProps,
            ...(blurToFocus && { filter: 'blur(0px)' }),
            duration: 0.8,
            ease: 'power3.out',
            delay: index * stagger
          }
        );
      } else {
        gsap.to(selector, {
          ...animProps,
          duration,
          ease,
          overwrite: 'auto'
        });
      }
    });

    hasMounted.current = true;
  }, [grid, imagesReady, stagger, getInitialPosition, blurToFocus, duration, ease]);

  const handleMouseEnter = (id: string, element: HTMLElement) => {
    if (scaleOnHover) {
      gsap.to(`[data-key="${id}"]`, {
        scale: hoverScale,
        duration: 0.3,
        ease: 'power2.out'
      });
    }
    if (colorShiftOnHover) {
      const overlay = element.querySelector('.color-overlay') as HTMLElement;
      if (overlay) gsap.to(overlay, { opacity: 0.3, duration: 0.3 });
    }
  };

  const handleMouseLeave = (id: string, element: HTMLElement) => {
    if (scaleOnHover) {
      gsap.to(`[data-key="${id}"]`, {
        scale: 1,
        duration: 0.3,
        ease: 'power2.out'
      });
    }
    if (colorShiftOnHover) {
      const overlay = element.querySelector('.color-overlay') as HTMLElement;
      if (overlay) gsap.to(overlay, { opacity: 0, duration: 0.3 });
    }
  };

  const handleClick = (item: GridItem) => {
    if (onItemClick) {
      onItemClick(item);
    } else if (item.url) {
      window.open(item.url, '_blank', 'noopener');
    }
  };

  return (
    <div
      ref={containerRef}
      className="relative w-full"
      style={{ minHeight: totalHeight }}
    >
      {grid.map(item => (
        <div
          key={item.id}
          data-key={item.id}
          className="absolute box-content cursor-pointer"
          style={{ willChange: 'transform, width, height, opacity' }}
          onClick={() => handleClick(item)}
          onMouseEnter={e => handleMouseEnter(String(item.id), e.currentTarget)}
          onMouseLeave={e => handleMouseLeave(String(item.id), e.currentTarget)}
        >
          <div
            className="relative w-full h-full rounded-[var(--radius-card)] shadow-2xl"
            style={{
              backgroundImage: `url(${item.img})`,
              backgroundSize: 'contain',
              backgroundPosition: 'center',
              backgroundRepeat: 'no-repeat',
            }}
          >
            {colorShiftOnHover && (
              <div className="color-overlay absolute inset-0 rounded-[var(--radius-card)] bg-gradient-to-br from-[#411B3C] to-[#772D63] opacity-0 pointer-events-none" />
            )}
          </div>
        </div>
      ))}
    </div>
  );
};

export default Masonry;
