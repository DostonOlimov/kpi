@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="container mt-2">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left mb-2">
                        <h2>Yangi Kiritish</h2>
                    </div>
                </div>
            </div>

            @if(session('status'))
                <div class="alert alert-success mb-1 mt-1">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('days.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="form-group">
                            <strong>Joriy oyda haqiqatdan ishlagan kunlari:</strong>
                            <input type="hidden" name="user_id" value="{{ $id }}">
                            <input type="hidden" name="month_id" value="{{ $month_id }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="number" name="days" class="form-control" min="0" placeholder="Ishlangan kunlar soni">
                            @error('days')
                            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Saqlash</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endsection
