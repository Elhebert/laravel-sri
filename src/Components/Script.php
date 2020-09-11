<?php

namespace Elhebert\SubresourceIntegrity\Components;

use Elhebert\SubresourceIntegrity\SriFacade as Sri;
use Illuminate\View\Component;

class Script extends Component
{
    public string $src;
    public bool $mix;

    public function __construct(string $src, bool $mix = false)
    {
        $this->src = $src;
        $this->mix = $mix;
    }

    public function integrity(): string
    {
        return Sri::hash($this->src);
    }

    public function path(): string
    {
        return $this->mix ? mix($this->src) : asset($this->src);
    }

    public function render(): string
    {
        return <<<'blade'
            <script src="{{ $path() }}" integrity="{{ $integrity() }}" {{ $attributes }} />
        blade;
    }
}
