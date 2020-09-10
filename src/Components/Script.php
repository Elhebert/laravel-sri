
<?php

namespace Elhebert\SubresourceIntegrity\Components;

use Illuminate\View\Component;

class Script extends Component
{
    private string $src;

    public function __construct(string $src)
    {
        $this->src = $src;
    }

    public function render(): string
    {
        return <<<'blade'
            <script src="{{ asset($this->src) }} integrity="{{ Sri::hash($this->src) }}" {{ $this->attributes }} />
        blade;
    }
}
