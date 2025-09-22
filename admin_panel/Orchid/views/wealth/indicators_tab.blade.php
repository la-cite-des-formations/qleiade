<div class="p-4">
    <h5>Indicateurs pour le label : {{ $label->label }}</h5>

    <ul>
        @foreach($label->criterias as $criterion)
            @foreach($criterion->indicators as $indicator)
                <li>
                    {{ $indicator->number }} – {{ $indicator->label }}
                </li>
            @endforeach
        @endforeach
    </ul>
</div>
