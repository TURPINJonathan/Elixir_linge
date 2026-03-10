import { SectionDivider } from '@components';

interface ApiRate {
  id: number;
  size: string;
  description: string;
  rate?: number | string | null;
  reduced_rate?: number | string | null;
  rate_after_tax_reduction?: number | string | null;
  created_at?: string;
  updated_at?: string | null;
  isOnQuotation?: boolean | null;

  reducedRate?: number | string | null;
  rateAfterTaxReduction?: number | string | null;
  createdAt?: string;
  updatedAt?: string | null;
  onQuotation?: boolean;

  isMaster?: boolean;
  isRecommended?: boolean;
}

const mockRates: ApiRate[] = [
  {
    id: 1,
    size: 'XS',
    description: '5 pièces',
    rate: 18,
    reduced_rate: 17.5,
    rate_after_tax_reduction: 9,
    isMaster: true,
    isRecommended: false,
    isOnQuotation: false,
    created_at: '2025-01-01T00:00:00Z',
    updated_at: null,
  },
  {
    id: 2,
    size: 'S',
    description: '10 - 15 pièces',
    rate: 35,
    reduced_rate: 17.5,
    rate_after_tax_reduction: 17.5,
    isMaster: true,
    isRecommended: true,
    isOnQuotation: false,
    created_at: '2025-01-01T00:00:00Z',
    updated_at: null,
  },
  {
    id: 3,
    size: 'M',
    description: '20 - 25 pièces',
    rate: 60,
    reduced_rate: 30,
    rate_after_tax_reduction: 30,
    isMaster: true,
    isRecommended: false,
    isOnQuotation: false,
    created_at: '2025-01-01T00:00:00Z',
    updated_at: null,
  },
  {
    id: 4,
    size: 'L',
    description: '30 - 35 pièces',
    rate: 85,
    reduced_rate: 42.5,
    rate_after_tax_reduction: 42.5,
    isMaster: false,
    isRecommended: false,
    isOnQuotation: false,
    created_at: '2025-01-01T00:00:00Z',
    updated_at: null,
  },
  {
    id: 5,
    size: 'XL',
    description: '40 - 45 pièces',
    rate: 105,
    reduced_rate: 52.5,
    rate_after_tax_reduction: 52.5,
    isMaster: false,
    isRecommended: false,
    isOnQuotation: false,
    created_at: '2025-01-01T00:00:00Z',
    updated_at: null,
  },
  {
    id: 6,
    size: 'XXL',
    description: '50 - 60 pièces',
    rate: 130,
    reduced_rate: 65,
    rate_after_tax_reduction: 65,
    isMaster: false,
    isRecommended: false,
    isOnQuotation: false,
    created_at: '2025-01-01T00:00:00Z',
    updated_at: null,
  },
  {
    id: 7,
    size: 'Sur mesure',
    description: 'Volume important, sur devis selon les besoins.',
    rate: null,
    reduced_rate: null,
    rate_after_tax_reduction: null,
    isMaster: false,
    isRecommended: false,
    isOnQuotation: true,
    created_at: '2025-01-01T00:00:00Z',
    updated_at: null,
  },
];

function toNumber(value: number | string | null | undefined): number | null {
  if (value === null || value === undefined || value === '') return null;

  const parsed = typeof value === 'number' ? value : Number(String(value).replace(',', '.'));

  return Number.isNaN(parsed) ? null : parsed;
}

function formatPrice(value: number | null) {
  if (value === null) return 'Sur devis';

  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
    minimumFractionDigits: value % 1 === 0 ? 0 : 2,
    maximumFractionDigits: 2,
  }).format(value);
}

function getRateValue(rate: ApiRate): number | null {
  return toNumber(rate.rate);
}

function getReducedRateValue(rate: ApiRate): number | null {
  return toNumber(rate.reduced_rate ?? rate.reducedRate);
}

function getRateAfterTaxReductionValue(rate: ApiRate): number | null {
  return toNumber(rate.rate_after_tax_reduction ?? rate.rateAfterTaxReduction);
}

function getDisplayFinalPrice(rate: ApiRate): number | null {
  return getRateAfterTaxReductionValue(rate) ?? getReducedRateValue(rate);
}

function isOnQuotation(rate: ApiRate): boolean {
  return Boolean(rate.isOnQuotation ?? rate.onQuotation);
}

function isMaster(rate: ApiRate): boolean {
  return Boolean(rate.isMaster);
}

function isRecommended(rate: ApiRate): boolean {
  return Boolean(rate.isRecommended);
}

