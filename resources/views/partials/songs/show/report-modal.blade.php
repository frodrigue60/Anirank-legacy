<div class="modal fade" tabindex="-1" id="modal-report">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  ">
            @guest
                <div class="modal-body">
                    <div class="d-flex justify-content-center">
                        <h3>Please <a class="" href="{{ route('login') }}">login</a> or <a class=""
                                href="{{ route('register') }}">register</a> for report</h3>
                    </div>
                </div>
            @endguest

            @auth
                <form action="{{ route('reports.store') }}" method="post" id="form-report" data-songid="{{ $song->id }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Report {{ $anime->title }}
                            {{ $song->slug }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="input-title" class="form-label">Report title</label>
                            <input type="text" class="form-control" id="input-title" placeholder="Title report..."
                                name="title" required>
                        </div>
                        <div class="">
                            <label for="textarea-content" class="form-label">Report content</label>
                            <textarea class="form-control" id="textarea-content" rows="3" placeholder="Report content..." name="content"
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn-submit-report">Send report</button>
                    </div>
                </form>
            @endauth
        </div>
    </div>
</div>
