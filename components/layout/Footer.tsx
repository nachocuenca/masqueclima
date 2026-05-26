import Image from "next/image";
import Link from "next/link";
import { site } from "@/content/site";
import type { HomeDictionary } from "@/content/types";
import { footerLines } from "@/lib/i18n";

type FooterProps = {
  dictionary: HomeDictionary;
};

const FOOTER_LINKS = [
  { href: "#metodo", label: "Nuestro método" },
  { href: "#nosotros", label: "Quiénes somos" },
  { href: "#zona", label: "Zona de servicio" },
  { href: "#faq", label: "Preguntas frecuentes" }
];

export function Footer({ dictionary }: FooterProps) {
  const year = new Date().getFullYear();

  return (
    <footer className="bg-neutral-950 text-neutral-300">
      {/* Main footer */}
      <div className="section-shell py-12">
        <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
          {/* Brand */}
          <div className="sm:col-span-2 lg:col-span-1">
            <Link href="/es/" aria-label={site.name}>
              <Image
                alt={site.name}
                className="h-8 w-auto brightness-0 invert"
                height={32}
                src={site.logo}
                width={130}
              />
            </Link>
            <p className="mt-4 text-sm leading-7 text-neutral-400">
              Instaladores certificados de climatización, calefacción y energía solar en Benidorm y
              la provincia de Alicante.
            </p>
            <a
              className="mt-4 inline-flex items-center gap-2 rounded-full bg-[#25d366] px-4 py-2 text-sm font-bold text-white"
              href={site.whatsappUrl}
              rel="noopener noreferrer"
              target="_blank"
            >
              <svg aria-hidden="true" fill="currentColor" height="16" viewBox="0 0 24 24" width="16">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.125.557 4.126 1.526 5.867L0 24l6.322-1.493C8.02 23.468 9.974 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818c-1.87 0-3.637-.504-5.165-1.388l-.37-.22-3.752.886.937-3.65-.241-.375C2.658 15.376 2.182 13.736 2.182 12 2.182 6.566 6.566 2.182 12 2.182S21.818 6.566 21.818 12 17.434 21.818 12 21.818z" />
              </svg>
              WhatsApp
            </a>
          </div>

          {/* Navigation */}
          <div>
            <h3 className="mb-4 text-xs font-bold uppercase tracking-widest text-neutral-500">
              Navegación
            </h3>
            <ul className="space-y-2">
              {FOOTER_LINKS.map((link) => (
                <li key={link.href}>
                  <Link
                    className="text-sm text-neutral-400 transition hover:text-white"
                    href={link.href}
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h3 className="mb-4 text-xs font-bold uppercase tracking-widest text-neutral-500">
              Contacto
            </h3>
            <ul className="space-y-2 text-sm">
              <li>
                <a className="text-neutral-400 transition hover:text-white" href={site.phoneHref}>
                  {site.phone}
                </a>
              </li>
              <li>
                <a className="text-neutral-400 transition hover:text-white" href={site.emailHref}>
                  {site.email}
                </a>
              </li>
              <li className="text-neutral-500">Benidorm, Marina Baixa</li>
              <li className="text-neutral-500">Provincia de Alicante</li>
            </ul>
          </div>

          {/* Hours */}
          <div>
            <h3 className="mb-4 text-xs font-bold uppercase tracking-widest text-neutral-500">
              Horario
            </h3>
            <ul className="space-y-1 text-sm text-neutral-400">
              <li>Lun – Vie: 8:00 – 18:00</li>
              <li>Sábado: 9:00 – 14:00</li>
              <li className="text-neutral-500">Domingos: cerrado</li>
            </ul>
          </div>
        </div>
      </div>

      {/* Bottom bar */}
      <div className="border-t border-neutral-800">
        <div className="section-shell py-6 text-center text-xs text-neutral-500 sm:flex sm:items-center sm:justify-between sm:text-left">
          <p>
            &copy; {year} {site.altName}. {dictionary.footer.rights}
          </p>
          <div className="mt-2 sm:mt-0">
            {footerLines(dictionary).map((line) => (
              <span className="mx-2" key={line}>
                {line}
              </span>
            ))}
          </div>
        </div>
      </div>
    </footer>
  );
}
