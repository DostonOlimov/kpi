@extends('layouts.app')

<style>
        .forms-sample input{
            font-size: 16px;
            font-weight: bold;
        }
        .forms-sample select{
            font-size: 16px;
            font-weight: bold;
        }
    </style>
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body bg-white">
                        <h2 class="mb-4">Xodimlarning ish kunlari</h2>

                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                {{ $message }}
                            </div>
                        @endif

                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Ism Sharifi</th>
                                <th>Ish joyi</th>
                                <th>Lavozimi</th>
                                <th>Oy</th>
                                <th>Oylik ish kuni</th>
                                <th>Ishga chiqilgan kun</th>
                                <th>Amal</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $key => $user)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                    <td>{{ $user->work_zone->name ?? '-' }}</td>
                                    <td>{{ $user->lavozimi }}</td>
                                    <td>{{ $date['month_name'] }}</td>
                                    <td>{{ $date['days'] }} kun</td>
                                    <td>
                                        {{ optional($user->employeeDays->first())->days ?? '-' }} kun
                                    </td>
                                    <td>
                                        @if ($user->employeeDays->first())
                                            <a href="{{ route('days.edit', $user->employeeDays->first()->id) }}" class="btn btn-sm btn-primary">
                                                O'zgartirish
                                            </a>
                                        @else
                                            <a href="{{ route('days.createday', [$user->id, $date['month_id'], $date['year']]) }}" class="btn btn-sm btn-success">
                                                Kiritish
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        {{-- Pagination (if applicable) --}}
                        @if(method_exists($users, 'links'))
                            <div class="mt-3">
                                {{ $users->links() }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
