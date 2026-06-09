# Form Builder — Laravel + Alpine.js + Tailwind CSS

A drag-and-drop form builder built as part of the UI/Front-End Developer (Laravel Ecosystem) technical assignment.

---

## Tech Stack

| Layer | Choice | Reason |
|-------|--------|--------|
| Backend | Laravel 11 | Required by assignment |
| Templating | Laravel Blade components | Required — no raw HTML inputs |
| Interactivity | Alpine.js v3 | Laravel ecosystem standard, lightweight, zero-build |
| Styling | Tailwind CSS (CDN) | Required — no inline styles, responsive to 1024px |
| Drag-and-Drop | **SortableJS v1.15** | See rationale below |

---

## DnD Library Choice: SortableJS

**Why SortableJS over alternatives:**

| Library | Bundle size | Touch support | No build step | Laravel/Alpine fit |
|---------|------------|---------------|---------------|--------------------|
| **SortableJS** ✅ | ~30KB | ✅ | ✅ CDN | ✅ Excellent |
| Dragula | ~20KB | Partial | ✅ CDN | Good |
| dnd-kit | ~50KB | ✅ | ❌ Needs build | React-focused |
| Vue Draggable | ~15KB | ✅ | ❌ Needs build | Vue-only |

SortableJS was chosen because:
1. Works directly from CDN — no Vite/webpack needed (`php artisan serve` just works)
2. First-class Alpine.js integration pattern — `onEnd` callback maps directly to Alpine state
3. Touch-ready out of the box (mobile drag works)
4. Active maintenance, 30K+ GitHub stars

---

## Run It Locally

Follow these simple steps to get the app up and running:

```bash
# 1. Clone the repo
git clone https://github.com/YOUR_USERNAME/form-builder.git
cd form-builder

# 2. Install PHP dependencies
composer install

# 3. Set up environment
cp .env.example .env
php artisan key:generate

# 4. Start the app
php artisan serve
```

Open [http://localhost:8000](http://localhost:8000) in your browser.

> No npm, no Vite build, and no database setup is required. Tailwind and Alpine.js load directly from CDN.

---

## Features Implemented

### Core (Required)
- [x] **10 Field Types** — Text, Number, Email, Phone, Textarea, Dropdown, Radio, Checkbox, Date, Hidden
- [x] **Drag from palette** to canvas (palette → canvas drop)
- [x] **Drag to reorder** fields on canvas (SortableJS with drag handle)
- [x] **Edit icon** — opens Field Options panel, live preview updates
- [x] **Duplicate icon** — copies field with all config preserved, placed below original
- [x] **Delete icon** — inline confirmation bar (no modal, no toast dependency)
- [x] **Field Options Panel** — all config fields per spec:
  - Label (all fields)
  - Placeholder (Text, Number, Email, Phone, Textarea)
  - Min / Max characters (Text, Textarea)
  - Options list add/remove (Dropdown, Radio, Checkbox)
  - Required toggle (all fields)
  - CSS Class (all fields)
  - Default value (Text, Number, Email, Hidden)
  - Remove element button (all fields)
- [x] **Laravel Blade components** for every field type — no raw HTML inputs
- [x] **Tailwind CSS** — zero inline styles, responsive ≥ 1024px
- [x] **Next button** — exports valid JSON schema (download as `form-schema.json`)

### Bonus Features
- [x] **Undo / Redo** — Ctrl+Z / Ctrl+Y, full history stack
- [x] **Form Preview Mode** — toggle renders live interactive HTML form
- [x] **LocalStorage Persistence** — form state survives page refresh
- [x] **Delete Confirmation** — inline confirm bar before removing a field
- [x] **Drag-over Visual Feedback** — canvas highlights with blue border on drag-over

---

## Project Structure

```
form-builder/
├── app/Http/Controllers/
│   └── FormBuilderController.php     # Passes fieldTypes to view
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php             # Base HTML layout
│   ├── components/fields/
│   │   ├── text-input.blade.php      # <x-fields.text-input />
│   │   ├── number.blade.php          # <x-fields.number />
│   │   ├── email.blade.php           # <x-fields.email />
│   │   ├── phone.blade.php           # <x-fields.phone />
│   │   ├── textarea.blade.php        # <x-fields.textarea />
│   │   ├── dropdown.blade.php        # <x-fields.dropdown />
│   │   ├── radio.blade.php           # <x-fields.radio />
│   │   ├── checkbox.blade.php        # <x-fields.checkbox />
│   │   ├── date.blade.php            # <x-fields.date />
│   │   ├── hidden.blade.php          # <x-fields.hidden />
│   │   ├── preview.blade.php         # Alpine-driven preview renderer
│   │   └── option-input.blade.php    # Reusable right-panel input
│   └── form-builder.blade.php        # Main page + Alpine.js logic
├── routes/web.php
├── sample-output.json                # Sample "Next" button output
└── README.md
```

---

## Assumptions Made

1. **No backend submission** — "Next" exports JSON client-side as per spec ("No API calls needed")
2. **No database** — SQLite configured in `.env` but unused; state is localStorage only
3. **CDN dependencies** — Tailwind, Alpine.js, SortableJS load from CDN to avoid build step
4. **Field ID generation** — done client-side (`Math.random().toString(36)`) as no backend is involved
5. **PHP 8.2+ required** — Laravel 11 minimum requirement

---

## Sample JSON Output

See [`sample-output.json`](./sample-output.json) — this is what clicking "Next" produces for a typical form.

```json
{
  "form": {
    "fields": [
      {
        "id": "abc1234",
        "type": "text",
        "label": "Full Name",
        "placeholder": "Enter your full name",
        "required": true,
        "css_class": "full-width",
        "min_chars": 2,
        "max_chars": 100
      },
      ...
    ]
  }
}
```

---

## Author

Built for the UI/Front-End Developer (Laravel Ecosystem) assignment.
Location: Gurgaon / Delhi NCR
