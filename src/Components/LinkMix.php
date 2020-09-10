
<?php

namespace Elhebert\SubresourceIntegrity\Components;

use Illuminate\View\Component;
use Elhebert\SubresourceIntegrity\SriFacade as Sri;

class LinkMix extends Component
{
    private string $href;

    public function __construct(string $href)
    {
        $this->href = $href;
    }

    public function render(): string
    {
        return <<<'blade'
            <link href="{{ mix($href) }}  integrity="{{ Sri::hash($this->href) }}" {{ $this->attributes }} />
        blade;
    }
}
