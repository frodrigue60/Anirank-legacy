<div class="modal fade" tabindex="-1" id="report-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  ">
            @if (Auth::check())
                <form action="{{ route('reports.store') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Report {{ $anime->title }}
                            {{ $song->slug }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="song_id" value="{{ $song->id }}">
                        <input type="hidden" name="user_id" value="{{ Auth::User()->id }}">
                        <div class="mb-3">
                            <label for="title-input" class="form-label">Report title</label>
                            <input type="text" class="form-control" id="title-input" placeholder="Title report..."
                                name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="content-textarea" class="form-label">Report content</label>
                            <textarea class="form-control" id="content-textarea" rows="3" placeholder="Report content..." name="content"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send report</button>
                    </div>
                </form>
            @else
                <div class="d-flex justify-content-center comment-form ">
                    <h3>Please <a class="" href="{{ route('login') }}">login</a> or <a class=""
                            href="{{ route('register') }}">register</a> for report</h3>
                </div>
            @endif
        </div>
    </div>
</div>
