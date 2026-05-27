# Runtime production snapshots

These HTML files are runtime inputs for the legacy PHP front controller, not audit evidence.

They were captured from the live production site on 2026-05-26 to preserve the current visual/textual legacy site while fixing technical issues in staging:

- 126 canonical URLs from the live sitemap, excluding the broken root `/`.
- 6 live reformas URLs that production serves and links but does not include in the sitemap.
- 6 languages: `es`, `en`, `de`, `nl`, `ru`, `no`.

Why they are committed:

- The previous repo did not contain a complete legacy representation of `/no/`, city landings, or reformas.
- Rebuilding the content from templates would risk changing working foreign-language copy and layout.
- `app/front_controller.php` serves these snapshots and applies only runtime technical patches: `/` redirect handling, `x-default` correction, CSRF/return URL refresh, staging noindex, and JSON-LD fallback where production has none.

Regeneration rule:

1. Audit production first.
2. Use `public/sitemap.xml` for canonical URLs and `docs/REFORMAS_CONTENT_REVIEW.md` for reformas URLs.
3. Fetch from `https://masqueclima.es`, not from `dev.masqueclima.es`.
4. Do not regenerate after production changes without updating the audit docs.

These files must not contain credentials, logs, caches, or local environment data.
