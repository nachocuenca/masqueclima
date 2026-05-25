#!/usr/bin/env node
import * as cheerio from "cheerio";

const args = process.argv.slice(2);
const baseArg = args.find((arg) => !arg.startsWith("--"));
const baseUrl = normalizeBase(baseArg || process.env.NEXT_PUBLIC_SITE_URL || "http://localhost:3000");
const limitArg = args.find((arg) => arg.startsWith("--limit="));
const crawlLimit = Number(limitArg?.split("=")[1] || 80);
const isStaging = /(^https:\/\/dev\.|localhost|127\.0\.0\.1)/.test(baseUrl);
const expectedHomeLangs = ["es", "en", "de", "nl", "ru"];

const fallbackPaths = [
  "/",
  "/es/",
  "/en/",
  "/de/",
  "/nl/",
  "/ru/",
  "/es/contacto/",
  "/es/servicios/aire-acondicionado/",
  "/es/servicios/mantenimiento-climatizacion/",
  "/es/servicios/reparacion-aire-acondicionado/",
  "/es/servicios/calefaccion-bomba-calor/",
  "/es/servicios/energia-solar/",
  "/es/zonas/benidorm/",
  "/es/zonas/finestrat/",
  "/es/zonas/la-nucia/",
  "/es/zonas/altea/",
  "/es/zonas/villajoyosa/",
  "/es/zonas/alfaz-del-pi/",
  "/es/zonas/calpe/",
  "/es/zonas/marina-baixa/",
  "/es/zonas/alicante/",
  "/es/blog/"
];

function normalizeBase(value) {
  return value.replace(/\/+$/, "");
}

function absolute(pathOrUrl) {
  return new URL(pathOrUrl, `${baseUrl}/`).toString();
}

function pathOf(url) {
  return new URL(url).pathname;
}

async function fetchWithRedirects(url, init = {}, maxRedirects = 8) {
  const redirects = [];
  let current = url;
  let response;

  for (let i = 0; i <= maxRedirects; i += 1) {
    response = await fetch(current, {
      redirect: "manual",
      headers: {
        "user-agent": "masqueclima-seo-audit/1.0"
      },
      ...init
    });

    if (![301, 302, 303, 307, 308].includes(response.status)) {
      break;
    }

    const location = response.headers.get("location");
    if (!location) {
      break;
    }

    const next = new URL(location, current).toString();
    redirects.push({ from: current, to: next, status: response.status });
    current = next;
  }

  return { response, finalUrl: current, redirects };
}

async function fetchText(url) {
  const result = await fetchWithRedirects(url);
  const text = await result.response.text();
  return { ...result, text };
}

function extractJsonLd($) {
  return $('script[type="application/ld+json"]')
    .toArray()
    .map((node) => {
      const text = $(node).text();
      try {
        return { ok: true, data: JSON.parse(text) };
      } catch (error) {
        return { ok: false, error: error instanceof Error ? error.message : "parse error" };
      }
    });
}

function extractPage(html, response, finalUrl) {
  const $ = cheerio.load(html);
  const links = new Set();
  const assets = new Set();

  $("a[href]").each((_, node) => {
    const href = $(node).attr("href");
    if (href) {
      links.add(new URL(href, finalUrl).toString());
    }
  });

  $("img[src], script[src], link[href]").each((_, node) => {
    const src = $(node).attr("src") || $(node).attr("href");
    if (src) {
      assets.add(new URL(src, finalUrl).toString());
    }
  });

  return {
    status: response.status,
    finalUrl,
    title: $("title").first().text().trim(),
    description: $('meta[name="description"]').attr("content")?.trim() || "",
    canonical: $('link[rel="canonical"]').attr("href") || "",
    hreflang: $('link[rel="alternate"][hreflang]')
      .toArray()
      .map((node) => ({
        lang: $(node).attr("hreflang") || "",
        href: $(node).attr("href") || ""
      })),
    h1: $("h1")
      .toArray()
      .map((node) => $(node).text().trim())
      .filter(Boolean),
    robotsMeta: $('meta[name="robots"]').attr("content") || "",
    xRobots: response.headers.get("x-robots-tag") || "",
    jsonLd: extractJsonLd($),
    og: {
      title: $('meta[property="og:title"]').attr("content") || "",
      description: $('meta[property="og:description"]').attr("content") || "",
      url: $('meta[property="og:url"]').attr("content") || "",
      image: $('meta[property="og:image"]').attr("content") || ""
    },
    twitter: {
      card: $('meta[name="twitter:card"]').attr("content") || "",
      title: $('meta[name="twitter:title"]').attr("content") || "",
      description: $('meta[name="twitter:description"]').attr("content") || ""
    },
    links: [...links],
    assets: [...assets],
    visibleTextLength: $("body").text().replace(/\s+/g, " ").trim().length,
    htmlSize: html.length
  };
}

function parseSitemap(xml) {
  return [...xml.matchAll(/<loc>(.*?)<\/loc>/g)].map((match) => match[1].trim());
}

async function statusOf(url) {
  try {
    const { response, finalUrl, redirects } = await fetchWithRedirects(url, { method: "HEAD" });
    if (response.status === 405) {
      const retry = await fetchWithRedirects(url, { method: "GET" });
      return {
        ok: retry.response.ok,
        status: retry.response.status,
        finalUrl: retry.finalUrl,
        redirects: retry.redirects
      };
    }

    return {
      ok: response.ok,
      status: response.status,
      finalUrl,
      redirects
    };
  } catch (error) {
    return {
      ok: false,
      status: 0,
      finalUrl: url,
      error: error instanceof Error ? error.message : "request failed"
    };
  }
}