export default function PricingPage() {
  const masterRates =
    mockRates.filter((rate) => isMaster(rate)).slice(0, 3).length > 0
      ? mockRates.filter((rate) => isMaster(rate)).slice(0, 3)
      : mockRates.filter((rate) => !isOnQuotation(rate)).slice(0, 3);

  const sortedRates = [...mockRates].sort((a, b) => {
    if (isOnQuotation(a) && !isOnQuotation(b)) return 1;
    if (!isOnQuotation(a) && isOnQuotation(b)) return -1;

    const aPrice = getRateValue(a) ?? Number.MAX_SAFE_INTEGER;
    const bPrice = getRateValue(b) ?? Number.MAX_SAFE_INTEGER;

    return aPrice - bPrice;
  });

  return (
    <>
      <section>
        <div className="mx-auto flex w-full max-w-6xl flex-col items-center gap-3 px-4 pb-12 pt-10 text-center md:pb-14 md:pt-14">
          <h1 className="font-display text-4xl font-bold leading-tight tracking-tight text-[var(--text)] md:text-5xl">
            Nos tarifs
          </h1>

          <p className="mx-auto max-w-2xl font-body text-base leading-relaxed text-[rgba(65,27,60,0.82)] md:text-lg">
            Des formules simples, pensées pour s’adapter à votre volume de linge et à votre rythme.
          </p>
        </div>
      </section>

      <section>
        <div className="mx-auto w-full max-w-6xl px-4 pb-14 md:pb-16">
          <div className="flex flex-col gap-5 md:flex-row items-center justify-center">
            {masterRates.map((rate) => {
              const finalPrice = getDisplayFinalPrice(rate);
              const recommended = isRecommended(rate);
              const initialPrice = getRateValue(rate);

              return (
                <article
                  key={rate.id}
                  className={`relative flex h-full flex-col rounded-[var(--radius-card)] p-6 shadow-2xl min-w-[230px] md:p-7 ${
                    recommended
                      ? 'bg-premium'
                      : 'border-[rgba(65,27,60,0.10)] bg-[var(--text-light)] text-[var(--text)]'
                  }`}
                >
                  {recommended && (
                    <div className="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-1/2 rounded-full border border-[var(--text)] bg-[var(--text-light)] px-3 py-1 text-xs font-semibold text-[var(--text)] shadow-2xl">
                      Recommandé
                    </div>
                  )}

                  <div>
                    <h2
                      className={`font-display text-2xl font-bold ${
                        recommended ? 'text-[var(--text-light)]' : 'text-[var(--text)]'
                      }`}
                    >
                      Formule {rate.size}
                    </h2>

                    <p
                      className={`min-h-[48px] text-sm leading-relaxed ${
                        recommended ? 'text-[rgba(255,255,255,0.78)]' : 'text-[rgba(65,27,60,0.76)]'
                      }`}
                    >
                      {rate.description}
                    </p>
                  </div>

                  <div>
                    <>
                      <div className="flex flex-col gap-1">
                        {initialPrice !== null && (
                          <span
                            className={`text-sm line-through ${
                              recommended ? 'text-[rgba(255,255,255,0.60)]' : 'text-[rgba(65,27,60,0.55)]'
                            }`}
                          >
                            {formatPrice(initialPrice)}
                          </span>
                        )}

                        <span className="font-display text-5xl font-bold leading-none">
                          {formatPrice(finalPrice)}
                          <span className="ml-1 text-sm font-normal">*</span>
                        </span>
                      </div>

                      <p
                        className={`mt-4 text-xs italic ${
                          recommended ? 'text-[rgba(255,255,255,0.68)]' : 'text-[rgba(65,27,60,0.62)]'
                        }`}
                      >
                        * après réduction d’impôts
                      </p>
                    </>
                  </div>
                </article>
              );
            })}
          </div>
        </div>
      </section>

      <SectionDivider />

      <section>
        <div className="mx-auto w-full max-w-6xl flex flex-col gap-8 px-4 pb-16 pt-10 md:pb-20 md:pt-14">
          <div className="mx-auto text-center flex flex-col gap-2">
            <h2 className="font-display text-3xl font-bold text-[var(--text)] md:text-4xl">Tous nos tarifs</h2>

            <p className="font-body text-base leading-relaxed text-[rgba(65,27,60,0.82)]">
              Retrouvez l’ensemble de nos formules et leurs tarifs en toute transparence.
            </p>
          </div>

          <div className="overflow-hidden rounded-[32px] border border-[rgba(65,27,60,0.10)] bg-[var(--text-light)] shadow-2xl backdrop-blur-sm">
            {/* DESKTOP */}
            <div className="hidden md:block">
              <table className="w-full border-collapse">
                <thead>
                  <tr className="border-b border-[rgba(65,27,60,0.10)] bg-[rgba(255,255,255,0.35)]">
                    <th className="px-6 py-5 text-left text-sm font-semibold text-[var(--text)]">Formule</th>
                    <th className="px-6 py-5 text-left text-sm font-semibold text-[var(--text)]">Description</th>
                    <th className="px-6 py-5 text-left text-sm font-semibold text-[var(--text)]">Tarif initial</th>
                    <th className="px-6 py-5 text-left text-sm font-semibold text-[var(--text)]">Tarif final*</th>
                  </tr>
                </thead>

                <tbody>
                  {sortedRates.map((rate, index) => {
                    const quotation = isOnQuotation(rate);
                    const initialPrice = getRateValue(rate);
                    const finalPrice = getDisplayFinalPrice(rate);

                    return (
                      <tr
                        key={rate.id}
                        className={index !== sortedRates.length - 1 ? 'border-b border-[rgba(65,27,60,0.08)]' : ''}
                      >
                        <td className="px-6 py-5">
                          <div className="flex items-center gap-3">
                            <span className="font-semibold text-[var(--text)]">{rate.size}</span>

                            {isRecommended(rate) && (
                              <span className="rounded-full bg-premium px-2.5 py-1 text-xs font-medium">
                                Recommandé
                              </span>
                            )}
                          </div>
                        </td>

                        <td className="px-6 py-5 text-[rgba(65,27,60,0.82)]">{rate.description}</td>

                        <td className="px-6 py-5 text-[rgba(65,27,60,0.82)]">
                          {quotation ? '—' : formatPrice(initialPrice)}
                        </td>

                        <td className="px-6 py-5 font-semibold text-[var(--text)]">
                          {quotation ? 'Sur devis' : `${formatPrice(finalPrice)}*`}
                        </td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>
            </div>

            {/* MOBILE */}
            <div className="flex flex-col gap-4 p-4 md:hidden">
              {sortedRates.map((rate) => {
                const quotation = isOnQuotation(rate);
                const initialPrice = getRateValue(rate);
                const finalPrice = getDisplayFinalPrice(rate);
                const recommended = isRecommended(rate);

                return (
                  <div
                    key={rate.id}
                    className={`rounded-[24px] border p-4 ${
                      recommended ? 'bg-premium ' : 'border-[rgba(65,27,60,0.10)] bg-[var(--text-light)]'
                    }`}
                  >
                    <div className="flex items-center justify-between gap-3">
                      <p
                        className={`font-display text-xl font-bold ${
                          recommended ? 'text-[var(--text-light)]' : 'text-[var(--text)]'
                        }`}
                      >
                        Formule {rate.size}
                      </p>

                      {recommended && (
                        <span className="rounded-full border border-[var(--bg-start)] bg-[var(--bg-start)] text-[var(--text)] px-2.5 py-1 text-xs">
                          Recommandé
                        </span>
                      )}
                    </div>

                    <p
                      className={`text-sm leading-relaxed ${
                        recommended ? 'text-[rgba(255,255,255,0.75)]' : 'text-[rgba(65,27,60,0.72)]'
                      }`}
                    >
                      {rate.description}
                    </p>

                    <div className="mt-4 grid grid-cols-2 gap-3">
                      <div>
                        <p
                          className={`text-xs ${
                            recommended ? 'text-[rgba(255,255,255,0.68)]' : 'text-[rgba(65,27,60,0.60)]'
                          }`}
                        >
                          Tarif initial
                        </p>
                        <p className="font-medium">{quotation ? '—' : formatPrice(initialPrice)}</p>
                      </div>

                      <div>
                        <p
                          className={`text-xs ${
                            recommended ? 'text-[rgba(255,255,255,0.68)]' : 'text-[rgba(65,27,60,0.60)]'
                          }`}
                        >
                          Tarif final*
                        </p>
                        <p className="font-semibold">{quotation ? 'Sur devis' : formatPrice(finalPrice)}</p>
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>

          <div className="text-end text-xs italic leading-relaxed text-[rgba(65,27,60,0.78)]">
            * Les tarifs finaux affichés correspondent au montant estimé après réduction d’impôts de 50%.
          </div>
        </div>
      </section>
    </>
  );
}
