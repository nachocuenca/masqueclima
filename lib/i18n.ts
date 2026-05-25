import { dictionaries } from "@/content/i18n";
import type { FaqItem, HomeDictionary, Lang } from "@/content/types";
import { DEFAULT_LANG, isLang } from "./routes";

export function getDictionary(lang: Lang): HomeDictionary {
  return dictionaries[lang] ?? dictionaries[DEFAULT_LANG];
}

export function normalizeLang(lang: string | undefined): Lang {
  return isLang(lang) ? lang : DEFAULT_LANG;
}

export function getHomeFaqs(dictionary: HomeDictionary): FaqItem[] {
  const faqs: FaqItem[] = [];

  for (let index = 1; index <= 8; index += 1) {
    const question = dictionary.faq[`q${index}`];
    let answer = dictionary.faq[`a${index}`];

    if (!question || !answer) {
      continue;
    }

    if (index === 3) {
      const items = [1, 2, 3, 4, 5]
        .map((itemIndex) => dictionary.faq[`a3.i${itemIndex}`])
        .filter(Boolean);
      answer = [answer, ...items].join(" ");
    }

    faqs.push({ question, answer });
  }

  return faqs;
}

export function footerLines(dictionary: HomeDictionary): string[] {
  return [
    dictionary.footer.line2 ?? dictionary.footer.tagline,
    dictionary.footer.line3 ?? dictionary.footer.service
  ].filter(Boolean);
}
