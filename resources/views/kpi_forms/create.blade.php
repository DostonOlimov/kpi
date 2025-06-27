@extends('layouts.app')
<style>
    .kpi-table-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .table-header th {
        border: none;
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 1rem;
    }

    .category-row {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        border-left: 4px solid #2196f3;
    }

    .category-row td {
        padding: 1rem;
        font-weight: 600;
        color: #1565c0;
    }

    .item-row {
        transition: all 0.3s ease;
        border-left: 2px solid transparent;
    }

    .item-row:hover {
        background-color: #f8f9fa;
        border-left-color: #28a745;
        transform: translateX(2px);
    }

    .item-number {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .item-name {
        color: #495057;
        font-weight: 500;
    }

    .item-name::before {
        content: "—";
        color: #28a745;
        font-weight: bold;
        margin-right: 8px;
    }

    .score-badge {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .action-buttons {
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .item-row:hover .action-buttons {
        opacity: 1;
    }

    .btn-action {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 2px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .btn-view {
        background-color: #e3f2fd;
        color: #1976d2;
    }

    .btn-view:hover {
        background-color: #1976d2;
        color: white;
        border-color: #1976d2;
    }

    .btn-edit {
        background-color: #e8f5e8;
        color: #388e3c;
    }

    .btn-edit:hover {
        background-color: #388e3c;
        color: white;
        border-color: #388e3c;
    }

    .btn-fill {
        background-color: #fff3e0;
        color: #f57c00;
    }

    .btn-fill:hover {
        background-color: #f57c00;
        color: white;
        border-color: #f57c00;
    }

    .btn-delete {
        background-color: #ffebee;
        color: #d32f2f;
    }

    .btn-delete:hover {
        background-color: #d32f2f;
        color: white;
        border-color: #d32f2f;
    }

    .table-stats {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }

    .stat-item {
        text-align: center;
        padding: 0.5rem;
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: #495057;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    @media (max-width: 768px) {
        .table-responsive {
            border-radius: 8px;
        }

        .action-buttons {
            opacity: 1;
        }

        .btn-action {
            width: 30px;
            height: 30px;
            margin: 0 1px;
        }
    }
</style>

@section('content')
    <div class="section">
        <!-- Page Header -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-bar-chart-2"></i> {{ __('Maqsadli ko\'rsatkichlarni qo\'shish') }}
                </li>
            </ol>
        </div>

        <!-- KPI Table Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body" style="background-color: #f4ebd9">

                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                {{ $message }}
                            </div>
                        @endif

                            <form method="POST" action="#" >
                                @csrf
                                @foreach ($kpis as $category)
                                    <h4 class="mt-4" >{{ $category->name }}</h4>
                                    @foreach ($category->children as $child)
                                        <div class="card mb-3">
                                            <div class="card-header" style="background-color: #96c8f3">
                                                <strong>{{ $child->name }}</strong> (Max: {{ $child->max_score }} ball)
                                            </div>
                                            <div class="card-body">
{{--                                                <div class="mb-2">--}}
{{--                                                    <label>--}}
{{--                                                        <input type="radio" name="task_mode[{{ $child->id }}]" value="single" checked>--}}
{{--                                                        Yagona topshiriq--}}
{{--                                                    </label>--}}
{{--                                                    <label class="ms-3">--}}
{{--                                                        <input type="radio" name="task_mode[{{ $child->id }}]" value="multiple">--}}
{{--                                                        Topshiriq qo'shish--}}
{{--                                                    </label>--}}
{{--                                                </div>--}}

                                                <!-- Single Task Input -->
                                                <div class="single-task" id="single-task-{{ $child->id }}">
                                                    <input type="text" readonly name="single{{ $child->id }}" class="form-control mb-2" value="{{ $child->name }}">
                                                    <input type="number" name="single{{ $child->id }}" class="form-control" max="{{ $child->max_score }}" value="{{ $child->max_score }}">
                                                </div>

                                                <!-- Multiple Task Input -->
{{--                                                <div class="multiple-tasks d-none" id="multiple-tasks-{{ $child->id }}">--}}
{{--                                                    <div class="task-group" data-kpi="{{ $child->id }}">--}}
{{--                                                        <div class="task mb-2">--}}
{{--                                                            <input type="text" name="multiple[{{ $child->id }}][0][title]" class="form-control mb-1" placeholder="Topshiriq nomi">--}}
{{--                                                            <input type="number" name="multiple[{{ $child->id }}][0][score]" class="form-control" placeholder="Vazni">--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <button type="button" class="btn btn-sm btn-primary add-task" data-kpi="{{ $child->id }}">+ Yangi topshiriq</button>--}}
{{--                                                </div>--}}
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach

                                <button type="submit" class="btn btn-success">Saqlash</button>
                            </form>

                    </div> <!-- card-body -->
                </div> <!-- card -->
            </div> <!-- col-md-12 -->
        </div> <!-- row -->
    </div> <!-- section -->
    <script>
        document.querySelectorAll('input[type=radio]').forEach(radio => {
            radio.addEventListener('change', function () {
                const kpiId = this.name.match(/\d+/)[0];
                const isMultiple = this.value === 'multiple';

                document.getElementById(`single-task-${kpiId}`).classList.toggle('d-none', isMultiple);
                document.getElementById(`multiple-tasks-${kpiId}`).classList.toggle('d-none', !isMultiple);
            });
        });

        document.querySelectorAll('.add-task').forEach(button => {
            button.addEventListener('click', function () {
                const kpiId = this.dataset.kpi;
                const group = document.querySelector(`.task-group[data-kpi="${kpiId}"]`);
                const index = group.children.length;

                const newTask = document.createElement('div');
                newTask.classList.add('task', 'mb-2');
                newTask.innerHTML = `
                <input type="text" name="multiple[${kpiId}][${index}][title]" class="form-control mb-1" placeholder="Topshiriq nomi">
                <input type="number" name="multiple[${kpiId}][${index}][score]" class="form-control" placeholder="Vazni">
            `;

                group.appendChild(newTask);
            });
        });
    </script>
@endsection

