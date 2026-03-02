<?php

namespace App\Filament\Components;

use Filament\Schemas\Components\Tabs\Tab as BaseTab;
use Filament\Support\Concerns\CanBeContained;

class Tab extends BaseTab
{
    use CanBeContained;

    protected string | \Closure | null $livewireProperty = null;

    public function livewireProperty(string | \Closure | null $property): static
    {
        $this->livewireProperty = $property;

        return $this;
    }

    public function getLivewireProperty(): ?string
    {
        return $this->evaluate($this->livewireProperty);
    }
}
