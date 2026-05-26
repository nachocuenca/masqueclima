"use client";

import type { ReactNode } from "react";
import { useLeadDrawer } from "./LeadDrawerProvider";

type OpenDrawerButtonProps = {
  service?: string;
  className?: string;
  children: ReactNode;
};

export function OpenDrawerButton({ service, className, children }: OpenDrawerButtonProps) {
  const { openDrawer } = useLeadDrawer();

  return (
    <button className={className} onClick={() => openDrawer(service)} type="button">
      {children}
    </button>
  );
}
