@extends('layouts.app')

@section('content')

    <style>
        .region-card {
            background: linear-gradient(135deg, #eef3ff, #ffffff);
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 25px 15px;
            text-align: center;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #d9e3ff;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .region-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.18);
            background: linear-gradient(135deg, #e0eaff, #ffffff);
        }

        .region-icon {
            font-size: 35px;
            margin-bottom: 10px;
            color: #467fcf;
        }

        /* Ripple effect */
        .region-card:active::after {
            content: "";
            position: absolute;
            width: 150px;
            height: 150px;
            background: rgba(70, 127, 207, 0.2);
            border-radius: 50%;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            animation: ripple 0.5s linear;
        }

        @keyframes ripple {
            from { opacity: 1; transform: translate(-50%, -50%) scale(0.2); }
            to { opacity: 0; transform: translate(-50%, -50%) scale(1.5); }
        }
    </style>

    <div class="section">

        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-life-buoy mr-1"></i>&nbsp; {{ __("Hududlar ro'yxati") }}
                </li>
            </ol>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card p-4">

                    <div class="row">
                        @foreach($regions as $region)
                            <div class="col-md-3 col-sm-6 mb-4">
                                <div class="region-card"
                                     onclick="window.location.href='{{ route('works.child-list', $region->id) }}'">

                                    <!-- Icon (you can customize per region) -->
                                    <div class="region-icon">
                                        <i class="fe fe-map-pin"></i>
                                    </div>

                                    {{ $region->name }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>

    </div>

@endsection