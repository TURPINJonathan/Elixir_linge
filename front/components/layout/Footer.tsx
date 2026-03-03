import { getCurrentYear, getMonth } from '@utils';
import { Copyright, Facebook, Instagram, Phone, Send } from 'lucide-react';

export default function Footer() {
  const month = getMonth('string', true, undefined, { length: 'long' });
  const year = getCurrentYear();
  const currentDate = `${month} ${year}`;

  return (
    <footer className="bg-gradient-to-br from-[#411B3C] to-[#772D63] text-white">
      <div className="mx-auto w-full max-w-6xl px-4 py-10 flex flex-col gap-5">
        <div className="flex flex-wrap gap-10">
          <div className="flex-1 basis-[430px] flex flex-col gap-4">
            <div className="font-display text-xl font-bold">Elixir Linge</div>

            <div className="flex flex-col gap-2">
              <p className="text-sm">Service de blanchisserie avec collecte et livraison en entreprise sous 48h !</p>
              <p className="text-sm">
                Tranquillité d&apos;esprit et gains de temps pour vous consacrer à ce qui est important.
              </p>
            </div>
          </div>

          <div className="flex-1 basis-[430px] flex flex-wrap gap-10 md:gap-4">
            <div className="flex-1 basis-[190px] flex flex-col gap-4">
              <div className="font-semibold">Services</div>
              <div className="flex flex-col gap-2">
                <div className="flex gap-4 items-center justify-start text-sm">Pressing Premium</div>
                <div className="flex gap-4 items-center justify-start text-sm">Livraison</div>
                <div className="flex gap-4 items-center justify-start text-sm">Solutions entreprises & CSE</div>
              </div>
            </div>

            <div className="flex-1 basis-[190px] flex flex-col gap-4">
              <div className="font-semibold">Contacts</div>
              <div className="flex flex-col gap-2">
                <div className="flex gap-4 items-center justify-start">
                  <Phone size={16} />
                  06 25 29 59 52
                </div>
                <div className="flex gap-4 items-center justify-start">
                  <Send size={16} />
                  contact@elixir-linge.fr
                </div>
                <div className="flex items-center justify-center gap-4">
                  <a
                    href="#"
                    className="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors duration-200"
                    aria-label="Facebook"
                  >
                    <Facebook size={18} />
                  </a>
                  <a
                    href="#"
                    className="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors duration-200"
                    aria-label="Instagram"
                  >
                    <Instagram size={18} />
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="flex justify-between opacity-20 border-t text-xs pt-5">
          <div className="flex flex-col">
            <span>
              <Copyright size={16} className="inline-block" /> All rights reserved.
            </span>
            <span>{currentDate}</span>
          </div>

          <div className="flex gap-5">
            <span>Mentions légales</span>
            <span>Politique de confidentialité</span>
          </div>
        </div>
      </div>
    </footer>
  );
}
