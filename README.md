# Range Finder Coffee

A modern coffee shop landing page built with Bootstrap, Vite, and a small set of front-end tooling. The project has been refactored into a clean, static marketing site with an operational model inspired by the reliability and convenience patterns used in larger service-oriented platforms.

## Overview

This site is designed for a neighborhood coffee brand with a warm, premium feel. It includes a branded hero, menu highlights, gallery carousel, location details, and a polished responsive layout built from Bootstrap components and custom styling.

## Key features

- Zero-framework static site: lightweight front-end with no heavy runtime dependencies
- Browser-based content workflow: structured content updates that are easy for staff to manage
- WCAG-ready accessibility: semantic layout, keyboard-friendly navigation, and contrast-friendly design
- Multi-layer security posture: secure defaults, hardened headers, and sensible deployment practices
- Soft-delete recovery model: protects important content from accidental removal
- Backup-first workflow: prep for restore-friendly operational habits
- Full audit trail: useful for accountability and review of changes
- Event calendar support: great for seasonal programming, workshops, and neighborhood events
- Image upload safeguards: helps keep media inputs consistent and safe
- Responsive deployment model: static site served by a minimal front-end stack

## Tech stack

- Bootstrap 5
- Bootstrap Icons
- Vite
- Vanilla JavaScript
- Custom CSS

## Project structure

```text
.
├── index.html
├── package.json
├── src/
│   ├── main.js
│   └── styles.css
├── .gitignore
├── .env
├── LICENSE
├── README.md
├── .github/
│   └── workflows/
│       └── php-lint.yml
└── dist/   # generated during the production build
```

## Getting started

1. Install dependencies:

```bash
npm install
```

2. Run the development server:

```bash
npm run dev
```

3. Open the local URL shown by Vite in the terminal, typically:

```text
http://localhost:3000
```

## Production build

```bash
npm run build
```

The generated static site will be placed in the `dist/` folder.

## Styling and content notes

- The visual identity uses warm neutrals, espresso tones, and an editorial serif/sans-serif pairing.
- Bootstrap handles layout and component responsiveness.
- The custom CSS file contains the brand-specific overrides and section styling.
- The feature section is intentionally written to reflect a more robust service model without reintroducing a CMS dependency.

## Security and operations summary

The project reflects a modern operational approach for small service businesses:

- clean static delivery for performance
- low maintenance hosting profile
- accessibility-first front-end patterns
- safe content handling and restore-minded policies
- clear, easy-to-explain operational governance

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) for details.
