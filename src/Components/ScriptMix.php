
<?php

namespace Elhebert\SubresourceIntegrity\Components;

use Illuminate\View\Component;

class ScriptMix extends Component
{
    private string $src;

    public function __construct(string $src)
    {
        $this->src = $src;
    }

    public function render(): string
    {
        return <<<'blade'
            <script src="{{ mix($this->src) }} integrity="{{ Sri::hash($this->src }}" {{ $this->attributes }} />
        blade;
    }
}
