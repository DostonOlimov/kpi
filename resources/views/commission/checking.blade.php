@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/kpi/commission/checking.css') }}">
@section('content')
    <div class="section">
        <!-- PAGE-HEADER -->
        <div class="page-header mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fa fa-user-circle mr-1"></i>&nbsp; Xodimning shaxsiy ko'rsatkichlari
                </li>
            </ol>
        </div>
        <!-- User Information Section -->
        <div class="user-info-section">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-title">
                            <i class="fa fa-user-circle"></i>
                            Xodimning ma'lumotlar
                        </div>
                        <div class="info-item">
                            <span class="info-label">Ismi-sharifi</span>
                            <span class="info-value">{{ $user->first_name .' '. $user->last_name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Lavozimi</span>
                            <span class="info-value">{{ $user->lavozimi }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Bo'limi</span>
                            <span class="info-value">{{ $user->work_zone->name }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-title">
                            <i class="fa fa-calendar-alt"></i>
                           Baho bo'yicha ma'lumotlar
                        </div>
                        <div class="info-item">
                            <span class="info-label">Sana</span>
                            <span class="info-value">{{ date('F j, Y') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Baholanuvchi oy</span>
                            <span class="info-value">{{ $month_name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Baholovchi</span>
                            <span class="info-value">{{ auth()->user()->name ?? 'Komissiya azosi' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="evaluation-card fade-in">
                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alert-container"></div>

                        <!-- Evaluation Form -->
                        <form id="kpi-scoring-form"  action="{{ route('commission.check_user_store',['type'=>\App\Models\Kpi::BEHAVIOUR,'user'=>$user]) }}" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            @foreach($user_kpis as $user_kpi)
                                <div class="kpi-card fade-in active">
                                    <div class="kpi-header" onclick="toggleKpi(this)">
                                        <div class="kpi-title"> <i class="fa fa-tasks me-2"></i>{{ $user_kpi->kpi->name }}</div>
                                        <div class="kpi-toggle">
                                            <i class="fa fa-chevron-down"></i>
                                        </div>
                                    </div>
                                    <div class="kpi-content">
                                        @foreach($user_kpi->kpi->criterias as $index => $criteria)
                                            <div class="criteria-card" style="animation-delay: {{ $index * 0.1 }}s" data-criteria="{{ $index }}">
                                                <div class="criteria-header">
                                                    <div class="criteria-number">{{ $index + 1 }}</div>
                                                    <div class="criteria-title">{{ $criteria->name }}</div>
                                                    <div class="criteria-description">{{ $criteria->description }}</div>
                                                </div>
                                                <div class="score-options">
                                                    @foreach($criteria->bands as $band)
                                                        <div class="score-option" data-score="{{ $band->fine_ball }}">
                                                            <input type="radio" name="scores[{{ $criteria->id }}]" value="{{ $band->fine_ball }}" id="score_{{ $criteria->id }}_{{ $band->fine_ball }}" {{ $band->fine_ball == 0 ? 'checked' : '' }}>
                                                            <label class="score-label" for="score_{{ $criteria->id }}_{{ $band->fine_ball }}">
                                                                <div class="score-icon">
                                                                    @if($band->fine_ball == 0)
                                                                        <i class="fa fa-check"></i>
                                                                    @elseif($band->fine_ball == 1)
                                                                        <i class="fa fa-minus"></i>
                                                                    @else
                                                                        <i class="fa fa-times"></i>
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    <div style="font-weight: 600;">{{ $band->name }}</div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                            <!-- Comments Section -->
                            <div class="comments-section fade-in">
                                <div class="comments-header">
                                    <div class="comments-icon">
                                        <i class="fa fa-comment"></i>
                                    </div>
                                    <div class="comments-title">Qo'shimcha izohlar</div>
                                </div>
                                <textarea class="form-control" name="feedback" rows="5" placeholder="Iltimos, xodimning ish faoliyati, kuchli tomonlari, takomillashtirish yo‘nalishlari va tavsiyalar haqida batafsil fikr-mulohaza bildiring..."></textarea>
                            </div>

                            <!-- Submit Section -->
                            <div class="submit-section">
                                <button type="submit" class="btn btn-submit">
                                        <span class="loading-spinner">
                                            <i class="fa fa-spinner fa-spin me-2"></i>
                                        </span>
                                    <span class="btn-text text-white">
                                            <i class="fa fa-paper-plane me-2"></i>Saqlash
                                        </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Make the entire score-option div clickable
            document.querySelectorAll('.score-option').forEach(function(scoreOption) {
                scoreOption.addEventListener('click', function() {
                    const radioButton = this.querySelector('input[type="radio"]');
                    if (radioButton) {
                        radioButton.checked = true;
                        // Remove any existing fine-ball classes
                        scoreOption.classList.remove('fine-ball-0', 'fine-ball-1', 'fine-ball-2');
                        // Add the appropriate fine-ball class based on the value
                        if (radioButton.value == 0) {
                            scoreOption.classList.add('fine-ball-0');
                        } else if (radioButton.value == 1) {
                            scoreOption.classList.add('fine-ball-1');
                        } else {
                            scoreOption.classList.add('fine-ball-2');
                        }
                        // Update the progress bar
                        updateProgress();
                    }
                });
            });

            // Update progress bar
            function updateProgress() {
                const totalCriteria = document.querySelectorAll('.criteria-card').length;
                const completedCriteria = document.querySelectorAll('.score-option input:checked').length;
                const progress = (completedCriteria / totalCriteria) * 100;
                document.getElementById('progress-bar').style.width = progress + '%';
                document.getElementById('progress-text').textContent = Math.round(progress);
            }

            // Initial progress update
            updateProgress();
        });

        function toggleKpi(element) {
            const kpiCard = element.closest('.kpi-card');
            kpiCard.classList.toggle('active');
        }
    </script>
@endsection
