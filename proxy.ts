import { NextRequest, NextResponse } from "next/server";

const legacyRedirects: Record<string, string> = {
  "/es/servicios/instalacion-aire-acondicionado/": "/es/servicios/aire-acondicionado/",
  "/es/servicios/reparacion-averias/": "/es/servicios/reparacion-aire-acondicionado/",
  "/es/servicios/calefaccion-bomba-de-calor/": "/es/servicios/calefaccion-bomba-calor/",
  "/es/aire-acondicionado-benidorm/": "/es/zonas/benidorm/",
  "/es/aire-acondicionado-finestrat/": "/es/zonas/finestrat/",
  "/es/aire-acondicionado-la-nucia/": "/es/zonas/la-nucia/",
  "/es/aire-acondicionado-altea/": "/es/zonas/altea/",
  "/es/aire-acondicionado-villajoyosa/": "/es/zonas/villajoyosa/",
  "/es/aire-acondicionado-alfaz-del-pi/": "/es/zonas/alfaz-del-pi/",
  "/es/aire-acondicionado-calpe/": "/es/zonas/calpe/"
};

function slash(pathname: string): string {
  return pathname.endsWith("/") ? pathname : `${pathname}/`;
}

export function proxy(request: NextRequest) {
  const { pathname } = request.nextUrl;

  if (pathname === "/") {
    const url = request.nextUrl.clone();
    url.pathname = "/es/";
    return NextResponse.redirect(url, 301);
  }

  if (pathname.toLowerCase().endsWith(".php")) {
    return new NextResponse("Not found", { status: 404 });
  }

  const redirected = legacyRedirects[slash(pathname)];
  if (redirected) {
    const url = request.nextUrl.clone();
    url.pathname = redirected;
    url.search = "";
    return NextResponse.redirect(url, 301);
  }

  const requestHeaders = new Headers(request.headers);
  requestHeaders.set("x-pathname", pathname);

  const response = NextResponse.next({
    request: {
      headers: requestHeaders
    }
  });

  if (process.env.APP_ENV !== "production") {
    response.headers.set("X-Robots-Tag", "noindex, nofollow, noarchive");
  }

  return response;
}

export const config = {
  matcher: ["/((?!_next/static|_next/image|favicon.ico).*)"]
};
