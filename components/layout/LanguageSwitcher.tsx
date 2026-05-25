import Image from "next/image";
import Link from "next/link";
import { langNames } from "@/content/site";
import type { Lang } from "@/content/types";
import { LANGS, homePath } from "@/lib/routes";

type LanguageSwitcherProps = {
  currentLang: Lang;
};

export function LanguageSwitcher({ currentLang }: LanguageSwitcherProps) {
  return (
    <ul className="flex items-center gap-1" aria-label="Idiomas">
      {LANGS.map((lang) => {
        const active = lang === currentLang;

        return (
          <li key={lang}>
            <Link
              aria-current={active ? "page" : undefined}
              aria-label={langNames[lang]}
              className={`focus-ring flex h-9 w-9 items-center justify-center rounded-full border transition ${
                active
                  ? "border-teal-700 bg-teal-700"
                  : "border-neutral-200 bg-white hover:border-teal-700"
              }`}
              href={homePath(lang)}
              hrefLang={lang}
              title={langNames[lang]}
            >
              <Image
                alt={langNames[lang]}
                height={12}
                src={`/assets/img/flags/${lang}.svg`}
                width={16}
              />
            </Link>
          </li>
        );
      })}
    </ul>
  );
}
