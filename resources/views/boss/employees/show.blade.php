@extends('layouts.app')

@section('content')
<div class="container">
    <h3>{{ $user->first_name }} {{ $user->last_name }} ish natijalari</h3>
    {{-- Masalan, KPIlar ro‘yxati --}}
    <ul>
        @foreach($kpis as $kpi)
            <li>{{ $kpi->name }}: {{ $kpi->current_score }}</li>
        @endforeach
    </ul>
</div>
@endsection