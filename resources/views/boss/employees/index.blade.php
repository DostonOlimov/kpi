@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Mening xodimlarim</h3>
    <ul>
        @foreach($employees as $item)
            <li>
                <a href="{{ route('boss.employee.show', $item->user->id) }}">
                    {{ $item->user->first_name }} {{ $item->user->last_name }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
@endsection