import { ServiceBlockComponent } from '@components';

export default function ServicesPage() {
  return (
    <>
      <section>
        <div className="mx-auto w-full max-w-6xl px-4 pb-16 pt-10 md:pb-26 md:pt-14 flex flex-col gap-2 items-center">
          <h1 className="font-display text-4xl text-center font-bold leading-tight tracking-tight md:text-5xl">
            Découvrez nos services !
          </h1>

          <span className="font-body text-base mx-auto text-center leading-relaxed text-[rgba(65,27,60,0.82)] md:text-lg md:max-w-[680px]">
            Tous nos services sont inclus dans nos prestations.
          </span>
        </div>
      </section>

      <section>
        <div className="mx-auto w-full max-w-6xl px-4 pb-10 md:pb-14 flex flex-col gap-10">
          <ServiceBlockComponent
            imageSrc="/services-page/service_collect.webp"
            imageAlt="Collecte et livraison"
            title="Collecte de votre linge"
            firstContent="Nous récupérons votre linge propre directement dans les locaux de votre entreprise grâce à notre service de collecte."
            secondContent="Plus besoin de vous déplacer au pressing, nous nous occupons de tout, depuis votre lieu de travail !"
            imagePosition="right"
          />

          <ServiceBlockComponent
            imageSrc="/services-page/service_pressing.webp"
            imageAlt="Service pressing"
            title="Service premium"
            firstContent="Votre linge est repassé et plié par nos équipes qualifiées avec des produits et outils permettant de garantir un résultat impeccable."
            secondContent="Que vous nous confiez du cachemire, de la soie ou du coton, nous adaptons notre traitement pour préserver la qualité de vos vêtements."
            imagePosition="left"
          />

          <ServiceBlockComponent
            imageSrc="/services-page/service_delivery.webp"
            imageAlt="Livraison rapide"
            title="Livraison rapide"
            firstContent="Nous nous engageons à vous livrer votre linge, repassé et plié en 24 à 48h après sa collecte, pour que vous puissiez en profiter au plus vite."
            secondContent="Grâce à notre logistique optimisée, nous assurons une livraison rapide et fiable directement dans sur votre lieu de travail."
            imagePosition="right"
          />
        </div>
      </section>
    </>
  );
}
