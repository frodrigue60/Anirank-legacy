# Anirank

Anirank is a modern, premium web application for discovering, exploring, and ranking anime themes (Openings and Endings). It features a sleek glassmorphic UI, deep AniList integration, and a sophisticated data model that distinguishes between animation studios and production committees.

## Key Features

- **🚀 AniList Synchronization**: Automatically fetch anime metadata, themes, and studio/producer relationships directly from AniList.
- **🎬 Studio vs. Producer Distinction**: Separates animation studios (creative) from production companies/committees (business) for better data accuracy.
- **📊 Seasonal Charts**: Browse the latest themes organized by year and season.
- **🏆 Global & Seasonal Rankings**: Real-time leaderboards for top-rated and most-viewed openings and endings, with distinct seasonal scoping.
- **📈 Ranking Trends**: Daily historical snapshots for songs, displaying performance trends (Up/Down/Same/New) with visual indicators for both global and seasonal context.
- **🔢 Centalized Score Formatting**: Flexible rating display system supporting multiple formats (100-point, 10-point, 10-point decimal, 5-point) with centralized logic in the model layer.
- **🎵 Immersive Player**: A dedicated cinema-mode player for seamless theme playback.
- **📁 User Collections**: Create public/private playlists and maintain a personalized favorites list.
- **🔐 Dynamic Role Management**: A robust many-to-many role system (`User <-> Role`) with unified middleware for scalable permissions.
- **📊 Advanced Analytics**: Time-series view tracking for trending content and a premium admin dashboard with interactive charts.
- **🛠️ Standardized Admin Panel**: Robust CRUD control with unified search patterns and RESTful state management.
- **🔔 Unified Notification System**: Real-time feedback via a custom subtle toast notification system, replacing intrusive popups.
- **📝 Streamlined Request Workflow**: Simplified user request system with automatic categorization and manual staff-controlled status tracking.
- **🖼️ Polymorphic Image System**: Centralized media management allowing multiple image types (thumbnails, banners, avatars) for any model with absolute decouple from legacy columns.

## Tech Stack

- **Backend**: Laravel 12.x, PHP 8.2+
- **Frontend**: Blade + Alpine.js (Admin), Livewire 3.x (Public), Tailwind CSS
- **Database**: MySQL 8.0
- **Icons**: Material Symbols (Google)

## Administrative Standards

The project follows a set of strict administrative architectural patterns:

- **Unified Search**: Local searches are integrated directly into the `index` methods via query parameters (`?q=...`), providing bookmarkable results and cleaner controllers.
- **RESTful State Management**: Resource status changes (e.g., toggling a post's publicity or activating a season) use the `PATCH` method and model-level encapsulation (`setCurrent`, `toggleStatus`).
- **Standardized Hierarchies**: Navigation follows a clear `Post -> Song -> Variant -> Video` flow with consistent breadcrumbs and cross-linking between related entities.

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL 8.0

### Installation

1. **Clone the repository**:

    ```bash
    git clone https://github.com/frodrigue60/Anirank.git
    cd Anirank
    ```

2. **Install dependencies**:

    ```bash
    composer install
    npm install
    ```

3. **Environment Setup**:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    _Configure your database settings in the `.env` file._

4. **Migrations & Seeding**:

    ```bash
    php artisan migrate --seed
    ```

5. **Start Development Server**:
    ```bash
    php artisan serve
    npm run dev
    ```

## Development

The project uses **Alpine.js** for reactivity in the admin panel and **Livewire 3** for the public-facing application.

For styling, we use **Tailwind CSS** with a custom high-end glassmorphic theme. See `tailwind.config.js` and `resources/css/app.css` for design tokens.

---

_Built with ❤️ for the anime community._
