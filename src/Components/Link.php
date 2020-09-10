
<?php

namespace Elhebert\SubresourceIntegrity\Components;

use Illuminate\View\Component;
use Elhebert\SubresourceIntegrity\Sri;

class Link extends Component
{
    private string $href;

    public function __construct(string $href)
    {
        $this->href = $href;
    }

    public function render(): string
    {
        return <<<'blade'
            <link href="{{ asset($this->href) }} integrity="{{ Sri::hash($this->href) }}" {{ $this->attributes }} />
        blade;
    }
}
