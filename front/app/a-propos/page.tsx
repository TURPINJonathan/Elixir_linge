import Image from "next/image";

export default function AboutPage() {
  return (
    <>
      <section>
        <div className="mx-auto flex w-full max-w-6xl flex-col items-center gap-3 px-4 pb-12 pt-10 text-center md:pb-14 md:pt-14">
          <h1 className="font-display text-4xl font-bold leading-tight tracking-tight text-[var(--text)] md:text-5xl">
            À propos de nous
          </h1>

          <p className="mx-auto max-w-2xl font-body text-base leading-relaxed text-[rgba(65,27,60,0.82)] md:text-lg">
            Des formules simples, pensées pour s’adapter à votre volume de linge et à votre rythme.
          </p>
        </div>
      </section>

      <section>
        <div className="mx-auto flex w-full max-w-6xl flex-row flex-wrap items-center justify-center gap-3 px-4 text-center">
          <div className="flex-1 basis-[350px]">
            <p>Ici il me faudrait ton texte</p>
            <p>C'est une page "A propos" donc ca doit parler de toi, ton entreprise, l'adn etc</p>
          </div>
          <div className="flex-1 flex-none basis-[350px] flex justify-center items-center">
            <Image
              src='/about_lady.webp'
              alt="Illustration d'une dame faisant un signe de la main"
              width={350}
              height={250}
              className="object-cover"
            />
          </div>
        </div>
      </section>


    </>
  );
}
