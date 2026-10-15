# Feature coverage

CVera is free and public. There are no payments, subscription tiers, paid template locks or AI credit plans. Optional accounts add cross-device persistence; core tools do not require an account.

| Capability | Implementation | External configuration |
| --- | --- | --- |
| Animated interactive landing | Pointer-responsive Three.js CV scanner; reduced-motion and WebGL fallback | None |
| General ATS guidance | Eight explainable content checks and actionable recommendations | None |
| Live builder score | Debounced analysis while editing | None |
| Job matching | Explicit skill dictionary, found/missing terms, 65% content + 35% term match | None |
| Job image OCR | Browser-side Turkish + English Tesseract; editable extracted text | None |
| CV import | Browser-side PDF, DOCX and TXT extraction | None |
| CV editor | Structured content fields, live preview, 25 layout presets including sidebars, timelines and header bands | None |
| PDF output | Browser print to PDF; searchable text | None |
| Local drafts and history | Explicit device save, up to 20 previous local versions | None |
| Cloud documents and versions | Optional Laravel session account; owner-isolated revisions and restore | SQLite by default |
| Separate CV variants | Duplicate saved document in workspace; original remains intact | Optional account |
| Cover letters | Fact-based basic draft, editable text/PDF output, optional AI tailoring | AI key for AI mode only |
| Personal portfolio | 18 design presets, live preview, profile photos, font/color selection, standalone HTML export | None |
| Portfolio sharing | Explicit publish/unpublish with an unguessable public URL | Optional account + public hosting |
| Browser extension | Manifest V3 active-tab extraction and user-reviewed transfer via URL fragment | Load unpacked; not store-published |
| Career resources | Six guides, 12 occupation guidance cards, 12-point manual checklist | None |
| AI summary, CV optimization and import | Server-side compatible chat-completions integration; mandatory user review in UI | AI_API_KEY, optional base URL/model |
| CV advisor | CV-context question/answer screen | AI configuration |

## Design references

The CV gallery contains the 25 layouts visible in the supplied CV-design reference. The portfolio gallery contains the 18 designs with visible previews in the portfolio reference; the last three blank reference cards are excluded. Template names differ from the reference names. Saved CV template identifiers remain compatible.

## Deliberate limits

- ATS scores are transparent heuristics, not employer ATS scores or a hiring guarantee. Layout, columns, tables and real employer parsing are not scored.
- Matching uses a maintained dictionary; unknown job terms are not automatically discovered. A job with no dictionary hits returns no match score.
- OCR quality depends on image legibility. Image text must be reviewed; scanned PDF CVs need a text-containing replacement.
- AI calls are implemented and tested with a mock provider. A live provider key has not been supplied or tested. The UI reports this honestly. AI output can contain mistakes and must be reviewed before applying.
- The 25 CV layouts adapt the visible arrangements from the supplied design reference, with distinct names. Two-column designs prioritize visual presentation; choose a single-column layout for ATS-focused applications.
- PDF export uses the browser’s print dialog; automated print-state tests do not operate the OS save dialog.
- The extension is a downloadable unpacked package. It has not been published to Chrome or Edge stores, and third-party job site layouts are not guaranteed.
- A shared portfolio needs a publicly reachable deployment to be accessible outside localhost.
- No transactional email, email verification, password-reset mail or production deployment is configured.
