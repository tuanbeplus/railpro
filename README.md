# RailPro Theme

RailPro Theme is a custom WordPress child theme built on top of the [Hello Elementor](https://wordpress.org/themes/hello-elementor/) parent theme. It is specifically designed to provide tailored Elementor widgets, advanced styling, and dynamic gallery functionalities for the RailPro website.

## Features

- **Parent Theme**: Built as a child theme of Hello Elementor for maximum performance and compatibility.
- **Custom Elementor Widgets**: Includes a collection of custom widgets registered specifically for RailPro page designs.
- **Dynamic Portfolio & Product Gallery**: Custom AJAX-powered gallery featuring support for images, video embed URLs, and thumbnail generation.
- **Dynamic Taxonomy Query Mapping**: Automated logic to map portfolio categories and products to specific queries (integrating with ACF relationships).
- **Modern Asset Stack**: Built-in styling with Sass (SCSS) and frontend libraries like Isotope, imagesLoaded, and Fancybox.

---

## Directory Structure

```text
railpro/
├── style.css                 # Theme metadata and styles
├── functions.php             # Core theme setup, enqueuing assets, and AJAX handler
├── screenshot.png            # Theme preview image
├── elementor/                # Elementor widgets integration
│   ├── widgets-load.php      # Widget registration class
│   └── widgets/              # Subfolders for each custom widget
│       ├── breadcrumbs/
│       ├── customize-product/
│       ├── portfolio-filter/
│       ├── portfolio-showcase/
│       ├── product-features/
│       ├── product-gallery/
│       ├── product-showcase/
│       ├── sample-widget/
│       ├── table-of-content/
│       └── value-scroll/
└── assets/                   # Theme assets
    ├── css/                  # Compiled CSS files (fancybox.css, main.css)
    ├── imgs/                 # Theme icons and static images
    ├── js/                   # JS scripts and libraries (fancybox, isotope, main.js)
    └── scss/                 # SASS style files
```

---

## Custom Elementor Widgets

The theme registers the following custom widgets inside Elementor:

1. **Breadcrumbs** - Custom breadcrumb trails.
2. **Customize Product** - UI and tools for product customization.
3. **Portfolio Filter** - Dynamic portfolio filtering functionality.
4. **Portfolio Showcase** - Highlighting specific portfolio items.
5. **Product Features** - Showcasing key features of products.
6. **Product Gallery** - Grid and sliding gallery layout with dynamic load-more.
7. **Product Showcase** - Grid/list view of custom product posts.
8. **Table of Content** - Automated table of contents generator.
9. **Value Scroll** - Layout elements triggered by scrolling interaction.
10. **Sample Widget** - A boiler-plate widget for developer reference.

---

## Setup & Installation

### Prerequisites

- WordPress 6.0+
- **Elementor** plugin (Active)
- **Advanced Custom Fields (ACF)** plugin (Active, if using ACF taxonomy mapping and gallery features)
- **Hello Elementor** parent theme installed in `wp-content/themes/hello-elementor`

### Installation Steps

1. Clone or upload the `railpro` folder into the WordPress themes directory:
   ```bash
   wp-content/themes/railpro/
   ```
2. Navigate to your WordPress Admin Dashboard.
3. Go to **Appearance > Themes**.
4. Locate and activate **RailPro Theme**.

---

## Development & Customization

### Styling with SASS/SCSS

The styling is modularly structured using Sass in `assets/scss/`. To make changes to the styling:
1. Modify files inside `assets/scss/` (e.g. `_header.scss`, `_home.scss`, `main.scss`).
2. Compile `assets/scss/main.scss` to `assets/css/main.css`.
   - *Example using command-line Sass compiler:*
     ```bash
     sass assets/scss/main.scss assets/css/main.css --watch
     ```
   - Alternatively, you can use the **VSCode Live Sass Compiler** extension or similar tools.

### JavaScript Libraries

The theme enqueues:
- **Isotope** (`isotope.pkgd.min.js`) for filter layouts.
- **imagesLoaded** (`imagesloaded.pkgd.min.js`) to detect when images are loaded.
- **Fancybox** (`fancybox.umd.js` & `fancybox.css`) for lightbox overlays.
- Theme custom code is located in `assets/js/main.js`.
