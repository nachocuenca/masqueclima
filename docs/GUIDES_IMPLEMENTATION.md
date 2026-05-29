# Guides Implementation

## Scope
- Added and validated 18 localized guide URLs across ES, EN, DE, NL, RU and NO.
- Fixed localized internal links in guide content where legacy URLs were still present.
- Restored the guides hub so it renders non-empty localized cards in all six languages.
- Added explicit service-to-guide interlinking on localized service pages.

## Validated guide URLs
- ES: 3 guides under `/es/blog/`
- EN: 3 guides under `/en/guides/`
- DE: 3 guides under `/de/ratgeber/`
- NL: 3 guides under `/nl/gidsen/`
- RU: 3 guides under `/ru/gidy/`
- NO: 3 guides under `/no/guider/`

## Notable fixes
- Corrected broken DE service URLs inside guide content.
- Replaced the blank guides hub state with localized guide cards.
- Removed reliance on the missing `cta.read` translation key in the guide cards partial.
- Added localized guide links to service detail pages.

## Current status
- Guide pages: validated 18/18.
- Guides hubs: validated 6/6.
- Service-to-guide interlinking: validated 30/30.
- Sitemap and robots: unchanged in this pass.
- Turnstile: untouched.
