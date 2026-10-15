# CVera

**A free CV workspace for clearer applications.** Built with Laravel and an animated, interactive Three.js frontend.

[English](README.md) · [Türkçe](README.tr.md)

Analyze a CV, compare it with a job description or screenshot, build a searchable PDF, write a cover letter, and create a personal portfolio. Core tools work without registration. Optional accounts provide cloud documents and version history. **No payments, subscriptions, premium tiers, or paid template locks.**

## Screenshots

Actual application captures at **1440 × 1000 desktop** and **390 × 844 mobile**. Sample data is clearly labeled in the interface.

### Desktop

![CVera interactive landing page on desktop](docs/screenshots/landing-desktop.png)

### Mobile

<img src="docs/screenshots/landing-mobile.png" alt="CVera interactive landing page on mobile" width="300">

<details>
<summary>ATS analysis, job matching, builder, and templates</summary>

| Tool | Desktop | Mobile |
| --- | --- | --- |
| ATS analysis | [Screenshot](docs/screenshots/analysis-desktop.png) | [Screenshot](docs/screenshots/analysis-mobile.png) |
| Job matching | [Screenshot](docs/screenshots/job-match-desktop.png) | [Screenshot](docs/screenshots/job-match-mobile.png) |
| CV builder | [Screenshot](docs/screenshots/builder-desktop.png) | [Screenshot](docs/screenshots/builder-mobile.png) |
| 25 templates | [Screenshot](docs/screenshots/templates-desktop.png) | [Screenshot](docs/screenshots/templates-mobile.png) |
| Portfolio designs | [Screenshot](docs/screenshots/portfolio-templates-desktop.png) | [Screenshot](docs/screenshots/portfolio-templates-mobile.png) |
| Portfolio builder | [Screenshot](docs/screenshots/portfolio-builder-desktop.png) | [Screenshot](docs/screenshots/portfolio-builder-mobile.png) |
| Full landing page | [Screenshot](docs/screenshots/landing-desktop-full.png) | [Screenshot](docs/screenshots/landing-mobile-full.png) |

![CVera ATS analysis with explainable recommendations](docs/screenshots/analysis-desktop.png)

![CVera CV builder and live document preview](docs/screenshots/builder-desktop.png)

![Portfolio design gallery](docs/screenshots/portfolio-templates-desktop.png)

</details>

## What is included

- Interactive Three.js CV studio with three scene modes, pointer/touch feedback, keyboard controls, pause/resume, scroll choreography, and reduced-motion support.
- General ATS guidance: eight explainable checks, category scores, recommendations, editable CV text, original-text restore, and text export.
- Job-specific comparison: found/missing skills and an explicit match percentage, with Turkish/English image OCR and editable extraction.
- PDF, DOCX, and TXT CV import in the browser. Original files are not uploaded.
- CV builder with live preview, live scoring, Turkish/English document headings, 25 single- and two-column templates, browser PDF output, local drafts, and local revision history.
- Optional accounts, isolated cloud documents, duplication, revision history, and restore.
- Editable cover-letter drafts, text/PDF export, and optional AI personalization.
- Portfolio builder with 18 selectable designs, profile photos, font/color controls, standalone HTML export, and optional public publish/unpublish.
- Unpacked Chrome/Edge Manifest V3 extension for transferring a user-reviewed job description.
- Career guides, 12 occupation guidance cards, and a 12-point manual checklist.
- Configurable server-side AI for summaries, CV optimization, structured import, cover letters, and CV questions.

See [feature coverage and limitations](docs/FEATURES.md) for the implementation status of each capability.

## Stack

Laravel 12 · PHP 8.2+ · Blade · native CSS/JavaScript ES modules · Three.js · Tesseract.js · PDF.js · Mammoth · SQLite · PHPUnit · Playwright.

Frontend libraries, OCR workers/language data, and fonts are served locally. Node is needed for browser tests, not for running the application. There is no frontend compilation step.

## Run locally

Requirements:

- PHP 8.2 or newer with the standard Laravel extensions, PDO SQLite, mbstring, OpenSSL, cURL, and ZIP.
- Composer 2.
- Node.js 22+ and npm only if you want to run browser tests or regenerate screenshots.

From the repository root:

~~~sh
composer install
php scripts/setup.php
php artisan serve --host=127.0.0.1 --port=8000
~~~

Open **http://127.0.0.1:8000**.

The setup script creates a missing .env, creates the default SQLite file, generates an application key only if absent, and runs migrations. It preserves an existing .env and application key. No API key is needed for analysis, OCR, the builder, basic cover letters, or local HTML/PDF exports.

### Optional AI

Add a compatible chat-completions provider to .env:

~~~dotenv
AI_API_KEY=your-provider-key
AI_BASE_URL=https://api.groq.com/openai/v1
AI_MODEL=llama-3.3-70b-versatile
~~~

~~~sh
php artisan config:clear
~~~

