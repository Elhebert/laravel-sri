<?php

namespace Elhebert\SubresourceIntegrity\Components;

use Elhebert\SubresourceIntegrity\SriFacade as Sri;
use Illuminate\View\Component;

class Script extends Component
{
    /** @var string */
    public $src;

    /** @var bool */
    public $mix;

    /** @var string */
    public $crossorigin = 'anonymous';

    public function __construct(string $src, bool $mix = false, string $crossorigin = 'anonymous')
    {
        $this->src = $src;
        $this->mix = $mix;
        $this->crossorigin = $crossorigin;
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
        @once
        <script src="{{ $path() }}" integrity="{{ $integrity() }}" crossorigin="{{ $crossorigin }}" {{ $attributes }} />
        @endonce
        blade;
    }
}
