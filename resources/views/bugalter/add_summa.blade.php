@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="container mt-2">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left mb-2">
                        <h2>Qoldiq summani kiritish</h2>
                    </div>
                </div>
            </div>
            @if(session('status'))
                <div class="alert alert-success mb-1 mt-1">
                    {{ session('status') }}
                </div>
            @endif
            <form action="{{ route('bugalter.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <strong>Pul miqdori:</strong>
                            <input style="font-size: 16px; font-weight: bold;"
                                   type="number" name="summa" maxlength="12" max="9999999999999"
                                   class="form-control" placeholder="">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <strong>Hisobot oyi:</strong>
                            <select style="font-size: 16px; font-weight: bold;" name="month" class="form-control">
                                @foreach($month as $key => $item)
                                    <option value="{{$key + 1}}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary ml-3">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
@endsection
