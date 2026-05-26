"use client";

import { useEffect, useRef, useState } from "react";
import { useLeadDrawer } from "./LeadDrawerProvider";
import { site } from "@/content/site";
import type { HomeDictionary } from "@/content/types";

type LeadDrawerProps = {
  dictionary: HomeDictionary;
};

type SubmitState =
  | { status: "idle"; message: "" }
  | { status: "success"; message: string }
  | { status: "error"; message: string };

const SERVICE_OPTIONS = [
  { value: "climatizacion", label: "Aire acondicionado" },
  { value: "mantenimiento", label: "Mantenimiento / Revisión" },
  { value: "reparacion", label: "Reparación / Avería" },
  { value: "calefaccion", label: "Calefacción / Bomba de calor" },
  { value: "solar", label: "Energía solar térmica" },
  { value: "electricidad", label: "Electricidad" },
  { value: "fontaneria", label: "Fontanería" },
  { value: "otro", label: "Otro" }
];

const TRUST_BADGES = [
  "Instaladores certificados",
  "Garantía real",
  "Respuesta rápida",
  "Sin compromiso"
];

export function LeadDrawer({ dictionary }: LeadDrawerProps) {
  const { isOpen, preSelectedService, closeDrawer } = useLeadDrawer();
  const [state, setState] = useState<SubmitState>({ status: "idle", message: "" });
  const closeRef = useRef<HTMLButtonElement>(null);
  const formRef = useRef<HTMLFormElement>(null);

  // Escape key
  useEffect(() => {
    function onKey(e: KeyboardEvent) {
      if (e.key === "Escape" && isOpen) closeDrawer();
    }
    document.addEventListener("keydown", onKey);
    return () => document.removeEventListener("keydown", onKey);
  }, [isOpen, closeDrawer]);

  // Focus + form reset on open
  useEffect(() => {
    if (!isOpen) return;
    setTimeout(() => closeRef.current?.focus(), 60);
    // Reset deferred so it doesn't run synchronously inside the effect body
    const id = setTimeout(() => setState({ status: "idle", message: "" }), 0);
    return () => clearTimeout(id);
  }, [isOpen]);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    const form = e.currentTarget;
    const payload = Object.fromEntries(new FormData(form).entries());
    setState({ status: "idle", message: "" });

    try {
      const res = await fetch("/api/contact/", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });
      const result = (await res.json()) as { ok?: boolean; message?: string };
      if (!res.ok || !result.ok) {
        setState({ status: "error", message: result.message ?? "No se ha podido enviar la solicitud." });
        return;
      }
      form.reset();
      setState({ status: "success", message: result.message ?? dictionary.form.ok });
    } catch {
      setState({ status: "error", message: "Error de conexión. Inténtalo de nuevo." });
    }
  }

  if (!isOpen) return null;

  return (
    <>
      {/* Overlay */}
      <div
        aria-hidden="true"
        className="fixed inset-0 z-50 bg-neutral-950/60 backdrop-blur-sm"
        onClick={closeDrawer}
      />

      {/* Panel: bottom sheet en móvil, drawer lateral en desktop */}
      <div
        aria-label="Solicitar presupuesto"
        aria-modal="true"
        className="fixed bottom-0 left-0 right-0 z-50 flex max-h-[92dvh] flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl md:bottom-0 md:left-auto md:right-0 md:top-0 md:h-full md:max-h-full md:w-[500px] md:rounded-none md:rounded-l-2xl"
        role="dialog"
      >
        {/* Header */}
        <div className="flex shrink-0 items-center justify-between border-b border-neutral-100 px-6 py-4">
          <div>
            <h2 className="text-lg font-black text-neutral-950">Solicita presupuesto</h2>
            <p className="text-sm text-neutral-500">Sin compromiso · Respuesta rápida</p>
          </div>
          <button
            aria-label="Cerrar"
            className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-neutral-600 transition hover:bg-neutral-200 focus-visible:outline-2 focus-visible:outline-teal-700"
            onClick={closeDrawer}
            ref={closeRef}
            type="button"
          >
            <svg aria-hidden="true" fill="none" height="18" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" viewBox="0 0 24 24" width="18">
              <path d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        {/* Trust badges */}
        <div className="shrink-0 flex flex-wrap gap-1.5 border-b border-neutral-100 bg-teal-50/70 px-6 py-3">
          {TRUST_BADGES.map((badge) => (
            <span
              className="inline-flex items-center gap-1 rounded-full bg-teal-100 px-3 py-1 text-xs font-bold text-teal-800"
              key={badge}
            >
              <span aria-hidden="true" className="text-teal-600">✓</span> {badge}
            </span>
          ))}
        </div>

        {/* Scrollable content */}
        <div className="flex-1 overflow-y-auto overscroll-contain">
          <div className="px-6 py-5">
            {state.status === "success" ? (
              <div className="rounded-2xl bg-emerald-50 px-6 py-10 text-center">
                <div className="mb-3 text-5xl">✅</div>
                <h3 className="mb-2 text-xl font-black text-emerald-800">¡Recibido!</h3>
                <p className="text-emerald-700">{state.message}</p>
                <a
                  className="mt-6 inline-flex items-center gap-2 rounded-full bg-[#25d366] px-6 py-3 font-bold text-white transition hover:bg-[#1fb158]"
                  href={site.whatsappUrl}
                  rel="noopener noreferrer"
                  target="_blank"
                >
                  <WhatsAppIcon />
                  También por WhatsApp
                </a>
              </div>
            ) : (
              <form className="grid gap-3.5" onSubmit={onSubmit} ref={formRef}>
                {/* Honeypot */}
                <input autoComplete="off" className="hidden" name="company" tabIndex={-1} />

                {state.status === "error" && (
                  <div className="rounded-xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700" role="alert">
                    {state.message}
                  </div>
                )}

                <label className="grid gap-1.5 text-sm font-bold text-neutral-800">
                  Nombre *
                  <input
                    autoComplete="given-name"
                    className="rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3.5 text-base text-neutral-950 transition focus:border-teal-600 focus:bg-white focus:outline-none"
                    name="name"
                    placeholder="Tu nombre"
                    required
                    type="text"
                  />
                </label>

                <label className="grid gap-1.5 text-sm font-bold text-neutral-800">
                  Teléfono *
                  <input
                    autoComplete="tel"
                    className="rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3.5 text-base text-neutral-950 transition focus:border-teal-600 focus:bg-white focus:outline-none"
                    name="phone"
                    placeholder="+34 600 000 000"
                    required
                    type="tel"
                  />
                </label>

                <label className="grid gap-1.5 text-sm font-bold text-neutral-800">
                  <span>
                    Email{" "}
                    <span className="font-normal text-neutral-500">(opcional)</span>
                  </span>
                  <input
                    autoComplete="email"
                    className="rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3.5 text-base text-neutral-950 transition focus:border-teal-600 focus:bg-white focus:outline-none"
                    name="email"
                    placeholder="tu@email.com"
                    type="email"
                  />
                </label>

                <label className="grid gap-1.5 text-sm font-bold text-neutral-800">
                  Tipo de servicio
                  <select
                    className="rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3.5 text-base text-neutral-950 transition focus:border-teal-600 focus:bg-white focus:outline-none"
                    defaultValue={preSelectedService}
                    key={preSelectedService}
                    name="service"
                  >
                    <option value="">Seleccionar servicio...</option>
                    {SERVICE_OPTIONS.map((s) => (
                      <option key={s.value} value={s.value}>
                        {s.label}
                      </option>
                    ))}
                  </select>
                </label>

                <label className="grid gap-1.5 text-sm font-bold text-neutral-800">
                  Cuéntanos qué necesitas
                  <textarea
                    className="min-h-20 rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3.5 text-base text-neutral-950 transition focus:border-teal-600 focus:bg-white focus:outline-none"
                    name="message"
                    placeholder="Describe brevemente tu situación..."
                  />
                </label>

                <button
                  className="w-full rounded-xl bg-[#f5b642] px-6 py-4 text-base font-black text-neutral-950 shadow-lg shadow-amber-200/50 transition hover:bg-[#ffc84a] active:scale-[0.98]"
                  type="submit"
                >
                  Enviar solicitud →
                </button>

                <a
                  className="flex items-center justify-center gap-2 rounded-xl border-2 border-[#25d366] px-6 py-3.5 font-bold text-[#1a9d4e] transition hover:bg-[#25d366] hover:text-white"
                  href={site.whatsappUrl}
                  rel="noopener noreferrer"
                  target="_blank"
                >
                  <WhatsAppIcon />
                  Escríbenos por WhatsApp
                </a>

                <div className="border-t border-neutral-100 pt-3 text-center text-sm text-neutral-500">
                  <a className="font-bold text-teal-700 hover:underline" href={site.phoneHref}>
                    {site.phone}
                  </a>
                  {" · "}
                  <a className="hover:underline" href={site.emailHref}>
                    {site.email}
                  </a>
                </div>
              </form>
            )}
          </div>
          {/* Safe area bottom padding for mobile */}
          <div className="h-6 md:hidden" style={{ paddingBottom: "env(safe-area-inset-bottom, 0px)" }} />
        </div>
      </div>
    </>
  );
}

function WhatsAppIcon() {
  return (
    <svg aria-hidden="true" fill="currentColor" height="20" viewBox="0 0 24 24" width="20">
      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" />
      <path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.557 4.126 1.526 5.867L0 24l6.322-1.493C8.02 23.468 9.974 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818c-1.87 0-3.637-.504-5.165-1.388l-.37-.22-3.752.886.937-3.65-.241-.375C2.658 15.376 2.182 13.736 2.182 12 2.182 6.566 6.566 2.182 12 2.182S21.818 6.566 21.818 12 17.434 21.818 12 21.818z" />
    </svg>
  );
}
