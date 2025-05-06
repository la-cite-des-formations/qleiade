<div class="bg-white rounded-top shadow-sm mb-3">
    <div class="row g-0">
        <div class="col col-lg-7 mt-6 p-4">

            <h2 class="mt-2 text-dark fw-light">
                {{ __('welcome_title') }}
            </h2>
            <p>
                {{ __('welcome_subtitle') }}
            </p>
        </div>
        <div class="d-none d-lg-block col align-self-center text-end text-muted p-4">
            {{-- <x-orchid-icon path="orchid" width="6em" height="100%"/> --}}
        </div>
    </div>

    <div class="row bg-light m-0 p-4 border-top rounded-bottom">

      @foreach($administerItems as $item)
       @hasAccess($item->permission)
        <div class="col-md-6 my-2">
            <h3 class="text-muted fw-light">
                <x-orchid-icon path="{{ $item->icon }}" />

                <span class="ms-3 text-dark">
                    <a href="{{ route($item->route) }}">
                        {{ __($item->welcomeLabel) }}
                    </a>
                </span>
            </h3>
            <p class="ms-md-5 ps-md-1">

            </p>
        </div>
       @endhasAccess
      @endforeach()

</div>
