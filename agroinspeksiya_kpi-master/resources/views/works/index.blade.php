@extends('layouts.app')

@section('content')
<div class="content-wrapper">
        <div class="row ">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body" style="background-color:white">
                        <!-- <div class="col-lg-12 grid-margin stretch-card">
                         <div class="card"> -->
                        <!-- <div class="card-body"> -->
                        <h2>Foydalanuvchilarning ish joylari</h2>

                        <div class="pull-right mb-2">
                            <a class="btn btn-success" href="{{ route('works.create') }}">  Yaratish</a>
                        </div>
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <table class="table table-bordered table-responsive">
                            <thead>
                            <tr>
                                <th>Tartib raqam</th>
                                <th>Nomi</th>
                                <th>Yaratilgan vaqti</th>
                                <th>O'zgartirilgan vaqti</th>
                                <th width="280px">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($works as $work)
                                <tr>
                                    <td>{{ $work->id }}</td>
                                    <td>{{ $work->name }}</td>
                                    <td>{{ $work->created_at }}</td>
                                    <td>{{ $work->updated_at }}</td>
                                    <td>
                                        <form action="{{ route('works.destroy',$work->id) }}" method="Post">
                                            <a class="btn btn-primary" href="{{ route('works.edit',$work->id) }}">Tahrirlash</a>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">O'chirish</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!! $works->links() !!}
                    </div>
                </div>

    </div>
</div>
</div>
@endsection
