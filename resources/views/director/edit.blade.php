@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-settings mr-1"></i>&nbsp; {{ __("KPI me'zonlarini o'zgartirish") }}
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">

                    <div class="card-header">
                        <h4 class="mb-0">{{ __("KPI baholash ma'lumotlari") }}</h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ url('/update/'.$id) }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="name"><strong>{{ __('Nomi') }}</strong></label>
                                <textarea name="name" class="form-control" id="name" rows="4" required></textarea>
                            </div>

                            <div class="form-group">
                                <label for="type"><strong>{{ __('Turi') }}</strong></label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="1">Ижро интизоми йўналиши</option>
                                    <option value="2">Асосий фаолият йўналишлари</option>
                                    <option value="3">Қонунчилик фаолият йўналиши</option>
                                    <option value="4">Қонунчилик тарғиботи йўналиши</option>
                                    <option value="5">Давлат хизматларини кўрсатиш йўналиши</option>
                                    <option value="6">Самарадорлик ва натижадорлик кўрсаткичларидан чегирмалар</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="ball"><strong>{{ __('Baholash (har bir ish uchun ball)') }}</strong></label>
                                    <input name="ball" type="number" step="0.01" class="form-control" id="ball" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="max_ball"><strong>{{ __("Yuqori ball miqdori") }}</strong></label>
                                    <input name="max_ball" type="number" step="0.01" class="form-control" id="max_ball" required>
                                </div>
                            </div>

                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-success">
                                    {{ __('Saqlash') }}
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
