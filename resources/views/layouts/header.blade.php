@php
    $currentRoute = request()->route()?->getName();
    $currentPath = request()->path();
    $isBankRoute = str_starts_with((string) $currentRoute, 'bank.') || str_starts_with((string) $currentPath, 'bank/');
@endphp

@if($isBankRoute)
    @include('layouts.bank-header')
@else
    @include('layouts.slecic-header')
@endif
