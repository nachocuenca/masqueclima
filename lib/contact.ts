import nodemailer from "nodemailer";
import { hasSmtpConfig, isProduction } from "./env";

export type ContactPayload = {
  name: string;
  phone: string;
  email?: string;
  service?: string;
  message?: string;
  company?: string;
  turnstileToken?: string;
};

export type ContactValidationResult =
  | { ok: true; data: Required<Pick<ContactPayload, "name" | "phone">> & ContactPayload }
  | { ok: false; error: string };

function clean(value: unknown, maxLength: number): string {
  return String(value ?? "")
    .replace(/<[^>]*>/g, "")
    .replace(/[\u0000-\u001F\u007F]/g, " ")
    .replace(/\s+/g, " ")
    .trim()
    .slice(0, maxLength);
}

export function validateContactPayload(input: unknown): ContactValidationResult {
  if (!input || typeof input !== "object") {
    return { ok: false, error: "Solicitud no válida." };
  }

  const body = input as Record<string, unknown>;
  const company = clean(body.company, 120);
  if (company) {
    return { ok: false, error: "Solicitud no válida." };
  }

  const name = clean(body.name, 120);
  const phone = clean(body.phone, 60);
  const email = clean(body.email, 160);
  const service = clean(body.service, 120);
  const message = clean(body.message, 2000);
  const turnstileToken = clean(body.turnstileToken, 2048);

  if (name.length < 2) {
    return { ok: false, error: "Indica tu nombre." };
  }

  if (phone.length < 6) {
    return { ok: false, error: "Indica un teléfono válido." };
  }

  if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    return { ok: false, error: "Indica un email válido." };
  }

  return {
    ok: true,
    data: {
      name,
      phone,
      email,
      service,
      message,
      company,
      turnstileToken
    }
  };
}

async function verifyTurnstile(token: string | undefined, ip: string | null): Promise<boolean> {
  const secret = process.env.TURNSTILE_SECRET_KEY;
  if (!secret) {
    return true;
  }

  if (!token) {
    return false;
  }

  const response = await fetch("https://challenges.cloudflare.com/turnstile/v0/siteverify", {
    method: "POST",
    body: new URLSearchParams({
      secret,
      response: token,
      ...(ip ? { remoteip: ip } : {})
    })
  });

  const result = (await response.json()) as { success?: boolean };
  return Boolean(result.success);
}

export async function sendContactEmail(payload: ContactPayload, ip: string | null): Promise<{
  ok: boolean;
  mode: "sent" | "logged";
  error?: string;
}> {
  const turnstileOk = await verifyTurnstile(payload.turnstileToken, ip);
  if (!turnstileOk) {
    return { ok: false, mode: "logged", error: "Verificación anti-spam no válida." };
  }

  if (!hasSmtpConfig()) {
    console.info("Contact request received without SMTP config", {
      name: payload.name,
      phone: payload.phone,
      email: payload.email,
      service: payload.service,
      ip
    });

    if (isProduction) {
      return { ok: false, mode: "logged", error: "SMTP no configurado." };
    }

    return { ok: true, mode: "logged" };
  }

  const transporter = nodemailer.createTransport({
    host: process.env.SMTP_HOST,
    port: Number(process.env.SMTP_PORT || 587),
    secure: Number(process.env.SMTP_PORT || 587) === 465,
    auth:
      process.env.SMTP_USER && process.env.SMTP_PASSWORD
        ? {
            user: process.env.SMTP_USER,
            pass: process.env.SMTP_PASSWORD
          }
        : undefined
  });

  await transporter.sendMail({
    to: process.env.CONTACT_TO,
    from: process.env.CONTACT_FROM,
    replyTo: payload.email || undefined,
    subject: `Nueva solicitud web +QUECLIMA - ${payload.service || "Contacto"}`,
    text: [
      `Nombre: ${payload.name}`,
      `Teléfono: ${payload.phone}`,
      `Email: ${payload.email || "-"}`,
      `Servicio: ${payload.service || "-"}`,
      `IP: ${ip || "-"}`,
      "",
      payload.message || "-"
    ].join("\n")
  });

  return { ok: true, mode: "sent" };
}
