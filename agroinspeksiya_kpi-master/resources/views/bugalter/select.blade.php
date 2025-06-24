@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="row ">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card-body" style="background-color:white">
                    <!-- <div class="col-lg-12 grid-margin stretch-card">
                     <div class="card"> -->
                    <!-- <div class="card-body"> -->
                    <h2>Oy nomini tanlang</h2>


                    @if(session('status'))
                        <div class="alert alert-success mb-1 mt-1">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form action="{{ route('days.store2') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Oy nomi:</strong>
                                    <select name="month_id" class="form-control"  >
                                        @foreach($months as $key => $month)
                                            <option value="{{$key}}">{{$month}}</option>
                                        @endforeach
                                    </select>
                                    @error('month_id')
                                    <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary ml-3">Yuborish</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
