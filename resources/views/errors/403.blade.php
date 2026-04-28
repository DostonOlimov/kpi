@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 60vh;">
        <div class="text-center">
            <h1 style="font-size: 6rem; font-weight: 700; color: #e55353;">403</h1>
            <h2 class="mb-3">Kirish taqiqlangan</h2>
            <p class="text-muted mb-4">
                {{ $exception->getMessage() ?: "Sizda bu sahifaga kirish huquqi yo'q!" }}
            </p>
            <a href="{{ url('/home') }}" class="btn btn-primary">
                <svg class="nav-icon me-1" style="width:16px;height:16px;">
                    <use xlink:href="/assets/vendors/@coreui/icons/svg/free.svg#cil-home"></use>
                </svg>
                Bosh sahifaga qaytish
            </a>
        </div>
    </div>
@endsection
