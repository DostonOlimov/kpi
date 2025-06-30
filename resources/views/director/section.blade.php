@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/kpi/section.css') }}">

@section('content')
    <div class="department-container">
        <div class="container-fluid">
            <!-- Department Header -->
            <div class="department-header fade-in">
                <h1 class="department-title">{{ $department->name }} Boshqaruv paneli</h1>
                <p class="department-subtitle">Bo‘limingizning KPI ko‘rsatkichlari va jamoa faoliyatini kuzatib boring</p>
                <div class="department-info">
                    <div class="info-item">
                        <i class="fa fa-users"></i> {{ $department->users->count() }} Jamoa a'zolari
                    </div>
                    <div class="info-item">
                        <i class="fa fa-chart-bar"></i> {{ $departmentStats['total_kpis'] }} Faol topshiriqlar
                    </div>
                    <div class="info-item">
                        <i class="fa fa-trophy"></i> Umumiy o‘rin: #{{ $departmentStats['department_rank'] }}
                    </div>
                </div>
            </div>

            <!-- Department Statistics -->
            <div class="dept-stats-grid fade-in">
                <div class="dept-stat-card users">
                    <span class="dept-stat-icon">👥</span>
                    <div class="dept-stat-number">{{ $departmentStats['active_users'] }}</div>
                    <div class="dept-stat-label">Faol foydalanuvchilar</div>
                    <div class="dept-stat-description">Vazifalarni topshirganlar</div>
                </div>

                <div class="dept-stat-card tasks">
                    <span class="dept-stat-icon">📝</span>
                    <div class="dept-stat-number">{{ $departmentStats['total_tasks'] }}</div>
                    <div class="dept-stat-label">Jami vazifalar</div>
                    <div class="dept-stat-description">Ushbu davrda topshirilgan vazifalar</div>
                </div>

                <div class="dept-stat-card average">
                    <span class="dept-stat-icon">⭐</span>
                    <div class="dept-stat-number">{{ number_format($departmentStats['average_score'], 1) }}%</div>
                    <div class="dept-stat-label">O‘rtacha ball</div>
                    <div class="dept-stat-description">Bo‘lim o‘rtacha samaradorligi</div>
                </div>

                <div class="dept-stat-card ranking">
                    <span class="dept-stat-icon">🏆</span>
                    <div class="dept-stat-number">#{{ $departmentStats['department_rank'] }}</div>
                    <div class="dept-stat-label">Reytingdagi o‘rni</div>
                    <div class="dept-stat-description">Barcha bo‘limlar orasida</div>
                </div>
            </div>

            <!-- Performance Chart Section -->
            <div class="performance-chart-section fade-in">
                <div class="chart-header">
                    <h3 class="chart-title">Samaradorlik grafigi</h3>
                    <div class="chart-filters">
                        <button class="filter-btn active" data-period="week">Ushbu hafta</button>
                        <button class="filter-btn" data-period="month">Ushbu oy</button>
                        <button class="filter-btn" data-period="quarter">Ushbu chorak</button>
                    </div>
                </div>
                <div id="performance-chart" style="height: 300px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                    <i class="fa fa-chart-line fa-2x me-2"></i> Samaradorlik grafigi shu yerda ko‘rsatiladi
                </div>
            </div>

            <!-- Users Performance Section -->
            <div class="users-performance-section slide-up">
                <div class="users-performance-header">
                    <h2 class="users-performance-title">Jamoa samaradorligi</h2>
                </div>

                <div class="users-grid">
                    @foreach($departmentUsers as $user)
                        <div class="user-performance-card" onclick="viewUserDetails({{ $user->id }})">
                            <div class="user-card-header">
                                <div class="user-info">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($user->first_name, 0, 2)) }}
                                    </div>
                                    <div class="user-details">
                                        <h6>{{ $user->first_name .' '.$user->last_name }}</h6>
                                        <div class="user-role">{{  'Jamoa aʼzosi' }}</div>
                                    </div>
                                </div>
                                <div class="user-score">
                                    <div class="score-circle-small" style="--score: {{ $user->average_score ?? 0 }}">
                                        <div class="score-text-small">{{ number_format($user->average_score ?? 0, 0) }}%</div>
                                    </div>
                                </div>
                            </div>

                            <div class="user-stats">
                                <div class="user-stat">
                                    <div class="user-stat-number">{{ $user->tasks_count ?? 0 }}</div>
                                    <div class="user-stat-label">Vazifalar</div>
                                </div>
                                <div class="user-stat">
                                    <div class="user-stat-number">{{ $user->reviewed_tasks ?? 0 }}</div>
                                    <div class="user-stat-label">Ko‘rib chiqilgan</div>
                                </div>
                                <div class="user-stat">
                                    <div class="user-stat-number">{{ $user->scored_kpis ?? 0 }}</div>
                                    <div class="user-stat-label">Baholangan</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- KPI Performance Section -->
            <div class="kpi-performance-section slide-up">
                <div class="kpi-performance-header">
                    <h2 class="kpi-performance-title">KPI ko‘rsatkichlari tahlili</h2>
                </div>

                <div class="kpi-grid">
                    @foreach($kpis as $category)
                        <div class="kpi-category-card">
                            <div class="kpi-category-header">
                                <h5 class="kpi-category-name">{{ $category->name }}</h5>
                                <div class="kpi-category-score">
                                    {{ number_format($category->average_score ?? 0, 1) }}/100
                                </div>
                            </div>
                            <div class="kpi-children">
                                @foreach($category->children as $child)
                                    <div class="kpi-child-item">
                                        <div class="kpi-child-name">{{ $child->name }}</div>
                                        <div class="kpi-child-stats">
                                            <div class="tasks-count">
                                                {{ $child->department_tasks_count ?? 0 }} ta vazifa
                                            </div>
                                            <div class="child-score {{ $child->score ? '' : 'no-score' }}">
                                                {{ $child->score ? number_format($child->score, 0) . '/100' : 'Baholanmagan' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- User Details Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foydalanuvchi tafsilotlari</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="userDetailsContent">
                    <!-- Foydalanuvchi tafsilotlari shu yerda yuklanadi -->
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Filter buttons functionality
            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');

                let period = $(this).data('period');
                loadPerformanceChart(period);
            });

            // Load performance chart
            function loadPerformanceChart(period) {
                // Simulate chart loading
                $('#performance-chart').html('<div class="spinner"></div>');

                setTimeout(() => {
                    $('#performance-chart').html(`📊 Performance chart for ${period} period`);
                }, 1000);
            }

            // Animate score circles on scroll
            function animateScoreCircles() {
                $('.score-circle-small').each(function() {
                    let $this = $(this);
                    let score = $this.css('--score') || $this.attr('style').match(/--score:\s*(\d+)/)?.[1] || 0;

                    if (isElementInViewport($this[0])) {
                        $this.css('background', 'conic-gradient(#e0e0e0 0deg, #e0e0e0 360deg)');

                        setTimeout(() => {
                            $this.css('background', `conic-gradient(var(--success-color) 0deg, var(--success-color) ${score * 3.6}deg, #e0e0e0 ${score * 3.6}deg)`);
                        }, 500);
                    }
                });
            }

            // Check if element is in viewport
            function isElementInViewport(el) {
                let rect = el.getBoundingClientRect();
                return (
                    rect.top >= 0 &&
                    rect.left >= 0 &&
                    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
                );
            }

            // Trigger animations on scroll
            $(window).on('scroll', function() {
                animateScoreCircles();
            });

            // Initial animation trigger
            setTimeout(() => {
                animateScoreCircles();
            }, 500);

            // Add hover effects to user cards
            $('.user-performance-card').hover(
                function() {
                    $(this).find('.user-avatar').addClass('pulse');
                },
                function() {
                    $(this).find('.user-avatar').removeClass('pulse');
                }
            );
        });

        // View user details function
        function viewUserDetails(userId) {
            $('#userDetailsModal').modal('show');
            $('#userDetailsContent').html('<div class="text-center"><div class="spinner"></div><p>Loading user details...</p></div>');

            $.ajax({
                url: `/department/user/${userId}/details`,
                type: 'GET',
                success: function(response) {
                    $('#userDetailsContent').html(response);
                },
                error: function() {
                    $('#userDetailsContent').html('<div class="alert alert-danger">Error loading user details.</div>');
                }
            });
        }
    </script>
@endsection
