# Anirank

Anirank is a modern, premium web application for discovering, exploring, and ranking anime themes (Openings and Endings). It features a sleek glassmorphic UI, deep AniList integration, and a sophisticated data model that distinguishes between animation studios and production committees.

## Key Features

- **🚀 AniList Synchronization**: Automatically fetch anime metadata, themes, and studio/producer relationships directly from AniList.
- **🎬 Studio vs. Producer Distinction**: Separates animation studios (creative) from production companies/committees (business) for better data accuracy.
- **📊 Seasonal Charts**: Browse the latest themes organized by year and season.
- **🏆 Global & Seasonal Rankings**: Real-time leaderboards for top-rated and most-viewed openings and endings, with distinct seasonal scoping and daily historical snapshots.
- **📈 Ranking Trends**: Daily performance tracking (Up/Down/Same/New) for both global and seasonal rankings, with visual trend indicators.
- **🏅 Badges & Rewards System**: Centralized system to reward user participation with unique badges, managed directly from the admin panel.
- **🖼️ High-Performance Media Rendering**: Standardized `<x-ui.image>` component with lazy-loading, error fallbacks, and automated avatar generation for users and artists.
- **🔢 Centralized Score Formatting**: Flexible rating display system supporting multiple formats (100-point, 10-point, 10-point decimal, 5-point) with centralized logic in the model layer.
- **🎵 Immersive Player**: A dedicated cinema-mode player for seamless theme playback.
- **📁 User Collections**: Create public/private playlists and maintain a personalized favorites list.
- **🔐 Dynamic Role Management**: A robust many-to-many role system (`User <-> Role`) with unified middleware for scalable permissions.
- **📊 Advanced Analytics**: Time-series view tracking for trending content and a premium admin dashboard with interactive charts.
- **🛠️ Standardized Admin Panel**: Robust CRUD control with unified search patterns and RESTful state management.
- **⚡ Vite Optimization**: Streamlined asset configuration to minimize production bundle size and improve build performance.
- **🛡️ Audit Log System**: Comprehensive tracking of all resource mutations (Create, Update, Delete) for core models (Anime, Song, Artist, etc.).
- **🔔 Unified Notification System**: Real-time user alerts for social interactions (replies, follows) and staff announcements via a centralized JSON-based system.
- **🤝 Social Connectivity**: Dynamic user-to-user follows and a global activity feed capturing community interactions.
- **🏷️ Automated Genre System**: Multi-layered categorization with direct AniList synchronization and advanced filtering capabilities across all database views.
- **🔒 Livewire Request Guard**: Application-wide optimization pattern that blocks redundant requests and disables interactive elements during data loading for a robust UX.
- **🚀 Optimized Dispatching**: Migrated inter-component events (like modal triggers) to client-side dispatches, eliminating unnecessary server roundtrips.
- **⚡ Backend Query Optimization**: Systematic eager loading implementation across models and controllers (e.g., `Song`, `RankingTable`) to resolve N+1 bottlenecks and improve performance during large list iterations.
- **⏳ Background AniList Sync**: Migrated high-latency AniList API synchronization loops to a queued background job (`SyncAnimeAnilistJob`) to prevent gateway timeouts and improve admin responsiveness.
- **🛡️ API Data Masking**: Standardized use of Laravel API Resources (`AnimeResource`) to control data exposure and prevent leaking internal database structures in JSON responses.
- **❤️ Standardized UX**: Real-time favorite and interaction feedback using consistent Material Symbols logic across all discovery views.
- **💬 Livewire Request Modal**: Fully interactive and reactive user request system, replacing legacy modal patterns.
- **♾️ Reliable Infinite Scroll**: All paginated lists use Alpine's `x-intersect.once` with dynamic `wire:key` for stable, loop-free infinite scrolling across 11 Livewire components.
- **💎 High-Fidelity Skeletons**: Sophisticated shimmering placeholders that accurately mirror the final UI layout, including circular avatars, specific grid ratios, and shimmering titles to eliminate layout shifts.
- **⚡ Performance First Architecture**: Systematic use of Livewire's `#[Lazy]` loading and `#[Computed]` properties across all listing components for an instant-load feel and optimized database interaction.
- **✅ Livewire V3 Compliance**: Full audit and cleanup of deprecated patterns (`wire:submit.prevent`, `#[On('loadMore')]`, static `wire:key`), ensuring all components follow Livewire 3.x best practices.

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

5. **Storage Setup** (required for local media access):

    ```bash
    php artisan storage:link
    ```

    > This creates a symlink from `public/storage` to `storage/app/public`. Without it, images/videos won't load when using `FILESYSTEM_DISK=public`.

6. **Start Development Server**:
    ```bash
    php artisan serve
    npm run dev
    ```

## Development

The project uses **Alpine.js** for reactivity in the admin panel and **Livewire 3** for the public-facing application.

For styling, we use **Tailwind CSS** with a custom high-end glassmorphic theme. See `tailwind.config.js` and `resources/css/app.css` for design tokens.

## Future Roadmap

The following features are planned to enhance user engagement and social interaction:

- **🎮 Gamification**: Theme guessing quizzes, seasonal predictions, and automated user milestones/achievements.
- **🤝 Social Features**: Song battles (1v1 brackets) for dynamic rankings and a real-time activity feed of community interactions.
- **🎧 Advanced UX**: A "Non-Stop" radio-style player and automated AniList profile synchronization for personalized recommendations.

---

_Built with ❤️ for the anime community._
