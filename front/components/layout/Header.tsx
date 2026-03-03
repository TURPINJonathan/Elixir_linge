'use client';

import Image from 'next/image';
import Link from 'next/link';
import { useEffect, useState } from 'react';
import Logo from '../../public/logo.webp';

const NAV_LINKS = [
  { href: '/services', label: 'Services' },
  { href: '/tarifs', label: 'Tarifs' },
  { href: '/medias', label: 'Médias' },
  { href: '/a-propos', label: 'À propos' },
];

export default function Header() {
  const [menuOpen, setMenuOpen] = useState(false);

  useEffect(() => {
    const onKey = (e: KeyboardEvent) => {
      if (e.key === 'Escape') setMenuOpen(false);
    };
    document.addEventListener('keydown', onKey);
    return () => document.removeEventListener('keydown', onKey);
  }, []);

  const close = () => setMenuOpen(false);

  return (
    <>
      {menuOpen && <div className="fixed inset-0 z-40 md:hidden" aria-hidden="true" onClick={close} />}

      <div className="sticky top-0 z-50">
        <header className="border-b border-[rgba(65,27,60,0.10)] bg-[rgba(255,255,255,0.55)] backdrop-blur-md">
          <div className="mx-auto flex h-[72px] w-full max-w-6xl items-center justify-between px-4">
            <Link
              href="/"
              onClick={close}
              className="font-display h-full flex items-center justify-center tracking-tight"
            >
              <Image
                src={Logo}
                alt="Elixir Linge"
                width={250}
                height={32}
                className="h-auto max-h-[inherit]"
                style={{ marginBottom: '-10px' }}
              />
            </Link>

            <nav className="hidden items-center gap-7 md:flex" aria-label="Navigation principale">
              {NAV_LINKS.map(({ href, label }) => (
                <Link
                  key={href}
                  href={href}
                  className="text-sm font-semibold text-[rgba(65,27,60,0.85)] hover:text-[var(--text)]"
                >
                  {label}
                </Link>
              ))}
            </nav>

            <Link className="hidden btn btn-primary  text-sm md:inline-flex" href="/contact">
              Contactez-nous
              <span aria-hidden="true">→</span>
            </Link>

            <button
              type="button"
              className="flex h-10 w-10 flex-col items-center justify-center gap-[5px] rounded-full text-[var(--text)] transition-colors hover:bg-[rgba(65,27,60,0.06)] md:hidden"
              onClick={() => setMenuOpen((v) => !v)}
              aria-expanded={menuOpen}
              aria-controls="mobile-nav"
              aria-label={menuOpen ? 'Fermer le menu' : 'Ouvrir le menu'}
            >
              <span
                className={`block h-[2px] w-5 origin-center rounded-full bg-current transition-all duration-200 ${menuOpen ? 'translate-y-[7px] rotate-45' : ''}`}
              />
              <span
                className={`block h-[2px] w-5 rounded-full bg-current transition-all duration-200 ${menuOpen ? 'scale-x-0 opacity-0' : ''}`}
              />
              <span
                className={`block h-[2px] w-5 origin-center rounded-full bg-current transition-all duration-200 ${menuOpen ? '-translate-y-[7px] -rotate-45' : ''}`}
              />
            </button>
          </div>
        </header>

        <div
          id="mobile-nav"
          aria-label="Menu mobile"
          className={`overflow-hidden rounded-bl-[32px] border-b border-[rgba(65,27,60,0.08)] bg-[rgba(255,255,255,0.92)] absolute right-0 backdrop-blur-md transition-all duration-300 ease-in-out md:hidden ${
            menuOpen ? 'max-h-[500px] opacity-100' : 'max-h-0 opacity-0'
          }`}
          style={{ boxShadow: '0 10px 30px rgba(65,27,60,0.4)' }}
        >
          <nav className="mx-auto flex w-full max-w-6xl flex-col px-4 pt-3 pb-4">
            {NAV_LINKS.map(({ href, label }) => (
              <Link
                key={href}
                href={href}
                onClick={close}
                className="flex items-center border-b border-[rgba(65,27,60,0.07)] py-3.5 text-base font-semibold text-[rgba(65,27,60,0.85)] last:border-0 hover:text-[var(--text)]"
              >
                {label}
              </Link>
            ))}

            <div className="pt-4">
              <Link href="/contact" onClick={close} className="btn btn-primary w-full justify-center">
                Contactez-nous
                <span aria-hidden="true">→</span>
              </Link>
            </div>
          </nav>
        </div>
      </div>
    </>
  );
}
