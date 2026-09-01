# Range Finder Coffee

A modern coffee shop landing page built with Bootstrap, Vite, and a small set of front-end tooling. The project has been refactored away from the previous CMS-based setup into a lightweight, static marketing site that is easier to run, customize, and deploy.

## Overview

This site is designed for a neighborhood coffee brand with a warm, premium feel. It includes a branded hero, menu highlights, gallery carousel, location details, and a polished responsive layout built from Bootstrap components and custom styling.

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

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) for details.
