'use client';

import { FeatureItemComponent, SectionDivider } from '@components';
import { Calendar, Mail, Phone } from 'lucide-react';
import { useMemo, useState } from 'react';

// TODO: factoriser avec les formules du back
type Formulas = 'XS' | 'S' | 'M' | 'L' | 'XL' | 'XXL' | '3XL';

export default function ContactPage() {
  // TODO: récupérer les formules depuis le back pour éviter la duplication
  const formulas = useMemo(
    () => [
      { value: 'XS', label: 'XS — 5 pièces' },
      { value: 'S', label: 'S — 10–15 pièces' },
      { value: 'M', label: 'M — 20–25 pièces (recommandé)' },
      { value: 'L', label: 'L — 30–35 pièces' },
      { value: 'XL', label: 'XL — 40–45 pièces' },
      { value: 'XXL', label: 'XXL — 50–60 pièces' },
      { value: '3XL', label: '3XL — 70–100 pièces (sur devis)' },
    ],
    [],
  );

  const [form, setForm] = useState({
    // Client
    title: 'M' as 'Mme' | 'M' | 'Autre',
    lastname: '',
    firstname: '',
    address: '',
    postalCode: '',
    city: '',
    phone: '',
    email: '',

    // Entreprise
    companyName: '',
    companyAddress: '',
    companyPostalCode: '',
    companyCity: '',
    companyInfo: '',

    // Commande
    formulas: 'M' as Formulas,
    comment: '',

    // Acceptation
    acceptation: false,
  });

  const onChange =
    (key: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
      setForm((prev) => ({ ...prev, [key]: e.target.value }));
    };

  const onSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    // TODO: brancher ton API / email / service
    console.log('Form submit:', form);
  };

  const inputBase =
    'w-full rounded-xl border border-[rgba(65,27,60,0.25)] bg-white/85 px-4 py-3 font-body text-sm text-[rgba(65,27,60,0.95)] outline-none transition focus:border-[#411b3c] focus:ring-2 focus:ring-[rgba(65,27,60,0.18)]';
  const sectionTitle = 'font-display text-lg font-bold tracking-tight text-[#411b3c]';

  return (
    <>
      <section>
        <div className="mx-auto w-full max-w-6xl px-4 pb-16 pt-10 md:pt-14">
          <div className="flex flex-wrap items-start gap-4 relative">
            <div className="z-1 flex-1 basis-[390px] flex flex-col items-start gap-2">
              <h1 className="font-display text-4xl text-center font-bold leading-tight tracking-tight md:text-start md:text-5xl">
                Contactez-nous !
              </h1>

              <span className="font-body text-base text-center leading-relaxed text-[rgba(65,27,60,0.82)] md:text-start md:text-lg md:max-w-[680px]">
                ... et découvrez comment avoir un linge impeccable en allant travailler.
              </span>

              <div className="flex flex-col gap-4 max-w-[776px] items-start py-10 pl-6">
                <FeatureItemComponent icon={Mail} title="Email" text="contact@elixir-linge.fr" />
                <FeatureItemComponent icon={Phone} title="Téléphone" text="06 25 29 59 52" />
                <FeatureItemComponent icon={Calendar} title="Horaires" text="Du lundi au vendredi, de 8h à 18h" />
              </div>
            </div>

            {/* FORMULAIRE */}
            <div
              className="z-1 flex-3 flex w-full basis-[420px] flex-col gap-6 border border-[#411b3c] p-6 md:p-10"
              style={{ borderRadius: 'var(--radius-card)' }}
            >
              <form onSubmit={onSubmit} className="flex flex-col gap-5">
                {/* GROUPE CLIENT */}
                <fieldset className="flex flex-col gap-4">
                  <legend className={sectionTitle}>Client</legend>

                  <div className="flex flex-wrap gap-1">
                    <div className="flex-1 basis-[80px]">
                      <select id="title" className={inputBase} value={form.title} onChange={onChange('title')}>
                        <option value="Mme">Mme</option>
                        <option value="M">M</option>
                        <option value="Autre">Autre</option>
                      </select>
                    </div>

                    <div className="flex-2 basis-[150px]">
                      <input
                        id="lastname"
                        className={inputBase}
                        value={form.lastname}
                        onChange={onChange('lastname')}
                        placeholder="Dupont"
                        required
                      />
                    </div>

                    <div className="flex-2 basis-[150px]">
                      <input
                        id="firstname"
                        className={inputBase}
                        value={form.firstname}
                        onChange={onChange('firstname')}
                        placeholder="Dominique"
                        required
                      />
                    </div>

                    <div className="w-full">
                      <input
                        id="address"
                        className={inputBase}
                        value={form.address}
                        onChange={onChange('address')}
                        placeholder="12 rue des Lilas"
                        required
                      />
                    </div>

                    <div className="flex-1 basis-[100px]">
                      <input
                        id="postalCode"
                        className={inputBase}
                        value={form.postalCode}
                        onChange={onChange('postalCode')}
                        placeholder="14200"
                        inputMode="numeric"
                        required
                      />
                    </div>

                    <div className="flex-3 basis-[250px]">
                      <input
                        id="city"
                        className={inputBase}
                        value={form.city}
                        onChange={onChange('city')}
                        placeholder="Mondeville"
                        required
                      />
                    </div>

                    <div className="flex flex-wrap gap-1 w-full">
                      <div className="flex-1 basis-[150px]">
                        <input
                          id="phone"
                          className={inputBase}
                          value={form.phone}
                          onChange={onChange('phone')}
                          placeholder="01 23 45 67 89"
                          required
                        />
                      </div>

                      <div className="flex-1 basis-[150px]">
                        <input
                          id="email"
                          className={inputBase}
                          value={form.email}
                          onChange={onChange('email')}
                          placeholder="email@example.com"
                          required
                        />
                      </div>
                    </div>
                  </div>
                </fieldset>

                {/* GROUPE ENTREPRISE */}
                <fieldset className="flex flex-col gap-4">
                  <legend className={sectionTitle}>Entreprise</legend>

                  <div className="flex flex-wrap gap-1">
                    <div className="flex-1 basis-[100%]">
                      <input
                        id="companyName"
                        className={inputBase}
                        value={form.companyName}
                        onChange={onChange('companyName')}
                        placeholder="ACME SARL"
                        required
                      />
                    </div>

                    <div className="flex-1 basis-[100%]">
                      <input
                        id="companyAddress"
                        className={inputBase}
                        value={form.companyAddress}
                        onChange={onChange('companyAddress')}
                        placeholder="25 avenue Victor Hugo"
                        required
                      />
                    </div>

                    <div className="flex-1 basis-[100px]">
                      <input
                        id="companyPostalCode"
                        className={inputBase}
                        value={form.companyPostalCode}
                        onChange={onChange('companyPostalCode')}
                        placeholder="14000"
                        inputMode="numeric"
                        required
                      />
                    </div>

                    <div className="flex-3 basis-[250px]">
                      <input
                        id="companyCity"
                        className={inputBase}
                        value={form.companyCity}
                        onChange={onChange('companyCity')}
                        placeholder="Caen"
                        required
                      />
                    </div>

                    <div className="w-full">
                      <textarea
                        id="companyInfo"
                        className={`${inputBase} min-h-[96px] resize-y`}
                        value={form.companyInfo}
                        onChange={onChange('companyInfo')}
                        placeholder="Étage, digicode, bâtiment, instructions de livraison…"
                      />
                    </div>
                  </div>
                </fieldset>

                {/* GROUPE COMMANDE */}
                <fieldset className="flex flex-col gap-4">
                  <legend className={sectionTitle}>Commande</legend>

                  <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div className="flex flex-col gap-2 md:col-span-2">
                      <select id="formulas" className={inputBase} value={form.formulas} onChange={onChange('formulas')}>
                        {formulas.map((f) => (
                          <option key={f.value} value={f.value}>
                            {f.label}
                          </option>
                        ))}
                      </select>
                    </div>

                    <div className="flex flex-col gap-2 md:col-span-2">
                      <textarea
                        id="comment"
                        className={`${inputBase} min-h-[120px] resize-y`}
                        value={form.comment}
                        onChange={onChange('comment')}
                        placeholder="Avez-vous des précisions à nous apporter ?"
                      />
                    </div>
                  </div>
                </fieldset>

                {/* ACTIONS */}
                <div className="flex flex-col gap-3 pt-2">
                  <div className="flex gap-2 items-center justify-start">
                    <input
                      type="checkbox"
                      id="acceptation"
                      className="form-checkbox h-5 w-5 text-blue-600 focus:ring-blue-500"
                      checked={form.acceptation}
                      onChange={onChange('acceptation')}
                    />
                    <label htmlFor="acceptation" className="font-body text-xs text-[rgba(65,27,60,0.7)]">
                      J&apos;accepte que mes données soient utilisées afin d&apos;être recontacté, conformément à la
                      politique de confidentialité. *
                    </label>
                  </div>
                  <button
                    type="submit"
                    disabled={!form.acceptation}
                    className="btn btn-primary text-sm flex items-center justify-center cursor-pointer disabled:cursor-not-allowed disabled:bg-[rgba(65,27,60,0.3)] disabled:text-[rgba(255,255,255,0.7)]"
                  >
                    Envoyer ma demande<span aria-hidden="true">→</span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <SectionDivider className="absolute! z-0 top-3/5 left-0 right-0" />
      </section>
    </>
  );
}
