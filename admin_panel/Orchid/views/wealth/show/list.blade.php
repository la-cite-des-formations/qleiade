@isset($wealths)
    <div class="bg-white rounded-top shadow-sm mb-3">
        <div class="row g-0">
            <div class="col col-lg-7 mt-6 p-4">

                <h2 class="mt-2 text-dark fw-light">
                    {{ __('result_set_title :needs', ['needs' => $needs]) }}
                </h2>
                <p>
                    {{ __('result_set_subtitle') }}
                </p>
            </div>
            <div class="d-none d-lg-block col align-self-center text-end text-muted p-4">
                <x-orchid-icon path="rocket" width="4em" height="100%" />
            </div>
        </div>

        @foreach ($wealths as $wealth)
            @include('wealth.show.wealth', ['wealth' => $wealth])
        @endforeach

    </div>
    @endif
