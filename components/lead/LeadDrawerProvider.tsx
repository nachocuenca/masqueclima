"use client";

import { createContext, useCallback, useContext, useState } from "react";
import type { ReactNode } from "react";

type LeadDrawerContextType = {
  isOpen: boolean;
  preSelectedService: string;
  openDrawer: (service?: string) => void;
  closeDrawer: () => void;
};

const LeadDrawerContext = createContext<LeadDrawerContextType | null>(null);

export function useLeadDrawer() {
  const ctx = useContext(LeadDrawerContext);
  if (!ctx) throw new Error("useLeadDrawer must be used within LeadDrawerProvider");
  return ctx;
}

export function LeadDrawerProvider({ children }: { children: ReactNode }) {
  const [isOpen, setIsOpen] = useState(false);
  const [preSelectedService, setPreSelectedService] = useState("");

  const openDrawer = useCallback((service = "") => {
    setPreSelectedService(service);
    setIsOpen(true);
    if (typeof document !== "undefined") {
      document.body.style.overflow = "hidden";
    }
  }, []);

  const closeDrawer = useCallback(() => {
    setIsOpen(false);
    if (typeof document !== "undefined") {
      document.body.style.overflow = "";
    }
  }, []);

  return (
    <LeadDrawerContext.Provider value={{ isOpen, preSelectedService, openDrawer, closeDrawer }}>
      {children}
    </LeadDrawerContext.Provider>
  );
}
