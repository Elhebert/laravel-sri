@if ($crossOrigin)
<x-sri.link href="css/app.css" rel="stylesheet" mix="{{ $mix }}" crossorigin="{{ $crossOrigin }}" />
@else
<x-sri.link href="css/app.css" rel="stylesheet" mix="{{ $mix }}" />
@endif
