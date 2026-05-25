"use client";

import { useState } from "react";
import Script from "next/script";
import { site } from "@/content/site";
import type { HomeDictionary } from "@/content/types";

type ContactProps = {
  dictionary: HomeDictionary;
};

type SubmitState =
  | { status: "idle"; message: "" }
  | { status: "success"; message: string }
  | { status: "error"; message: string };

export function Contact({ dictionary }: ContactProps) {
  const [state, setState] = useState<SubmitState>({ status: "idle", message: "" });
  const turnstileSiteKey = process.env.NEXT_PUBLIC_TURNSTILE_SITE_KEY;

  async function onSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const form = event.currentTarget;
    const payload = Object.fromEntries(new FormData(form).entries());
    setState({ status: "idle", message: "" });

    const response = await fetch("/api/contact/", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(payload)
    });

    const result = (await response.json()) as { ok?: boolean; message?: string };

    if (!response.ok || !result.ok) {
      setState({
        status: "error",
        message: result.message || "No se ha podido enviar la solicitud."
      });
      return;
    }

    form.reset();
    setState({ status: "success", message: result.message || dictionary.form.ok });
  }

  return (
    <section className="bg-[#f6f8f9] py-20" id="contacto">
      <div className="section-shell">
        <h2 className="text-3xl font-black text-neutral-950">{dictionary.contact.title}</h2>
        <div className="mt-8 grid gap-6 lg:grid-cols-[0.85fr_1.15fr]">
          <aside className="rounded-lg border border-neutral-200 bg-white p-6">
            <h3 className="text-xl font-black text-neutral-950">{dictionary.contact.info_title}</h3>
            <dl className="mt-6 space-y-5 text-neutral-700">
              <div>
                <dt className="font-bold">{dictionary.contact.phone}</dt>
                <dd>
                  <a className="focus-ring rounded-sm text-teal-700" href={site.phoneHref}>
                    {site.phone}
                  </a>
                </dd>
              </div>
              <div>
                <dt className="font-bold">{dictionary.contact.email}</dt>
                <dd>
                  <a className="focus-ring rounded-sm text-teal-700" href={site.emailHref}>
                    {site.email}
                  </a>
                </dd>
              </div>
              <div>
                <dt className="font-bold">{dictionary.contact.area}</dt>
                <dd>{dictionary.contact.area_text}</dd>
              </div>
              <div>
                <dt className="font-bold">{dictionary.contact.hours_label}</dt>
                <dd>{dictionary.contact.hours_text}</dd>
              </div>
            </dl>
            <a
              className="focus-ring mt-7 inline-flex rounded-full bg-[#25d366] px-5 py-3 font-bold text-white"
              href={site.whatsappUrl}
              rel="noopener noreferrer"
              target="_blank"
            >
              {dictionary.cta.whatsapp}
            </a>
          </aside>

          <div className="rounded-lg border border-neutral-200 bg-white p-6">
            <h3 className="text-xl font-black text-neutral-950">{dictionary.contact.form_title}</h3>
            {state.message ? (
              <p
                className={`mt-4 rounded-md px-4 py-3 text-sm font-semibold ${
                  state.status === "success"
                    ? "bg-emerald-50 text-emerald-800"
                    : "bg-red-50 text-red-800"
                }`}
                role="status"
              >
                {state.message}
              </p>
            ) : null}
            <form className="mt-6 grid gap-4" onSubmit={onSubmit}>
              <input autoComplete="off" className="hidden" name="company" tabIndex={-1} />
              <label className="grid gap-2 font-semibold text-neutral-800">
                {dictionary.form.name} *
                <input
                  className="rounded-md border border-neutral-300 px-4 py-3 focus:border-teal-700 focus:outline-none"
                  name="name"
                  required
                  type="text"
                />
              </label>
              <label className="grid gap-2 font-semibold text-neutral-800">
                {dictionary.form.phone} *
                <input
                  className="rounded-md border border-neutral-300 px-4 py-3 focus:border-teal-700 focus:outline-none"
                  name="phone"
                  required
                  type="tel"
                />
              </label>
              <label className="grid gap-2 font-semibold text-neutral-800">
                {dictionary.form.email}
                <input
                  className="rounded-md border border-neutral-300 px-4 py-3 focus:border-teal-700 focus:outline-none"
                  name="email"
                  type="email"
                />
              </label>
              <label className="grid gap-2 font-semibold text-neutral-800">
                {dictionary.form.service}
                <select
                  className="rounded-md border border-neutral-300 px-4 py-3 focus:border-teal-700 focus:outline-none"
                  name="service"
                >
                  <option value="">{dictionary.form.select}</option>
                  <option value="climatizacion">{dictionary.service.install_ac}</option>
                  <option value="mantenimiento">{dictionary.service.maintenance}</option>
                  <option value="calefaccion">{dictionary.service.install_heating}</option>
                  <option value="electricidad">{dictionary.service.electrical}</option>
                  <option value="fontaneria">{dictionary.service.plumbing}</option>
                  <option value="urgente">{dictionary.service.urgent}</option>
                </select>
              </label>
              <label className="grid gap-2 font-semibold text-neutral-800">
                {dictionary.form.desc}
                <textarea
                  className="min-h-32 rounded-md border border-neutral-300 px-4 py-3 focus:border-teal-700 focus:outline-none"
                  name="message"
                />
              </label>
              {turnstileSiteKey ? (
                <>
                  <Script
                    src="https://challenges.cloudflare.com/turnstile/v0/api.js"
                    strategy="lazyOnload"
                  />
                  <div
                    className="cf-turnstile"
                    data-response-field-name="turnstileToken"
                    data-sitekey={turnstileSiteKey}
                  />
                </>
              ) : null}
              <button
                className="focus-ring rounded-full bg-teal-700 px-6 py-3 font-bold text-white transition hover:bg-teal-800"
                type="submit"
              >
                {dictionary.form.submit}
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>
  );
}
