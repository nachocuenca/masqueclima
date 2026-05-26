"use client";

import type { ReactNode } from "react";
import type { HomeDictionary } from "@/content/types";
import { LeadDrawer } from "./LeadDrawer";
import { LeadDrawerProvider } from "./LeadDrawerProvider";

type DrawerShellProps = {
  dictionary: HomeDictionary;
  children: ReactNode;
};

export function DrawerShell({ dictionary, children }: DrawerShellProps) {
  return (
    <LeadDrawerProvider>
      <LeadDrawer dictionary={dictionary} />
      {children}
    </LeadDrawerProvider>
  );
}
