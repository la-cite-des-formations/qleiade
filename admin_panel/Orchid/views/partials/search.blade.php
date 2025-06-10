<div class="bg-white rounded d-flex flex-column" data-async>
    <label class="col-sm-1 mt-2 form-label">
        {{ $title }}
    </label>
    <div class="input-group rounded" style="max-width: 18em;">
            <input {{ $attributes}}>
        <div class="input-group-text border-0" id="myfuckingbutton">
            <x-orchid-icon path="magnifier" />
        </div>
    </div>
</div>
