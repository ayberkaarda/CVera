# Verification record

Verified locally on 2026-10-15. LearnToPoker informed interaction/animation depth only; CVera keeps its own identity.

## Results

| Check | Result |
| --- | --- |
| Laravel feature/security tests | 12 passed, 62 assertions |
| Playwright desktop/mobile suite | 42 passed, 2 intentionally skipped |
| PHP style verification | Laravel Pint passed, 16 files |
| JavaScript syntax | app.js, pages.js, scene.js passed node --check |
| Composer metadata/lock | composer validate --no-check-publish passed |
| Runtime errors | No landing-page JavaScript errors in tested Chromium contexts |
| Responsive layout | No horizontal overflow in all public tool routes at tested desktop/mobile sizes |
| Additional viewport review | 320, 360, 390, 414, 700, 701, 768, 820, 1024, 1440 px |

The two skipped scenarios are duplicate mobile PDF-generation and OCR checks. Both flows were exercised on desktop with real files/images.

## Evidence from the full story

- Browser CV upload → actual PDF.js/Mammoth/TXT extraction → CSRF-protected Laravel analysis → category score and actionable recommendations.
- Real job image → local Tesseract Turkish/English worker → editable text → Laravel keyword comparison → found/missing terms.
- Builder input → live preview and debounced scoring → template/language switch → local draft revisions and restore → print-only document output.
- Three.js initialization → real scene-ready flag → pause → keyboard mode switch → different canvas pixels → mobile mode controls.
- Account-backed document service → owner checks → prior version snapshot → restore without losing the outgoing version.
- Explicit public portfolio publication → escaped public HTML → unpublish → former URL returns 404.
- Cover-letter facts → editable draft → text download; portfolio input → sandboxed live preview → standalone HTML download.
- Extension page → actual downloadable ZIP. The extension’s installed browser UI and third-party job websites were not exercised.
- Missing AI configuration → honest unavailable response. Mock provider → generated response explicitly marked for review.

## Template output checks

All 25 CV designs were exported through Chromium print rendering using the sample CV. Each produced one A4 page with searchable name and experience text. Longer user content can naturally span multiple pages. Sidebar, gradient, geometric-header and split-header outputs were rendered back to images for visual inspection.

All 18 portfolio layouts were exercised at desktop/mobile viewport sizes. The gallery uses locally captured previews of the same renderer used for live editing, HTML export and published pages.

## Screenshot artifacts

Captured from the running application, not design mockups:

- landing-desktop.png / landing-mobile.png
- landing-desktop-full.png / landing-mobile-full.png
- analysis-desktop.png / analysis-mobile.png
- job-match-desktop.png / job-match-mobile.png
- builder-desktop.png / builder-mobile.png
- templates-desktop.png / templates-mobile.png
- portfolio-templates-desktop.png / portfolio-templates-mobile.png
- portfolio-builder-desktop.png / portfolio-builder-mobile.png

Files are in [screenshots](screenshots). Both README editions embed the desktop/mobile landing and additional product screenshots. The screenshot test scrolls sections into view so scroll-triggered content is captured.

## Not claimed as verified

Live AI provider behavior/cost, real employer ATS systems, production hosting, production email, browser-store publication, every third-party job site layout, and the operating system’s final PDF save dialog were not verified. No payment or subscription mechanism exists.

## Reproduce

Start the Laravel server at http://127.0.0.1:8000, then run:

~~~sh
php artisan test
npm ci
npx playwright install chromium
npm test
npm run screenshots
~~~

If immediately rerunning the complete browser suite against the same development server, allow rate-limit windows to expire or run php artisan cache:clear in that disposable development environment first. Production limits are not disabled for tests.
