import type { Metadata } from 'next';
import './global.scss';
import { Inter, Playfair_Display } from 'next/font/google';
import { Footer, Header } from '@components/layout';

const inter = Inter({
  subsets: ['latin'],
  variable: '--font-body',
  display: 'swap',
});

const playfair = Playfair_Display({
  subsets: ['latin'],
  variable: '--font-display',
  display: 'swap',
});

export const metadata: Metadata = {
  title: 'Elixir Linge — Repassage & pliage pour salariés',
  description:
    'Service de repassage & pliage pour salariés : dépôt en entreprise, collecte le matin, retour en 24/48h.',
  metadataBase: new URL('https://example.com'),
  openGraph: {
    title: 'Elixir Linge',
    description: 'Dépôt en entreprise • Collecte le matin • Retour en 24/48h • Repassé & plié',
    type: 'website',
  },
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="fr" className={`${inter.variable} ${playfair.variable}`}>
      <body className="font-body">
        <Header />
        <main className="paper-grain min-h-screen bg-[var(--bg)] text-[var(--text)]">{children}</main>
        <Footer />
      </body>
    </html>
  );
}
