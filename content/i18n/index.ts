import de from "./de";
import en from "./en";
import es from "./es";
import nl from "./nl";
import ru from "./ru";
import type { HomeDictionary, Lang } from "../types";

export const dictionaries: Record<Lang, HomeDictionary> = {
  es,
  en,
  de,
  nl,
  ru
};
