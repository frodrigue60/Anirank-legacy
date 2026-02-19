<div x-data="{ open: @entangle('showModal') }" x-show="open" x-cloak
    class="fixed inset-0 z-100 overflow-y-auto overflow-x-hidden p-4 md:p-8" role="dialog" aria-modal="true"
    @open-request-modal.window="open = true">
    {{-- Backdrop --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="open = false">
    </div>

    {{-- Modal Content --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative mx-auto mt-20 max-w-lg w-full transform transition-all">
        <div class="glass-panel bg-surface-darker/90 rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between bg-[#181825]">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">campaign</span>
                    <h3 class="text-xl font-bold text-white tracking-tight">Send Request</h3>
                </div>
                <button type="button" @click="open = false" class="text-white/40 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-6">
                <form wire:submit="submitRequest" class="space-y-6">
                    <div>
                        <label for="category" class="block text-sm font-medium text-white/60 mb-2">Request
                            Category</label>
                        <div class="relative">
                            <select wire:model.live="category" id="category"
                                class="w-full bg-surface-dark border border-white/10 rounded-xl px-4 py-3 text-white focus:border-primary focus:ring-1 focus:ring-primary appearance-none transition-all outline-none">
                                <option value="" disabled selected>Select a category...</option>
                                <option value="1">Add Anime/Song</option>
                                <option value="2">Report Bug/Issue</option>
                                <option value="3">Suggestion/Feedback</option>
                            </select>
                            <span
                                class="material-symbols-outlined absolute right-4 top-3.5 text-white/20 pointer-events-none">expand_more</span>
                        </div>
                        @error('category')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="details" class="block text-sm font-medium text-white/60 mb-2">Details</label>
                        <textarea wire:model="content" id="details" rows="4" required
                            class="w-full bg-surface-dark border border-white/10 rounded-xl px-4 py-3 text-white focus:border-primary focus:ring-1 focus:ring-primary transition-all outline-none resize-none placeholder:text-white/20"
                            placeholder="Describe your request in detail..."></textarea>
                        @error('content')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" @click="open = false"
                            class="flex-1 px-4 py-3 rounded-xl border border-white/10 text-white/60 font-medium hover:bg-white/5 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="flex-1 px-4 py-3 rounded-xl bg-primary text-white font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="submitRequest">Send Request</span>
                            <span wire:loading wire:target="submitRequest">Sending...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
