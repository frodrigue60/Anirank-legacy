@extends('layouts.app')

@section('meta')
    <title>{{ $playlist->name }} | Playlists</title>
    <meta name="description" content="Now playing: {{ $playlist->name }}. Enjoy your custom anime music collection.">
@endsection

@push('styles')
    <style>
        .glass-panel {
            background: rgba(29, 20, 40, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .neon-border {
            box-shadow: 0 0 15px rgba(127, 19, 236, 0.3), inset 0 0 5px rgba(127, 19, 236, 0.2);
        }

        .neon-text {
            text-shadow: 0 0 8px rgba(178, 77, 255, 0.6);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(127, 19, 236, 0.3);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #7f13ec;
        }

        #player-wrapper {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8), 0 0 30px rgba(127, 19, 236, 0.1);
        }
    </style>
@endpush

@section('content')
    <div
        class="flex flex-col bg-background-dark text-white font-display antialiased overflow-hidden shadow-2xl shadow-primary/5 min-h-[calc(100vh-120px)] p-0 md:px-8">
        {{-- Player layout content --}}
        <main class="flex-1 grid grid-cols-1 lg:grid-cols-10 overflow-hidden gap-6 h-full min-h-0">
            {{-- Left Section: Player & Info (6/10) --}}
            <aside
                class="lg:col-span-6 flex flex-col px-4 md:px-8 mx-auto overflow-y-auto bg-linear-to-b from-background-dark to-surface-darker custom-scrollbar pb-8 rounded-2xl border border-white/5">
                <div class="relative aspect-video rounded-2xl overflow-hidden bg-black shadow-2xl mb-8 mt-6 group ring-1 ring-white/10"
                    id="player-wrapper">
                    <div id="player-container" class="w-full h-full flex items-center justify-center">
                        {{-- Initializing State --}}
                        <div class="flex flex-col items-center gap-4 opacity-20">
                            <div class="w-12 h-12 rounded-full animate-spin">
                            </div>
                            <p class="text-sm font-black uppercase tracking-widest text-white">Initializing Player...</p>
                        </div>
                    </div>

                    {{-- Play/Pause Overlay --}}
                    <div id="player-overlay"
                        class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm z-20 cursor-pointer">
                        <div class="flex items-center gap-8 translate-y-2">
                            <button id="prev-overlay-btn"
                                class="w-12 h-12 rounded-full bg-white/10 text-white flex items-center justify-center hover:bg-white/20 transition-all transform active:scale-95">
                                <span class="material-symbols-outlined text-3xl">skip_previous</span>
                            </button>
                            <div
                                class="w-16 h-16 rounded-full bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/50 transform active:scale-90 transition-transform">
                                <span class="material-symbols-outlined text-4xl filled" id="overlay-icon">play_arrow</span>
                            </div>
                            <button id="next-overlay-btn"
                                class="w-12 h-12 rounded-full bg-white/10 text-white flex items-center justify-center hover:bg-white/20 transition-all transform active:scale-95">
                                <span class="material-symbols-outlined text-3xl">skip_next</span>
                            </button>
                        </div>

                        {{-- New Fullscreen Location --}}
                        <button id="fullscreen-btn"
                            class="absolute bottom-6 right-6 w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white/60 hover:text-white transition-all bg-white/5 hover:bg-white/10">
                            <span class="material-symbols-outlined">fullscreen</span>
                        </button>
                    </div>

                    {{-- Seek Bar --}}
                    <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-white/20 cursor-pointer group/seek z-30">
                        <input type="range" id="seek-slider" min="0" max="100" value="0"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div id="seek-progress" class="h-full bg-primary shadow-[0_0_10px_#7f13ec] transition-all"
                            style="width: 0%"></div>
                    </div>
                </div>

                <div class="flex flex-col gap-10">
                    <div class="flex justify-between items-start gap-8">
                        <div class="flex-1 min-w-0">
                            <h1 id="current-song-title"
                                class="text-5xl font-black text-white tracking-tight leading-tight mb-4 truncate drop-shadow-xl">
                                Loading...</h1>
                            <div class="flex flex-col items-start gap-2">
                                <p id="current-anime-title" class="text-2xl font-black text-primary truncate uppercase tracking-tight leading-none"></p>
                                <p id="current-artists" class="text-lg text-white/50 font-bold truncate"></p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-3 shrink-0">
                            @if(count($queue) > 0)
                                <div class="bg-white/5 p-4 rounded-2xl border border-white/5 backdrop-blur-sm">
                                    <livewire:song-interactions :songId="$queue[0]['song_id']" mode="score" />
                                </div>
                            @endif
                            <div
                                class="flex items-center gap-2 px-3 py-1.5 bg-primary/10 rounded-full border border-primary/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                                <p id="current-song-type" class="text-[10px] font-black text-primary uppercase tracking-widest leading-none"></p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-wrap items-center justify-between p-6 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-xl gap-6">
                        <div class="flex-1">
                            @if(count($queue) > 0)
                                <livewire:song-interactions :songId="$queue[0]['song_id']" />
                            @endif
                        </div>
                        <div class="flex items-center gap-6">
                            <div class="flex items-center gap-5 px-5 py-2.5 bg-black/40 rounded-2xl border border-white/5">
                                <span id="current-time" class="text-sm font-black tabular-nums text-white">0:00</span>
                                <span class="text-white/20 font-bold">/</span>
                                <span id="duration" class="text-sm font-black tabular-nums text-white/40">0:00</span>
                            </div>
                            @auth
                                @if($playlist->user_id === Auth::id())
                                    <a href="{{ route('playlists.edit', $playlist->id) }}"
                                        class="w-12 h-12 rounded-2xl bg-white text-black flex items-center justify-center hover:bg-primary hover:text-white transition-all shadow-2xl group/edit"
                                        title="Edit Playlist">
                                        <span class="material-symbols-outlined text-[24px] group-hover:rotate-12 transition-transform">edit</span>
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>

                    {{-- Description/About Panel --}}
                    <div class="p-6 rounded-2xl bg-surface-dark/40 border border-white/5 backdrop-blur-sm">
                        <h3 class="text-xs font-bold text-white/50 uppercase tracking-widest mb-4">About this playlist</h3>
                        <p id="playlist-description" class="text-white/80 leading-relaxed font-medium">
                            {{ $playlist->description ?? 'No description provided for this collection.' }}
                        </p>
                        {{-- themes featured here --}}
                        {{-- <div class="mt-6 flex items-center gap-4">
                            <div class="flex -space-x-3">
                                @foreach ($queue->take(4) as $item)
                                <div
                                    class="w-8 h-8 rounded-full border-2 border-background-dark overflow-hidden bg-surface-darker">
                                    <img src="{{ $item->banner_url }}" class="w-full h-full object-cover">
                                </div>
                                @endforeach
                            </div>
                            <span class="text-xs font-bold text-white/40 uppercase tracking-widest">+ Themes featured
                                here</span>
                        </div> --}}
                    </div>
                {{-- Player Controls Bar --}}
                <div class="mt-auto pt-8 border-t border-white/5 flex flex-wrap items-center justify-between gap-6">
                    <div class="flex items-center gap-6 group">
                        <button id="mute-btn"
                            class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-white/10 hover:text-primary transition-all shadow-xl">
                            <span class="material-symbols-outlined text-[28px]">volume_up</span>
                        </button>
                        <div
                            class="w-40 h-2 bg-white/10 rounded-full relative overflow-hidden group-hover:h-3 transition-all cursor-pointer">
                            <input type="range" id="volume-slider" min="0" max="100" value="100"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div id="volume-progress"
                                class="h-full bg-linear-to-r from-primary to-primary/60 shadow-[0_0_20px_rgba(127,19,236,0.3)] pointer-events-none"
                                style="width: 100%"></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button id="prev-btn"
                            class="w-16 h-16 rounded-3xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-white/10 text-white/40 hover:text-white transition-all shadow-xl group/btn">
                            <span
                                class="material-symbols-outlined text-[32px] group-hover:-translate-x-1 transition-transform">skip_previous</span>
                        </button>
                        <button id="play-pause-btn"
                            class="w-20 h-20 rounded-[2.5rem] bg-primary flex items-center justify-center hover:scale-105 hover:shadow-[0_0_40px_rgba(127,19,236,0.4)] shadow-2xl transition-all transform active:scale-95">
                            <span class="material-symbols-outlined filled text-white text-[48px]">play_arrow</span>
                        </button>
                        <button id="next-btn"
                            class="w-16 h-16 rounded-3xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-white/10 text-white/40 hover:text-white transition-all shadow-xl group/btn">
                            <span
                                class="material-symbols-outlined text-[32px] group-hover:translate-x-1 transition-transform">skip_next</span>
                        </button>
                    </div>
                </div>
            </aside>

            {{-- Right Section: Queue (4/10) --}}
            <section
                class="lg:col-span-4 flex flex-col bg-surface-darker/50 backdrop-blur-md overflow-hidden shadow-2xl rounded-2xl border border-white/5 h-full min-h-0">
                {{-- Desktop Player Bottom Bar --}}
                <div class="p-6 bg-surface-dark/20 border-b border-white/5 flex items-center justify-between sticky top-0 z-10 backdrop-blur-xl">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">queue_music</span>
                        <h2 class="text-sm font-black uppercase tracking-[0.2em] text-white/90">Up Next</h2>
                    </div>
                    <span id="queue-count" class="text-[10px] font-black px-2 py-1 bg-white/5 rounded-md text-white/40">0 items</span>
                </div>
                {{-- <div
                    class="p-6 flex items-center justify-between border-b border-white/5 bg-surface-dark/20 backdrop-blur-xl">
                    <h2 class="text-xl font-bold flex items-center gap-3 tracking-tight">
                        <span class="w-1.5 h-6 bg-primary rounded-full shadow-[0_0_10px_#7f13ec]"></span>
                        Next in Playlist
                    </h2>
                    <div class="flex items-center gap-3">
                        <span id="queue-count"
                            class="text-[10px] font-black text-primary bg-primary/10 px-2 py-1 rounded uppercase tracking-widest">
                            {{ count($queue) }} Tracks
                        </span>
                    </div>
                </div> --}}

                {{-- Scrollable List --}}
                <div id="queue-list" class="flex-1 overflow-y-auto p-6 space-y-3 custom-scrollbar bg-background-dark/30">
                    {{-- Populated by JS --}}
                </div>


            </section>
        </main>
    </div>

    <script>
        // Data from Laravel
        window.playlistQueue = @json($queue);
        window.currentIndex = 0;
    </script>
    <script>
        class PlaylistPlayer {
            constructor() {
                this.queue = window.playlistQueue || [];
                this.currentIndex = window.currentIndex || 0;

                // Elements
                this.playerContainer = document.getElementById('player-container');
                this.titleEl = document.getElementById('current-song-title');
                this.animeEl = document.getElementById('current-anime-title');
                this.artistEl = document.getElementById('current-artists');
                this.typeEl = document.getElementById('current-song-type');
                this.ratingEl = document.getElementById('current-rating');
                this.timeEl = document.getElementById('current-time');
                this.durationEl = document.getElementById('duration');
                this.overlayIcon = document.getElementById('overlay-icon');
                this.playerOverlay = document.getElementById('player-overlay');
                this.prevOverlayBtn = document.getElementById('prev-overlay-btn');
                this.nextOverlayBtn = document.getElementById('next-overlay-btn');

                this.playPauseBtn = document.getElementById('play-pause-btn');
                this.prevBtn = document.getElementById('prev-btn');
                this.nextBtn = document.getElementById('next-btn');
                this.muteBtn = document.getElementById('mute-btn');
                this.fullscreenBtn = document.getElementById('fullscreen-btn');

                this.seekSlider = document.getElementById('seek-slider');
                this.seekProgress = document.getElementById('seek-progress');
                this.volumeSlider = document.getElementById('volume-slider');
                this.volumeProgress = document.getElementById('volume-progress');

                this.queueList = document.getElementById('queue-list');

                // State
                this.currentPlayer = null;
                this.isPlaying = false;
                this.ytPlayer = null;
                this.youtubeTimeInterval = null;
                this.lastVolume = 100;

                this.init();
            }

            init() {
                if (this.queue.length === 0) {
                    this.showEmpty();
                    return;
                }

                this.renderQueue();
                this.loadCurrent();
                this.bindEvents();

                // Keyboard shortcuts
                document.addEventListener('keydown', (e) => {
                    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
                    if (e.key === ' ') {
                        e.preventDefault();
                        this.togglePlay();
                    } else if (e.key === 'ArrowRight') this.next();
                    else if (e.key === 'ArrowLeft') this.prev();
                });
            }

            loadCurrent() {
                const item = this.queue[this.currentIndex];
                if (!item) return;

                // UI Update
                this.titleEl.textContent = item.title || 'Unknown Theme';
                if (this.animeEl) this.animeEl.textContent = item.anime || 'Unknown Anime';
                if (this.artistEl) this.artistEl.textContent = item.artists || 'Unknown Artist';
                if (this.typeEl) this.typeEl.textContent = item.song_type || 'Theme';
                if (this.ratingEl) this.ratingEl.textContent = item.average_rating || 'N/A';

                // Sync Livewire
                if (window.Livewire) {
                    Livewire.dispatch('songChanged', {
                        songId: item.song_id
                    });
                }

                this.highlightCurrent();

                // Clear and Reset
                this.playerContainer.innerHTML = '';
                this.currentPlayer = null;
                this.ytPlayer = null;
                this.timeEl.textContent = '0:00';
                this.durationEl.textContent = '--:--';
                this.seekProgress.style.width = '0%';
                this.stopYouTubeTimeTracking();

                if (item.type === 'embed') {
                    const embedUrl = this.parseEmbedInput(item.video_url);
                    if (embedUrl) {
                        this.loadYouTubeEmbed(embedUrl);
                    } else {
                        this.showError('Invalid video source');
                    }
                } else {
                    this.loadLocalVideo(item.video_url);
                }
            }

            highlightCurrent() {
                document.querySelectorAll('.queue-item').forEach((el, i) => {
                    const isActive = i === this.currentIndex;
                    el.classList.toggle('bg-primary/10', isActive);
                    el.classList.toggle('border-primary/30', isActive);
                    el.classList.toggle('bg-surface-darker/50', !isActive);
                    el.classList.toggle('border-white/5', !isActive);

                    const activeBadge = el.querySelector('.active-badge');
                    if (activeBadge) activeBadge.classList.toggle('hidden', !isActive);
                });
            }

            parseEmbedInput(input) {
                if (!input) return null;
                const str = input.trim();
                const embedMatch = str.match(/<embed[^>]+src=["']([^"']+)["']/i) || str.match(
                    /<iframe[^>]+src=["']([^"']+)["']/i);
                if (embedMatch) return this.buildEmbedUrl(embedMatch[1]);
                if (/^https?:\/\//i.test(str)) return this.buildEmbedUrl(str);
                return null;
            }

            buildEmbedUrl(url) {
                if (/youtube\.com|youtu\.be/i.test(url)) {
                    const id = this.extractYouTubeId(url);
                    return id ? `https://www.youtube.com/embed/${id}?autoplay=1&rel=0&modestbranding=1&enablejsapi=1` :
                        null;
                }
                return url;
            }

            extractYouTubeId(url) {
                if (!url) return null;
                const patterns = [
                    /(?:v=|\/v\/|embed\/|v\/|youtu\.be\/|\/v=|^v=)([^#\&\?]{11})/,
                    /[?&]v=([^#\&\?]{11})/,
                    /embed\/([^#\&\?]{11})/,
                    /\/([^#\&\?]{11})$/
                ];
                for (const pattern of patterns) {
                    const match = url.match(pattern);
                    if (match && match[1]) return match[1];
                }
                // Fallback for raw IDs
                if (url.length === 11 && !url.includes('/') && !url.includes('.')) return url;
                return null;
            }

            loadYouTubeEmbed(url) {
                const id = this.extractYouTubeId(url);
                if (!id) return;

                if (!window.YT) {
                    const tag = document.createElement('script');
                    tag.src = "https://www.youtube.com/iframe_api";
                    const firstScriptTag = document.getElementsByTagName('script')[0];
                    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
                }

                const container = document.createElement('div');
                container.id = 'yt-player-instance';
                container.className = "w-full h-full";
                this.playerContainer.appendChild(container);

                const createPlayer = () => {
                    this.ytPlayer = new YT.Player('yt-player-instance', {
                        videoId: id,
                        playerVars: {
                            autoplay: 1,
                            controls: 0,
                            modestbranding: 1,
                            rel: 0,
                            iv_load_policy: 3
                        },
                        events: {
                            onReady: (e) => {
                                this.isPlaying = true;
                                this.updatePlayBtnIcon();
                                this.durationEl.textContent = this.formatTime(e.target.getDuration());
                                this.ytPlayer.setVolume(this.volumeSlider.value);
                                this.startYouTubeTimeTracking();
                            },
                            onStateChange: (e) => {
                                if (e.data === YT.PlayerState.ENDED) this.next();
                                else if (e.data === YT.PlayerState.PLAYING) {
                                    this.isPlaying = true;
                                    this.updatePlayBtnIcon();
                                    this.startYouTubeTimeTracking();
                                } else if (e.data === YT.PlayerState.PAUSED) {
                                    this.isPlaying = false;
                                    this.updatePlayBtnIcon();
                                    this.stopYouTubeTimeTracking();
                                }
                            }
                        }
                    });
                };

                if (window.YT && YT.Player) createPlayer();
                else window.onYouTubeIframeAPIReady = createPlayer;
            }

            loadLocalVideo(url) {
                const video = document.createElement('video');
                video.src = url;
                video.controls = false;
                video.autoplay = true;
                video.volume = this.volumeSlider.value / 100;
                video.className = 'w-full h-full object-contain';
                this.playerContainer.appendChild(video);
                this.currentPlayer = video;

                video.addEventListener('loadedmetadata', () => this.durationEl.textContent = this.formatTime(video
                    .duration));
                video.addEventListener('timeupdate', () => {
                    this.timeEl.textContent = this.formatTime(video.currentTime);
                    const percent = (video.currentTime / video.duration) * 100;
                    this.seekProgress.style.width = percent + '%';
                    this.seekSlider.value = percent;
                });
                video.addEventListener('ended', () => this.next());
                video.addEventListener('play', () => {
                    this.isPlaying = true;
                    this.updatePlayBtnIcon();
                });
                video.addEventListener('pause', () => {
                    this.isPlaying = false;
                    this.updatePlayBtnIcon();
                });
                video.play().catch(() => {});
            }

            togglePlay() {
                if (this.ytPlayer && this.ytPlayer.getPlayerState) {
                    const state = this.ytPlayer.getPlayerState();
                    state === YT.PlayerState.PLAYING ? this.ytPlayer.pauseVideo() : this.ytPlayer.playVideo();
                } else if (this.currentPlayer) {
                    this.currentPlayer.paused ? this.currentPlayer.play() : this.currentPlayer.pause();
                }
            }

            updatePlayBtnIcon() {
                const icon = this.playPauseBtn.querySelector('.material-symbols-outlined');
                const overlayIcon = this.overlayIcon;

                const newIcon = this.isPlaying ? 'pause' : 'play_arrow';
                if (icon) icon.textContent = newIcon;
                if (overlayIcon) overlayIcon.textContent = newIcon;
            }

            next() {
                if (this.currentIndex < this.queue.length - 1) {
                    this.currentIndex++;
                    this.loadCurrent();
                }
            }
            prev() {
                if (this.currentIndex > 0) {
                    this.currentIndex--;
                    this.loadCurrent();
                }
            }
            playIndex(index) {
                this.currentIndex = index;
                this.loadCurrent();
            }

            bindEvents() {
                this.playPauseBtn.addEventListener('click', () => this.togglePlay());
                this.nextBtn.addEventListener('click', () => this.next());
                this.prevBtn.addEventListener('click', () => this.prev());

                if (this.playerOverlay) {
                    this.playerOverlay.addEventListener('click', (e) => {
                        // Toggle play/pause when clicking the overlay area
                        if (e.target === this.playerOverlay || e.target.closest('#overlay-icon')
                            ?.parentElement) {
                            this.togglePlay();
                        }
                    });
                }

                if (this.prevOverlayBtn) {
                    this.prevOverlayBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.prev();
                    });
                }

                if (this.nextOverlayBtn) {
                    this.nextOverlayBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.next();
                    });
                }

                // Seek
                this.seekSlider.addEventListener('input', (e) => {
                    const percent = e.target.value;
                    const duration = this.getDuration();
                    if (duration > 0) {
                        const time = (percent / 100) * duration;
                        this.seekTo(time);
                        this.seekProgress.style.width = percent + '%';
                    }
                });

                // Volume
                this.volumeSlider.addEventListener('input', (e) => {
                    const val = e.target.value;
                    this.setVolume(val);
                    this.volumeProgress.style.width = val + '%';
                    this.updateVolumeIcon(val);
                });

                this.muteBtn.addEventListener('click', () => {
                    if (this.getVolume() > 0) {
                        this.lastVolume = this.getVolume();
                        this.setVolume(0);
                        this.volumeSlider.value = 0;
                        this.volumeProgress.style.width = '0%';
                        this.updateVolumeIcon(0);
                    } else {
                        const val = this.lastVolume || 100;
                        this.setVolume(val);
                        this.volumeSlider.value = val;
                        this.volumeProgress.style.width = val + '%';
                        this.updateVolumeIcon(val);
                    }
                });

                this.fullscreenBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggleFullscreen();
                });
            }

            toggleFullscreen() {
                if (!document.fullscreenElement) {
                    this.playerContainer.requestFullscreen().catch(err => {
                        console.error(`Error attempting to enable full-screen mode: ${err.message}`);
                    });
                } else {
                    document.exitFullscreen();
                }
            }

            getDuration() {
                if (this.ytPlayer && this.ytPlayer.getDuration) return this.ytPlayer.getDuration();
                if (this.currentPlayer) return this.currentPlayer.duration;
                return 0;
            }

            seekTo(time) {
                if (this.ytPlayer && this.ytPlayer.seekTo) this.ytPlayer.seekTo(time, true);
                else if (this.currentPlayer) this.currentPlayer.currentTime = time;
            }

            setVolume(val) {
                if (this.ytPlayer && this.ytPlayer.setVolume) this.ytPlayer.setVolume(val);
                else if (this.currentPlayer) this.currentPlayer.volume = val / 100;
            }

            getVolume() {
                if (this.ytPlayer && this.ytPlayer.getVolume) return this.ytPlayer.getVolume();
                if (this.currentPlayer) return this.currentPlayer.volume * 100;
                return 100;
            }

            updateVolumeIcon(val) {
                const icon = this.muteBtn.querySelector('.material-symbols-outlined');
                if (val == 0) icon.textContent = 'volume_off';
                else if (val < 50) icon.textContent = 'volume_down';
                else icon.textContent = 'volume_up';
            }

            renderQueue() {
                this.queueList.innerHTML = '';
                this.queue.forEach((item, i) => {
                    const div = document.createElement('div');
                    div.className =
                        `queue-item group flex items-center gap-4 p-3 rounded-xl border border-transparent hover:bg-white/5 hover:border-white/5 transition-all cursor-pointer relative overflow-hidden`;

                    div.innerHTML =
                        `
                                                                                                                                                                        <div class="w-6 text-center text-white/20 font-bold text-sm group-hover:text-primary">${(i + 1).toString().padStart(2, '0')}</div>
                                                                                                                                                                        <div class="relative w-20 h-14 rounded-lg overflow-hidden shrink-0 bg-surface-dark border border-white/5">
                                                                                                                                                                            <img src="${item.thumbnail || '/resources/images/song_cover.png'}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                                                                                                                                                                            <div class="active-badge hidden absolute inset-0 bg-primary/20 backdrop-blur-sm flex items-center justify-center">
                                                                                                                                                                                <span class="material-symbols-outlined text-white text-[18px] animate-pulse">equalizer</span>
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="flex-1 min-w-0">
                                                                                                                                                                            <h4 class="font-bold text-white/90 truncate text-base group-hover:text-white transition-colors">${item.title || 'Unknown Theme'}</h4>
                                                                                                                                                                            <p class="text-xs text-white/40 truncate">${item.artists || 'Various Artists'} • ${item.anime || 'Various Anime'} • ${item.song_type || 'Theme'}</p>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="flex flex-col items-end shrink-0">
                                                                                                                                                                            <div class="flex items-center gap-1 text-yellow-400/60 font-bold text-sm">
                                                                                                                                                                                <span class="material-symbols-outlined text-[14px] filled">star</span>
                                                                                                                                                                                ${item.average_rating || 'N/A'}
                                                                                                                                                                            </div>
                                                                                                                                                                            <span class="text-[10px] text-white/30 font-medium">${this.formatTime(item.duration || 0)}</span>
                                                                                                                                                                        </div>
                                                                                                                                                                    `;

                    div.addEventListener('click', () => this.playIndex(i));
                    this.queueList.appendChild(div);
                });
            }

            formatTime(s) {
                if (!s) return '0:00';
                const m = Math.floor(s / 60);
                const rs = Math.floor(s % 60);
                return `${m}:${rs.toString().padStart(2, '0')}`;
            }

            startYouTubeTimeTracking() {
                this.stopYouTubeTimeTracking();
                this.youtubeTimeInterval = setInterval(() => {
                    if (this.ytPlayer && this.ytPlayer.getCurrentTime) {
                        const current = this.ytPlayer.getCurrentTime();
                        const duration = this.ytPlayer.getDuration();
                        this.timeEl.textContent = this.formatTime(current);

                        if (duration > 0) {
                            const percent = (current / duration) * 100;
                            this.seekProgress.style.width = percent + '%';
                            this.seekSlider.value = percent;
                        }
                    }
                }, 1000);
            }

            stopYouTubeTimeTracking() {
                if (this.youtubeTimeInterval) {
                    clearInterval(this.youtubeTimeInterval);
                    this.youtubeTimeInterval = null;
                }
            }
            showEmpty() {
                this.playerContainer.innerHTML =
                    '<div class="opacity-40 flex flex-col items-center gap-4"><span class="material-symbols-outlined text-6xl">videocam_off</span><p class="font-bold">No themes in this playlist</p></div>';
            }
            showError(m) {
                this.playerContainer.innerHTML = `<div class="text-red-500 font-bold p-10 text-center">${m}</div>`;
            }
        }

        document.addEventListener('DOMContentLoaded', () => new PlaylistPlayer());
    </script>
@endsection

