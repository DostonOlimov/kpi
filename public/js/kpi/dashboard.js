// Initialize AOS (Animate On Scroll)
AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true,
    offset: 100
});

// Chart.js configurations
const chartColors = {
    primary: '#4f46e5',
    secondary: '#7c3aed',
    success: '#10b981',
    warning: '#f59e0b',
    danger: '#ef4444',
    info: '#3b82f6'
};

// Counter animation for statistics
function animateCounters() {
    const counters = document.querySelectorAll('.stat-number[data-count]');

    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-count'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current);
        }, 16);
    });
}

// Initialize circular progress bars
function initCircularProgress() {
    const progressElements = document.querySelectorAll('.circular-progress');

    progressElements.forEach(element => {
        const percentage = element.getAttribute('data-percentage');
        const circle = element.querySelector('.progress-ring-circle');

        if (circle) {
            circle.style.setProperty('--percentage', percentage);
            circle.classList.add('active');
        }
    });
}

// Progress Overview Chart
function initProgressChart() {
    const ctx = document.getElementById('progressChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Bajarilgan', 'Jarayonda', 'Boshlanmagan'],
            datasets: [{
                data: [completedKpis, totalKpis - completedKpis, 0],
                backgroundColor: [
                    chartColors.success,
                    chartColors.warning,
                    chartColors.danger
                ],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 8
                }
            },
            animation: {
                animateRotate: true,
                duration: 2000
            }
        }
    });
}

// Score vs Target Chart
function initComparisonChart() {
    const ctx = document.getElementById('comparisonChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Joriy Ball', 'Maqsad Ball', 'Maksimal Ball'],
            datasets: [{
                label: 'Ball',
                data: [totalCurrentScore, totalTargetScore, totalMaxScore],
                backgroundColor: [
                    chartColors.primary,
                    chartColors.warning,
                    chartColors.success
                ],
                borderRadius: 12,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#6b7280'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6b7280'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 8
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeOutQuart'
            }
        }
    });
}

// Enhanced KPI Detail Modal
function showKpiDetail(userKpi) {
    const modal = new bootstrap.Modal(document.getElementById('kpiDetailModal'));
    const content = document.getElementById('kpiDetailContent');
    const progressPercentage = (userKpi.current_score / userKpi.kpi.max_score * 100).toFixed(1);

    content.innerHTML = `
        <div class="row">
            <div class="col-lg-8">
                <div class="kpi-detail-header mb-4">
                    <h3 class="kpi-detail-title">${userKpi.kpi.name}</h3>
                    <span class="kpi-detail-type ${userKpi.kpi.type.toLowerCase()}">${userKpi.kpi.type.charAt(0).toUpperCase() + userKpi.kpi.type.slice(1)}</span>
                </div>

                <div class="progress-overview mb-4">
                    <h5 class="section-subtitle">Jarayon Umumiy Ko'rinishi</h5>
                    <div class="progress-container">
                        <div class="progress-bar-large">
                            <div class="progress-fill" style="width: ${progressPercentage}%"></div>
                        </div>
                        <div class="progress-info-large">
                            <span class="progress-text">Maksimal ballning ${progressPercentage}% i erishildi</span>
                        </div>
                    </div>
                </div>

                ${userKpi.score && userKpi.score.feedback ? `
                <div class="feedback-section mb-4">
                    <h5 class="section-subtitle">Fikr-mulohaza</h5>
                    <div class="feedback-content">
                        <i class="fas fa-comment-dots"></i>
                        <p>${userKpi.score.feedback}</p>
                    </div>
                </div>
                ` : ''}

                <div class="timeline-section">
                    <h5 class="section-subtitle">Jarayon Tarixi</h5>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>KPI Yaratildi</h6>
                                <p class="text-muted">Maqsad va maksimal ball belgilandi</p>
                            </div>
                        </div>
                        <div class="timeline-item active">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>Joriy Holat</h6>
                                <p class="text-muted">${userKpi.current_score} ball to'plandi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="score-summary">
                    <h5 class="section-subtitle">Ball Taqsimoti</h5>
                    <div class="score-cards">
                        <div class="score-card current">
                            <div class="score-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="score-info">
                                <span class="score-value">${userKpi.current_score}</span>
                                <span class="score-label">Joriy Ball</span>
                            </div>
                        </div>

                        <div class="score-card target">
                            <div class="score-icon">
                                <i class="fas fa-target"></i>
                            </div>
                            <div class="score-info">
                                <span class="score-value">${userKpi.target_score}</span>
                                <span class="score-label">Maqsad Ball</span>
                            </div>
                        </div>

                        <div class="score-card max">
                            <div class="score-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="score-info">
                                <span class="score-value">${userKpi.kpi.max_score}</span>
                                <span class="score-label">Maksimal Ball</span>
                            </div>
                        </div>

                        ${userKpi.score ? `
                        <div class="score-card final">
                            <div class="score-icon">
                                <i class="fas fa-medal"></i>
                            </div>
                            <div class="score-info">
                                <span class="score-value">${userKpi.score.score}</span>
                                <span class="score-label">Yakuniy Ball</span>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;

    modal.show();
}

// Filter KPIs
function filterKPIs(filterValue) {
    const kpiCards = document.querySelectorAll('.child-kpi-card');

    kpiCards.forEach(card => {
        let show = true;

        if (filterValue !== 'all') {
            // Add filtering logic based on KPI status
            const progressElement = card.querySelector('.progress-value');
            if (progressElement) {
                const progress = parseFloat(progressElement.textContent);

                switch (filterValue) {
                    case 'completed':
                        show = progress >= 100;
                        break;
                    case 'in-progress':
                        show = progress > 0 && progress < 100;
                        break;
                    case 'not-started':
                        show = progress === 0;
                        break;
                }
            }
        }

        if (show) {
            card.style.display = 'block';
            card.style.animation = 'fadeInUp 0.5s ease';
        } else {
            card.style.display = 'none';
        }
    });
}

// Refresh Dashboard
function refreshDashboard() {
    const overlay = document.getElementById('loadingOverlay');
    overlay.style.display = 'flex';

    // Simulate API call
    setTimeout(() => {
        overlay.style.display = 'none';
        location.reload();
    }, 2000);
}

// Export Report
function exportReport() {
    // Implement export functionality
    alert('Hisobot eksport qilish funksiyasi ishlab chiqilmoqda...');
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    animateCounters();
    initCircularProgress();
    initProgressChart();
    initComparisonChart();

    // Add smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Add resize handler for charts
window.addEventListener('resize', function() {
    Chart.helpers.each(Chart.instances, function(instance) {
        instance.resize();
    });
});
