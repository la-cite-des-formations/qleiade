<div class="form-signin container h-full p-0 px-sm-5 py-5 my-sm-5">
    @foreach($available_locales as $locale_name => $available_locale)
    @if($available_locale === $current_locale)
    
    <div class="v-top opacity">{{ $locale_name }}</div>
    @else
    <div class="m-l d-none d-sm-block">
        <a href="language/{{ $available_locale }}">
            <span class="m-l d-none d-sm-block">{{ $locale_name }}</span>
        </a>
    </div>
    @endif
    @endforeach
</div>