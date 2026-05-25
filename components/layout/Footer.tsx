import { site } from "@/content/site";
import type { HomeDictionary } from "@/content/types";
import { footerLines } from "@/lib/i18n";

type FooterProps = {
  dictionary: HomeDictionary;
};

export function Footer({ dictionary }: FooterProps) {
  const year = new Date().getFullYear();

  return (
    <footer className="bg-neutral-950 py-10 text-white">
      <div className="section-shell text-center text-sm leading-7 text-neutral-200">
        <p>
          &copy; {year} {site.altName}. {dictionary.footer.rights}
        </p>
        {footerLines(dictionary).map((line) => (
          <p key={line}>{line}</p>
        ))}
      </div>
    </footer>
  );
}
