import { CardComponent, Chip, FeatureItemComponent, SectionDivider } from '@components/ui';
import { Calendar, Package, Sparkles, Truck } from 'lucide-react';

export default function HomePage() {
  return (
    <>
      <section className="sparkle">
        <div className="mx-auto w-full max-w-6xl px-4 pb-16 pt-10 md:pb-26 md:pt-14">
          <div className="relative flex items-end justify-between">
            <div className="flex-2 flex flex-col gap-4 items-center md:items-start md:gap-14 md:bg-[url('/home-linge.webp')] md:[background-size:40%] md:[background-position:right_bottom] md:[background-repeat:no-repeat]">
              <div className="flex flex-col items-center md:items-start gap-2 md:max-w-[955px]">
                <Chip
                  label="Linge collecté le matin • Retour sous 24/48h"
                  variant="default"
                  outline
                  size="lg"
                  className="w-[fit-content]"
                />
                <h1 className="font-display text-4xl text-center font-bold leading-tight tracking-tight md:text-start md:text-5xl">
                  Blanchisserie avec collecte et livraison en entreprise sous 48h !
                </h1>

                <span className="font-body text-base text-center leading-relaxed text-[rgba(65,27,60,0.82)] md:text-start md:text-lg md:max-w-[680px]">
                  Nous récupérons votre linge directement dans vos locaux, le traitons dans notre atelier et vous le
                  livrons propre, repassé et plié en 24 à 48h.
                </span>
              </div>

              <div className="flex flex-wrap gap-3">
                <a className="btn btn-primary" href="/services">
                  Nos services <span aria-hidden="true">›</span>
                </a>
                <a className="btn btn-secondary" href="/tarifs">
                  Voir nos tarifs <span aria-hidden="true">›</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <SectionDivider className="text-[var(--accent)]" />

      <section className="flex-wrap gap-4 mx-auto w-full max-w-6xl px-4 pb-10 md:pb-14 flex mt-[-150px]">
        <CardComponent
          title="Gain de temps"
          content="Collecte et livraison à votre bureau. Plus besoin de se déplacer au pressing."
          icon="/icons/save_time.webp"
          className="flex-1 text-center flex justify-center items-center basis-xs"
        />
        <CardComponent
          title="Qualité garantie"
          content="Traitement premium par des professionnels expérimentés avec garantie satisfaction."
          icon="/icons/guarantees.webp"
          className="flex-1 text-center flex justify-center items-center basis-xs"
        />
        <CardComponent
          title="Tarifs transparents"
          content="Forfaits adaptés aux besoins avec -50% de crédit d'impôt."
          icon="/icons/rates.webp"
          className="flex-1 text-center flex justify-center items-center basis-xs"
        />
      </section>

      <section className="mx-auto w-full max-w-6xl px-4 pt-8 pb-26 flex flex-col gap-10">
        <div className="flex flex-col gap-4">
          <h2 className="font-display text-center text-3xl font-bold tracking-tight md:text-4xl">
            Comment ça marche ?
          </h2>

          <div className="font-body text-base text-center leading-relaxed text-[rgba(65,27,60,0.82)] md:text-lg">
            Un processus simple en 4 étapes pour un service sans effort
          </div>
        </div>

        <div className="flex flex-col gap-4 items-center max-w-[776px] mx-auto items-start">
          <FeatureItemComponent
            icon={Calendar}
            title="Planification de votre collecte"
            text="Vous nous contactez afin de planifier la collecte de votre linge."
          />

          <FeatureItemComponent
            icon={Package}
            title="Collecte de votre linge"
            text="Nous récupérons votre linge directement sur votre lieu de travail aux horaires convenus."
          />

          <FeatureItemComponent
            icon={Sparkles}
            title="Service premium"
            text="Lavage, repassage et pressing par nos équipes qualifiées avec produits haut de gamme."
          />

          <FeatureItemComponent
            icon={Truck}
            title="Livraison rapide"
            text="Retour de votre linge propre et repassé sous 24 à 48h à votre bureau !"
          />
        </div>
      </section>
    </>
  );
}
