import { NextRequest, NextResponse } from "next/server";
import { sendContactEmail, validateContactPayload } from "@/lib/contact";

export const runtime = "nodejs";

type Hit = {
  count: number;
  resetAt: number;
};

const hits = new Map<string, Hit>();
const WINDOW_MS = 10 * 60 * 1000;
const MAX_REQUESTS = 5;

function clientIp(request: NextRequest): string {
  const forwarded = request.headers.get("x-forwarded-for");
  return forwarded?.split(",")[0]?.trim() || request.headers.get("x-real-ip") || "unknown";
}

function rateLimited(ip: string): boolean {
  const now = Date.now();
  const current = hits.get(ip);

  if (!current || current.resetAt < now) {
    hits.set(ip, { count: 1, resetAt: now + WINDOW_MS });
    return false;
  }

  current.count += 1;
  return current.count > MAX_REQUESTS;
}

export async function POST(request: NextRequest) {
  const ip = clientIp(request);

  if (rateLimited(ip)) {
    return NextResponse.json(
      { ok: false, message: "Demasiadas solicitudes. Inténtalo de nuevo más tarde." },
      { status: 429 }
    );
  }

  let body: unknown;
  try {
    body = await request.json();
  } catch {
    return NextResponse.json({ ok: false, message: "Solicitud no válida." }, { status: 400 });
  }

  const validation = validateContactPayload(body);
  if (!validation.ok) {
    return NextResponse.json({ ok: false, message: validation.error }, { status: 400 });
  }

  try {
    const result = await sendContactEmail(validation.data, ip);
    if (!result.ok) {
      return NextResponse.json(
        { ok: false, message: result.error || "No se ha podido enviar la solicitud." },
        { status: 500 }
      );
    }

    return NextResponse.json({
      ok: true,
      mode: result.mode,
      message: "Solicitud enviada. Te contactaremos pronto."
    });
  } catch (error) {
    console.error("Contact API error", {
      error: error instanceof Error ? error.message : "unknown",
      ip
    });

    return NextResponse.json(
      { ok: false, message: "No se ha podido enviar la solicitud." },
      { status: 500 }
    );
  }
}
