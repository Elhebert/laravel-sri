@if ($crossOrigin)
<x-sri.script src="/js/app.js" mix="{{ $mix }}" crossorigin="{{ $crossOrigin }}" />
@else
<x-sri.script src="/js/app.js" mix="{{ $mix }}" />
@endif
