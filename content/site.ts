import configJson from "./legacy/config.json";
import type { Lang } from "./types";

type LegacyConfig = {
  brand: {
    name: string;
    alt_name: string;
    domain: string;
    phone: string;
    email: string;
    area: string;
    default_lang: Lang;
    langs: Lang[];
  };
  seo: {
    title: Record<Lang, string>;
    description: Record<Lang, string>;
  };
};

export const legacyConfig = configJson as LegacyConfig;

export const site = {
  name: legacyConfig.brand.name,
  altName: legacyConfig.brand.alt_name,
  phone: legacyConfig.brand.phone,
  phoneHref: "tel:+34613026600",
  whatsappUrl: "https://wa.me/34613026600",
  email: legacyConfig.brand.email,
  emailHref: `mailto:${legacyConfig.brand.email}`,
  area: legacyConfig.brand.area,
  defaultLang: legacyConfig.brand.default_lang,
  langs: legacyConfig.brand.langs,
  logo: "/assets/img/logo.svg",
  ogImage: "/assets/img/og.jpg",
  heroPoster: "/assets/img/hero1.webp",
  heroVideo: "/assets/img/hero.mp4",
  mapEmbed:
    "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12725.620917144712!2d-0.136398!3d38.538232!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd6221f6b0e9dffd%3A0x3ee4adcdf1ef9be4!2sBenidorm%2C%20Alicante!5e0!3m2!1ses!2ses!4v1689843948108!5m2!1ses!2ses",
  homeMeta: legacyConfig.seo
} as const;

export const langNames: Record<Lang, string> = {
  es: "Español",
  en: "English",
  de: "Deutsch",
  nl: "Nederlands",
  ru: "Русский"
};

export const localeMap: Record<Lang, string> = {
  es: "es_ES",
  en: "en_GB",
  de: "de_DE",
  nl: "nl_NL",
  ru: "ru_RU"
};

export const brandAssets = [
  { src: "/assets/img/Daikin-Logo.png", altKey: "daikin_alt", name: "Daikin" },
  {
    src: "/assets/img/Mitsubishi-Electric-Logo.png",
    altKey: "mitsubishi_alt",
    name: "Mitsubishi Electric"
  },
  { src: "/assets/img/Logo-Fujitsu.png", altKey: "fujitsu_alt", name: "Fujitsu" },
  { src: "/assets/img/Logo Panasonic.png", altKey: "panasonic_alt", name: "Panasonic" },
  { src: "/assets/img/Haier-Logo.wine.png", altKey: "haier_alt", name: "Haier" },
  { src: "/assets/img/LG-logo.png", altKey: "lg_alt", name: "LG" },
  { src: "/assets/img/gree-logo.png", altKey: "gree_alt", name: "Gree" },
  { src: "/assets/img/giatsu-01.png", altKey: "giatsu_alt", name: "Giatsu" }
] as const;