The provider base URL/model are configurable. AI credentials stay on the server. AI tools send the submitted CV/job text to that provider and require review before applying generated content. With no key, the interface reports that AI is unavailable; it does not simulate a generated result. A live provider has not been configured in this checkout. Automated provider tests use a mock response.

## Routes

| Route | Purpose |
| --- | --- |
| / | Interactive landing |
| /analiz | General ATS guidance and editing |
| /ilana-gore | Job OCR, text comparison, and tailoring |
| /cv-olustur | Live CV builder |
| /sablonlar | 25 free templates |
| /on-yazi | Cover letters |
| /portfolyo | Personal portfolio builder |
| /portfolyo-sablonlari | 18 portfolio designs |
| /p/{slug} | Explicitly published public portfolio |
| /rehber | Career resources and checklist |
| /asistan | Optional AI CV advisor |
| /eklenti | Extension download and instructions |
| /hesabim | Optional account and cloud workspace |

JSON endpoints use Laravel session CSRF protection. Analysis and AI endpoints are public with separate per-IP rate limits. Cloud document endpoints require authentication and scope every operation to the owner. Published portfolios use randomly generated URLs and can be unpublished.

## Browser extension

Download the ZIP from /eklenti or use the [extension source](extension).

1. Extract public/downloads/cvera-extension.zip.
2. Open chrome://extensions or edge://extensions.
3. Enable Developer mode and choose **Load unpacked**.
4. Select the extracted extension folder.
5. Open a job listing, read the page, review/edit its text, and choose **Open in CVera**.

The default destination is http://localhost:8000. Change it to your own HTTPS origin when deployed. Only activeTab, scripting, and storage permissions are requested. Extraction occurs after a click; job text is transferred in a URL fragment, not a query string. The extension is not published to a browser store and third-party site markup may require manual corrections.

## Tests and screenshots

Start the Laravel server before the browser suite.

~~~sh
php artisan test
npm ci
npx playwright install chromium
npm test
npm run screenshots
~~~

Backend tests cover scoring, validation, no persistence during analysis, job-term boundaries, owner isolation, revision restore, publish/unpublish, HTML escaping, account flows, independent API rate limits, and mock/unconfigured AI behavior.

Browser tests cover desktop/mobile rendering, real WebGL initialization, TXT/PDF/DOCX import, actual image OCR, job matching, local drafts/history, template changes, print media, cover-letter/HTML/extension downloads, reduced motion, and horizontal overflow. PDF generation and OCR are tested once on desktop; their duplicate mobile cases are intentionally skipped. No live AI provider or browser-store installation is exercised.

Regenerated screenshots are written to docs/screenshots. Run npm run previews after changing portfolio layouts to regenerate the 18 gallery thumbnails. See [verification notes](docs/VERIFICATION.md).

## Scoring and privacy

The general score totals 100 points: contact details 15, experience 15, education 10, skills 15, summary 10, measurable achievements 15, content scope 10, and action-oriented language 10. When a job contains supported dictionary terms, the displayed score is **65% general content + 35% term overlap**. Unsupported jobs return no match percentage.

This is a transparent heuristic, not an employer ATS score or a guarantee of interviews. It does not inspect document layout, columns, tables, or real ATS parsing. Keyword matching uses a fixed dictionary and OCR may make mistakes.

Original files and images are processed in the browser. Extracted text is sent to Laravel for analysis and is not saved by the analysis endpoint. Device drafts/history are saved only on explicit action. Cloud documents are saved only on explicit account actions. Published portfolio content is publicly accessible until unpublished. AI requests send only the submitted text to the configured provider; review that provider’s data policy before enabling it.

## Production

Point the web server to public/. Set APP_ENV=production, APP_DEBUG=false and the correct HTTPS APP_URL; configure secure cookies and trusted proxies for your host. Make storage/ and bootstrap/cache writable, back up the database, and run migrations and Laravel deployment caches. Never expose .env or serve the repository root. Use a proper web server/PHP-FPM rather than artisan serve. This repository has not been deployed as part of the task.

Optional account email verification, password-reset email, production mail delivery, and store publication are not configured. There is no billing integration.

## Project map

~~~text
app/Services/AtsAnalyzer.php        Explainable scoring and keyword dictionary
app/Http/Controllers/              Analysis, AI, accounts, documents
resources/views/                   Main Blade shell and public portfolio
public/assets/                     UI, Three.js scene, self-hosted dependencies/fonts
extension/                         Unpacked browser extension source
scripts/                           Setup and test-fixture helpers
tests/Feature/                     Laravel feature/security tests
e2e/                               Desktop/mobile Playwright flows and captures
docs/                              Feature coverage, verification, screenshots
~~~

Third-party dependencies retain their respective licenses; see [third-party notices](docs/THIRD_PARTY.md). Functional reference: [PratikCV](https://pratikcv.net/). The site identity is original; CV and portfolio layouts follow the user-supplied design references with distinct template names. The author’s [LearnToPoker](https://learntopoker.vercel.app/) is an interaction/animation reference only, not a visual design reference.
