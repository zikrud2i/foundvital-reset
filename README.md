# FoundVital Reset – WordPress theme

Custom microsite theme for **reset.foundvital.com** (separate WordPress install). Does not touch the main foundvital.com theme.
Plugins: Secure Custom Fields (page copy), Rank Math (SEO), Forminator (optional form replacement). No page builder, no WooCommerce.

## 1. Install

1. Create the `reset` subdomain, point it at a new WordPress install, enable SSL (HTTPS everywhere).
2. Zip the `foundvital-reset` folder and upload via Appearance > Themes, or copy it to `wp-content/themes/`.
3. Install & activate **Secure Custom Fields**, **Rank Math SEO** (and Forminator only if you want it to replace the built-in forms).
4. Activate the theme. On activation it automatically:
   - sets permalinks to `/%postname%/` (if none set),
   - creates every page with the right template and URL (`/checklist`, `/quiz`, `/results/foundations|routine|coaching`, `/masterclass`, `/fit-call`, `/enroll`, `/thank-you/checklist|masterclass|fit-call|enrollment`),
   - sets the static front page, creates draft Privacy/Terms/Clarity pages, FAQ drafts and sample quiz questions.
   Re-run any time (non-destructive) from **Reset Settings > "Run page & content setup again"**.
5. Reset Settings has a **Launch readiness** panel listing everything still pending.

## 2. Configure (Reset Settings)

| Tab | What to enter |
|---|---|
| Links & checkout | Calendly URL (pre-filled), Stripe Payment Links (Reset, Clarity $100), OutfitMD shop URL, main-site URL, support email |
| Pricing | Approved price + terms (hidden until "Show pricing block" is ticked), check-in details, Completion Guarantee, FoundVital Sustain terms |
| Founder & brand | Portrait, credentials, experience statement (hidden until entered), video, logo override, socials |
| Kit & forms | Kit API key (prefer `define('FVR_KIT_API_KEY','…');` in wp-config.php), Form IDs, Tag IDs, optional Turnstile |
| Checklist & masterclass | PDF URL, masterclass title/date/attendance/replay, "replay mode" |
| Quiz | Go-live switch, required email step (or off), tie-break |

Analytics (GA4 + Search Console) is handled by the **Site Kit by Google** plugin, not the theme. The theme sends its events (quiz completion, sign-ups, bookings) to the Google tag Site Kit loads, and never sends answers or form fields. Ad pixels, if added later, must stay off the quiz, results, booking and client pages.

Page copy (hero, intro, lists) is edited in each page's **SCF field groups**; defaults come from the client's messaging guide. FAQ = **Reset FAQ** menu; quiz = **Reset Quiz** menu (`Answer text | A|B|C` per line).

## 3. Funnel wiring

- **Checklist / Masterclass forms** → server-side Kit API (subscribe + form + tag) → redirect to `/thank-you/…` **only after Kit confirms**. Honeypot, signed time-token, nonce, per-IP rate limit, optional Turnstile.
- **Quiz** → scored server-side → `/results/{foundations|routine|coaching}`. Optional email (with consent) is tagged in Kit with the *result only*; answers are never stored or sent.
- **Calendly** → `/fit-call` (external button + optional lazy embed). Booking event fires only on Calendly's `calendly.event_scheduled`.
- **Stripe** → hosted Payment Links only (`/enroll`, `/clarity-coaching`). No card data touches WordPress. The $99 OutfitMD purchase is a plain external link and is never bundled.

### Kit setup outside WordPress
Create forms + tags (checklist, masterclass, quiz-any, quiz A/B/C) and sequences: checklist welcome/nurture; 3 quiz nurtures triggered by tags A/B/C; masterclass confirmation, reminders, replay. Paste the numeric IDs into the Kit tab.

## 4. Rank Math suggestions
Home: "FoundVital Reset™ | 12-Week Midlife Health Coaching Program" · Checklist: "Free Midlife Health Reset Checklist" · Quiz: "What's Your Best Next Step for Midlife Health? | Free Quiz" · Masterclass: "Free Masterclass: The Midlife Health Reset" · Fit call: "Book a Reset Fit Conversation". Thank-you, quiz-result and `/enroll` pages are automatically `noindex,nofollow` (theme + Rank Math filter). Avoid copying foundvital.com text verbatim.

## 5. Confirmed vs. pending
**Confirmed from client docs:** page structure/copy/tone, Calendly link, $99 + $100 split (FoundVital collects only $100), Kit + Calendly + Stripe roles.
**Pending client approval (nothing invented):** Reset price/payment terms/refund policy; check-in frequency/format/provider; weekly time; equipment answer; post-week-12 and Optimize terms; credentials & years of experience verification; last name; quiz questions & routing (sample content, quiz hidden until "live"); masterclass date/link/replay; checklist PDF; physician-review wording for Clarity; **location of the Clarity page** (this theme ships the template; the guide proposes foundvital.com/lab-consultation); Privacy/Terms text; Stripe→program-access fulfilment (Stripe does **not** replace GoHighLevel's CRM/automation/membership delivery; define this before launch); GoHighLevel vs Stripe final decision.

## 6. Known limits (be honest)
- Kit calls are written to Kit's documented v4 API but **not tested against a live account**. Test with a throw-away address; check `debug.log` for `[fvr] Kit API … returned HTTP`.
- Nonces on aggressively page-cached pages can expire: exclude the form pages from full-page cache or rely on the timing token + honeypot (nonce failure shows a "reload" message).
- Google Fonts (Poppins) loads from Google; self-host for stricter privacy/perf.
- No verified-payment event exists yet. For that, add a Stripe webhook endpoint (not included) and fire `payment_complete` server-side.
- Behind a proxy/CDN, make sure `REMOTE_ADDR` is the visitor IP (rate limiting).

## 7. QA checklist
Mobile (360/390/768/1280): hero CTAs visible without scroll · every CTA URL · checklist & masterclass submit success/invalid email/no consent/Kit down · quiz keyboard use, no-JS fallback, all three results, tie-break, incomplete answers · Kit tags per segment · Calendly button + embed + booking event · Stripe links open hosted checkout in new tab · thank-you pages noindex · Site Kit events arrive in GA4 (no personal data) · axe/WAVE pass, focus visible, reduced-motion · Lighthouse ≥90 · pricing hidden when unchecked · editor can change copy in SCF/Settings · 404 page.
