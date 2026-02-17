<div id="reportModal" class="fixed inset-0 z-60 hidden overflow-y-auto overflow-x-hidden p-4 md:p-8" role="dialog"
    aria-modal="true">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="window.hideModal('reportModal')">
    </div>

    {{-- Modal Content --}}
    <div class="relative mx-auto mt-20 max-w-lg w-full transform transition-all">
        <div class="glass-panel bg-surface-darker/90 rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-red-500">report</span>
                    <h3 class="text-xl font-bold text-white tracking-tight">Report Theme Issue</h3>
                </div>
                <button type="button" onclick="window.hideModal('reportModal')"
                    class="text-white/40 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-6">
                <form method="post" action="{{ route('reports.store') }}" id="form-report" class="space-y-6">
                    @csrf
                    <input type="hidden" name="song_id" id="report_song_id">

                    <div>
                        <label for="report_title" class="block text-sm font-medium text-white/60 mb-2">What's
                            wrong?</label>
                        <div class="relative">
                            <select name="title" id="report_title" required
                                class="w-full bg-surface-dark border border-white/10 rounded-xl px-4 py-3 text-white focus:border-primary focus:ring-1 focus:ring-primary appearance-none transition-all outline-none">
                                <option value="" disabled selected>Select an issue...</option>
                                <option value="Broken Link / Video Not working">Broken Link / Video Not working</option>
                                <option value="Wrong Audio / Song">Wrong Audio / Song</option>
                                <option value="Missing / Wrong Artist Credits">Missing / Wrong Artist Credits</option>
                                <option value="Wrong Lyrics / Sync">Wrong Lyrics / Sync</option>
                                <option value="Other / General Issue">Other / General Issue</option>
                            </select>
                            <span
                                class="material-symbols-outlined absolute right-4 top-3.5 text-white/20 pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <div>
                        <label for="report_content" class="block text-sm font-medium text-white/60 mb-2">Additional
                            Details (Optional)</label>
                        <textarea name="content" id="report_content" rows="4"
                            class="w-full bg-surface-dark border border-white/10 rounded-xl px-4 py-3 text-white focus:border-primary focus:ring-1 focus:ring-primary transition-all outline-none resize-none placeholder:text-white/20"
                            placeholder="Provide any extra context to help us fix it faster..."></textarea>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" onclick="window.hideModal('reportModal')"
                            class="flex-1 px-4 py-3 rounded-xl border border-white/10 text-white/60 font-medium hover:bg-white/5 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-3 rounded-xl bg-red-600 text-white font-bold shadow-lg shadow-red-900/20 hover:bg-red-500 hover:scale-[1.02] active:scale-[0.98] transition-all">
                            Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    window.openReportModal = function(songId) {
        const songInput = document.getElementById('report_song_id');
        if (songInput) {
            songInput.value = songId;
            showModal('reportModal');
        }
    }
</script>
