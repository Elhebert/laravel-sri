<?php

namespace Elhebert\SubresourceIntegrity\Components;

use Elhebert\SubresourceIntegrity\SriFacade as Sri;
use Illuminate\View\Component;

class Link extends Component
{
    /** @var string */
    public $href;

    /** @var bool */
    public $mix = false;

    public function __construct(string $href, bool $mix = false)
    {
        $this->href = $href;
        $this->mix = $mix;
    }

    public function integrity(): string
    {
        return Sri::hash($this->href);
    }

    public function path(): string
    {
        return $this->mix ? mix($this->href) : asset($this->href);
    }

    public function render(): string
    {
        return <<<'blade'
            <link href="{{ $path() }}" integrity="{{ $integrity() }}" {{ $attributes }} />
        blade;
    }
}