function internal(url) {
  return new URL(url).origin === new URL(baseUrl).origin;
}

function checkPage(page) {
  const issues = [];
  const pageUrl = page.finalUrl;
  const path = pathOf(pageUrl);

  if (page.status < 200 || page.status >= 400) {
    issues.push(`status ${page.status}`);
  }
  if (!page.title) {
    issues.push("missing title");
  }
  if (!page.description) {
    issues.push("missing meta description");
  }
  if (page.h1.length !== 1) {
    issues.push(`expected one H1, found ${page.h1.length}`);
  }
  if (!page.canonical) {
    issues.push("missing canonical");
  } else if (!page.canonical.startsWith("http")) {
    issues.push("canonical is not absolute");
  }
  if (page.jsonLd.some((entry) => !entry.ok)) {
    issues.push("invalid JSON-LD");
  }
  if (!page.og.title || !page.og.description || !page.og.url || !page.og.image) {
    issues.push("incomplete Open Graph");
  }
  if (!page.twitter.card || !page.twitter.title || !page.twitter.description) {
    issues.push("incomplete Twitter Card");
  }
  if (isStaging && !/noindex/i.test(`${page.robotsMeta} ${page.xRobots}`)) {
    issues.push("staging page missing noindex");
  }
  if (!isStaging && /noindex/i.test(`${page.robotsMeta} ${page.xRobots}`)) {
    issues.push("production page has noindex");
  }

  if (expectedHomeLangs.map((lang) => `/${lang}/`).includes(path)) {
    const langs = new Set(page.hreflang.map((item) => item.lang));
    for (const lang of expectedHomeLangs) {
      if (!langs.has(lang)) {
        issues.push(`missing hreflang ${lang}`);
      }
    }
    if (!langs.has("x-default")) {
      issues.push("missing hreflang x-default");
    }
  } else if (path.startsWith("/es/") && page.hreflang.length > 0) {
    issues.push("Spanish-only page should not emit hreflang alternates");
  }

  return issues;
}

async function main() {
  const findings = [];
  const pages = new Map();

  const robots = await fetchText(absolute("/robots.txt"));
  if (isStaging && !/Disallow:\s*\//i.test(robots.text)) {
    findings.push({ url: absolute("/robots.txt"), issues: ["staging robots.txt must disallow /"] });
  }
  if (!isStaging && !/Sitemap:/i.test(robots.text)) {
    findings.push({ url: absolute("/robots.txt"), issues: ["production robots.txt missing sitemap"] });
  }

  const sitemap = await fetchText(absolute("/sitemap.xml"));
  const sitemapUrls = parseSitemap(sitemap.text);
  if (isStaging && sitemapUrls.length > 0) {
    findings.push({ url: absolute("/sitemap.xml"), issues: ["staging sitemap exposes URLs"] });
  }
  if (!isStaging && sitemapUrls.length === 0) {
    findings.push({ url: absolute("/sitemap.xml"), issues: ["production sitemap empty"] });
  }

  const queue = [...new Set([...fallbackPaths.map(absolute), ...sitemapUrls])].slice(0, crawlLimit);
  const visited = new Set();

  while (queue.length > 0 && visited.size < crawlLimit) {
    const url = queue.shift();
    if (!url || visited.has(url)) {
      continue;
    }
    visited.add(url);

    try {
      const result = await fetchText(url);
      const contentType = result.response.headers.get("content-type") || "";
      if (!contentType.includes("text/html")) {
        continue;
      }

      const page = extractPage(result.text, result.response, result.finalUrl);
      pages.set(result.finalUrl, page);
      const issues = checkPage(page);
      if (issues.length > 0) {
        findings.push({ url, finalUrl: result.finalUrl, redirects: result.redirects, issues });
      }

      for (const link of page.links) {
        if (internal(link) && !visited.has(link) && queue.length + visited.size < crawlLimit) {
          queue.push(link);
        }
      }
    } catch (error) {
      findings.push({
        url,
        issues: [error instanceof Error ? error.message : "fetch failed"]
      });
    }
  }

  const internalLinks = new Set();
  const internalAssets = new Set();
  for (const page of pages.values()) {
    page.links.filter(internal).forEach((link) => internalLinks.add(link));
    page.assets.filter(internal).forEach((asset) => internalAssets.add(asset));
  }

  const broken = [];
  for (const url of [...internalLinks, ...internalAssets].slice(0, 200)) {
    const status = await statusOf(url);
    if (!status.ok && status.status !== 0) {
      broken.push({ url, status: status.status });
    }
  }

  const pagePaths = new Set([...pages.keys()].map(pathOf));
  const sitemapPaths = new Set(sitemapUrls.map(pathOf));
  const orphans = [...sitemapPaths].filter((path) => !pagePaths.has(path));
  const notInSitemap = !isStaging
    ? [...pagePaths].filter((path) => path !== "/" && !sitemapPaths.has(path))
    : [];

  const summary = {
    baseUrl,
    mode: isStaging ? "staging" : "production",
    crawledPages: pages.size,
    sitemapUrls: sitemapUrls.length,
    findings,
    broken,
    orphans,
    notInSitemap
  };

  console.log(JSON.stringify(summary, null, 2));

  if (findings.length > 0 || broken.length > 0 || (!isStaging && notInSitemap.length > 0)) {
    process.exitCode = 1;
  }
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
