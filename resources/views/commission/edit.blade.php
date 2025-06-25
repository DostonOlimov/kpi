@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-check-square mr-1"></i>&nbsp;Xodim baholarini tekshirish bo'limi
                </li>
            </ol>
        </div>

        @if (session()->has('fail'))
            <div class="alert alert-danger">{{ session()->get('fail') }}</div>
        @endif
        @if (session()->has('success'))
            <div class="alert alert-success">{{ session()->get('success') }}</div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h4>Qilingan ishlarni tekshirish formasi</h4>
                            <h4><span class="text-primary">{{ $user->first_name . ' ' . $user->last_name }}</span></h4>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered mt-3">
                                <thead style="background-color:bisque;">
                                <tr>
                                    <th>№</th>
                                    <th>Ko'rsatkich nomi</th>
                                    <th>Maqsadli</th>
                                    <th>Amaldagi</th>
                                    <th>Hujjat</th>
                                    <th>Ball</th>
                                    <th>Chegirma</th>
                                    <th>Izoh</th>
                                    <th>Fayl</th>
                                    <th>Amal</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $key => $item)
                                    <tr>
                                        <td>{{ $item->razdel.'.'.$item->band_id }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->works_count }}</td>
                                        <td>{{ $item->current_works }}</td>
                                        <td>
                                            @if ($item->file_path)
                                                <a class="btn btn-primary btn-sm" href="{{ route('profile.download',$item->id) }}">Yuklash</a>
                                            @endif
                                        </td>
                                        <td>{{ $current_ball = \App\Models\KpiEmployees::find($item->id)->CalculateBall() }}</td>

                                        @php
                                            $fine = \App\Models\FineBall::where('user_id', $item->user_id)->where('kpi_id', $item->id)->first();
                                        @endphp

                                        @if($current_ball != 0)
                                            @if (!$fine)
                                                @if(auth()->user()->role_id != 7)
                                                    <form action="{{ route('commission.upload') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $item->id }}">
                                                        <td><input class="form-control" type="number" name="fine_ball"></td>
                                                        <td><textarea class="form-control" name="commit" rows="2"></textarea></td>
                                                        <td><input type="file" name="file" class="form-control"></td>
                                                        <td><button type="submit" class="btn btn-success btn-sm">Saqlash</button></td>
                                                    </form>
                                                @else
                                                    <td colspan="4" class="bg-light"></td>
                                                @endif
                                            @else
                                                <td>{{ $fine->fine_ball }}</td>
                                                <td>{{ $fine->comment }}</td>
                                                <td>
                                                    @if ($fine->order_file)
                                                        <a class="btn btn-primary btn-sm" href="{{ route('commission.download',$fine->id) }}">Yuklash</a>
                                                    @endif
                                                </td>
                                                <td class="bg-light"></td>
                                            @endif
                                        @else
                                            <td colspan="4" class="bg-light"></td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger mt-3">
                                <strong>Diqqat!</strong> Ma'lumotlar to'liq emas yoki xatolik mavjud.
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </div>

                </div>
            </div>
        </div>

        <h2 class="text-center fw-bold my-4">Xodimning oylik ko'rsatkichlari</h2>
        <div style="width: 70%; margin: 0 auto;">
            <canvas id="bar_chart"></canvas>
        </div>

        <x-bar_chart :data1="$balls" />
    </div>

@endsection
