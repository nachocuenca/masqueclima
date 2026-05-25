#!/usr/bin/env node
import * as cheerio from "cheerio";

const prod = (process.env.PROD_URL || "https://masqueclima.es").replace(/\/+$/, "");
const dev = (process.env.DEV_URL || "https://dev.masqueclima.es").replace(/\/+$/, "");

const paths = [
  "/",
  "/es/",
  "/en/",
  "/de/",
  "/nl/",
  "/ru/",
  "/es/contacto/",
  "/es/servicios/aire-acondicionado/",
  "/es/zonas/benidorm/",
  "/es/blog/",
  "/robots.txt",
  "/sitemap.xml"
];

async function fetchWithRedirects(url, maxRedirects = 8) {
  const redirects = [];
  let current = url;
  let response;

  for (let i = 0; i <= maxRedirects; i += 1) {
    response = await fetch(current, {
      redirect: "manual",
      headers: {
        "user-agent": "masqueclima-seo-compare/1.0"
      }
    });

    if (![301, 302, 303, 307, 308].includes(response.status)) {
      break;
    }

    const location = response.headers.get("location");
    if (!location) {
      break;
    }

    const next = new URL(location, current).toString();
    redirects.push({ status: response.status, from: current, to: next });
    current = next;
  }

  const text = await response.text();
  return { response, finalUrl: current, redirects, text };
}

function extractHtml(text, response, finalUrl) {
  const contentType = response.headers.get("content-type") || "";
  if (!contentType.includes("text/html")) {
    return {
      status: response.status,
      finalUrl,
      contentType,
      htmlSize: text.length,
      textSize: text.length,
      xRobots: response.headers.get("x-robots-tag") || ""
    };
  }

  const $ = cheerio.load(text);
  return {
    status: response.status,
    finalUrl,
    contentType,
    title: $("title").first().text().trim(),
    description: $('meta[name="description"]').attr("content") || "",
    canonical: $('link[rel="canonical"]').attr("href") || "",
    hreflang: $('link[rel="alternate"][hreflang]')
      .toArray()
      .map((node) => `${$(node).attr("hreflang")}=${$(node).attr("href")}`),
    xDefault:
      $('link[rel="alternate"][hreflang="x-default"]').attr("href") ||
      $('link[rel="alternate"][hreflang="x-default"]').attr("hrefLang") ||
      "",
    h1: $("h1")
      .toArray()
      .map((node) => $(node).text().trim())
      .filter(Boolean),
    robotsMeta: $('meta[name="robots"]').attr("content") || "",
    xRobots: response.headers.get("x-robots-tag") || "",
    jsonLdCount: $('script[type="application/ld+json"]').length,
    links: $("a[href]").length,
    assets: $("img[src], script[src], link[href]").length,
    htmlSize: text.length,
    textSize: $("body").text().replace(/\s+/g, " ").trim().length
  };
}

function diff(prodData, devData) {
  const checks = [
    "status",
    "title",
    "description",
    "canonical",
    "xDefault",
    "jsonLdCount",
    "htmlSize",
    "textSize"
  ];
  const differences = [];

  for (const key of checks) {
    if (prodData[key] !== devData[key]) {
      differences.push({
        field: key,
        prod: prodData[key],
        dev: devData[key]
      });
    }
  }

  if (JSON.stringify(prodData.h1) !== JSON.stringify(devData.h1)) {
    differences.push({ field: "h1", prod: prodData.h1, dev: devData.h1 });
  }
  if (JSON.stringify(prodData.hreflang) !== JSON.stringify(devData.hreflang)) {
    differences.push({ field: "hreflang", prod: prodData.hreflang, dev: devData.hreflang });
  }

  return differences;
}

async function main() {
  const rows = [];

  for (const path of paths) {
    const [prodFetch, devFetch] = await Promise.all([
      fetchWithRedirects(`${prod}${path}`),
      fetchWithRedirects(`${dev}${path}`)
    ]);

    const prodData = extractHtml(prodFetch.text, prodFetch.response, prodFetch.finalUrl);
    const devData = extractHtml(devFetch.text, devFetch.response, devFetch.finalUrl);

    rows.push({
      path,
      prod: { ...prodData, redirects: prodFetch.redirects },
      dev: { ...devData, redirects: devFetch.redirects },
      differences: diff(prodData, devData),
      critical: {
        prodNoindex: /noindex/i.test(`${prodData.robotsMeta || ""} ${prodData.xRobots || ""}`),
        devMissingNoindex: !/noindex/i.test(`${devData.robotsMeta || ""} ${devData.xRobots || ""}`),
        prodRoot403: path === "/" && prodData.status === 403
      }
    });
  }

  console.log(JSON.stringify({ prod, dev, rows }, null, 2));
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
