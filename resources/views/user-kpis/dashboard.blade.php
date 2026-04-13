@extends('layouts.app')

@section('content')
    <div class="section">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <i class="fe fe-users mr-1"></i> Foydalanuvchilar KPI Dashboard
                </li>
            </ol>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="panel panel-primary">
                    <div class="tab_wrapper page-tab">
                        <ul class="tab_list">
                            <li class="active">
                                <a href="#">
                                    <span class="visible-xs"></span>
                                    <i class="fa fa-list fa-lg">&nbsp;</i>
                                    Foydalanuvchilar Ro'yxati
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Work Zone Filter -->
                <div class="filters-wrapper bg-light p-4 rounded">
                    <form action="{{ route('kpis.user-kpis-dashboard') }}" method="GET" id="workZoneFilterForm">
                        <!-- Parent Work Zones - Modern Card Design -->
                        <div class="mb-4">
                            <label class="form-label mb-3">
                                <i class="fa fa-map-marker text-primary"></i> 
                                <strong>Ish Hududlari (Asosiy):</strong>
                            </label>
                            <div class="work-zones-scrollable">
                                <div class="d-flex flex-nowrap" id="workZonesContainer">
                                    @foreach($parentWorkZones as $index => $parentZone)
                                        <div class="work-zone-card-wrapper flex-shrink-0 me-2" style="width: 220px;">
                                            <div class="card work-zone-card 
                                                {{ $workZoneId == $parentZone->id ? 'border-primary bg-primary bg-opacity-10' : 'border-secondary' }}" 
                                                onclick="selectWorkZone({{ $parentZone->id }})" 
                                                style="cursor: pointer; transition: all 0.3s;">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="work-zone-icon">
                                                                <i class="fa fa-building-o fa-2x {{ $workZoneId == $parentZone->id ? 'text-primary' : 'text-muted' }}"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1 {{ $workZoneId == $parentZone->id ? 'text-primary' : '' }}">
                                                                <strong>{{ Str::limit($parentZone->name, 25) }}</strong>
                                                            </h6>
                                                            <small class="text-muted">{{ $parentZone->users()->where('role_id', 3)->count() }} xodim</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <input type="hidden" name="work_zone_id" id="workZoneId" value="{{ $workZoneId }}">
                        </div>

                        <!-- Child Work Zones Dropdown -->
                        @if($childWorkZones->count() > 0)
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label">
                                        <i class="fa fa-sitemap text-info"></i> 
                                        <strong>Qo'shimcha ish hududi:</strong>
                                    </label>
                                    <select name="child_work_zone_id" class="form-control form-control-lg" id="childWorkZone">
                                        <option value="">Barchasi</option>
                                        @foreach($childWorkZones as $childZone)
                                            <option value="{{ $childZone->id }}" {{ $childWorkZoneId == $childZone->id ? 'selected' : '' }}>
                                                {{ $childZone->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <div class="card-body">
                @if(session('message'))
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i> {{ session('message') }}
                    </div>
                @endif

                @if($users->isEmpty())
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle fa-2x mb-2"></i>
                        <p class="mb-0">Ushbu oy uchun foydalanuvchilar topilmadi</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered text-nowrap">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>F.I.O</th>
                                    <th>Lavozimi</th>
                                    <th>Ish hududi</th>
                                    <th>KPI lar Soni</th>
                                    <th>Maqsad Ball</th>
                                    <th>Joriy Ball</th>
                                    <th>Natija</th>
                                    <th>Harakatlar</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($users as $index => $user)
                                    <tr class="text-center align-middle">
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-left">
                                            <strong>{{ $user->full_name }}</strong>
                                        </td>
                                        <td>{{ $user->lavozimi ?? '-' }}</td>
                                        <td>{{ $user->work_zone->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-primary">{{ $user->total_kpis }}</span>
                                        </td>
                                        <td>
                                            <strong class="text-primary">{{ $user->total_target_score ?? 0 }}</strong>
                                        </td>
                                        <td>
                                            <strong class="text-success">{{ $user->total_current_score ?? 0 }}</strong>
                                        </td>
                                        <td>
                                            @if($user->total_target_score > 0)
                                                @php
                                                    $percentage = ($user->total_current_score / $user->total_target_score) * 100;
                                                @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar {{ $percentage >= 80 ? 'bg-success' : ($percentage >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ min($percentage, 100) }}%;" 
                                                         aria-valuenow="{{ $percentage }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ number_format($percentage, 1) }}%
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">0%</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('kpis.user-kpis-detail', $user->id) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="Batafsil ko'rish">
                                                <i class="fa fa-eye"></i> Ko'rish
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fe fe-users fa-2x mb-2"></i>
                                            <p class="mb-0">Foydalanuvchilar topilmadi</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            
                            @if($users->isNotEmpty())
                                <tfoot>
                                    <tr class="bg-light">
                                        <td colspan="5" class="text-right"><strong>Umumiy natija:</strong></td>
                                        <td colspan="4" class="text-center">
                                            <strong class="text-primary">
                                                {{ $users->sum('total_current_score') }} / {{ $users->sum('total_target_score') }}
                                            </strong>
                                            <br>
                                            @if($users->sum('total_target_score') > 0)
                                                <small class="text-muted">
                                                    {{ number_format(($users->sum('total_current_score') / $users->sum('total_target_score')) * 100, 1) }}%
                                                </small>
                                            @endif
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .work-zones-scrollable {
        overflow-x: auto;
        overflow-y: hidden;
        padding-bottom: 10px;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scroll-behavior: smooth;
    }
    
    .work-zones-scrollable::-webkit-scrollbar {
        height: 8px;
    }
    
    .work-zones-scrollable::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .work-zones-scrollable::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    .work-zones-scrollable::-webkit-scrollbar-thumb:hover {
        background: #555;
        cursor: pointer;
    }
    
    .work-zone-card {
        user-select: none;
    }
</style>
@endpush

@section('scripts')
<script>
    function selectWorkZone(zoneId) {
        // Update hidden input
        document.getElementById('workZoneId').value = zoneId;
        
        // Remove active state from all cards
        document.querySelectorAll('.work-zone-card').forEach(card => {
            card.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
            card.classList.add('border-secondary');
            const icon = card.querySelector('i');
            if (icon) {
                icon.classList.remove('text-primary');
                icon.classList.add('text-muted');
            }
            const h6 = card.querySelector('h6');
            if (h6) {
                h6.classList.remove('text-primary');
            }
        });
        
        // Add active state to selected card
        const selectedCard = event.currentTarget;
        selectedCard.classList.remove('border-secondary');
        selectedCard.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
        const selectedIcon = selectedCard.querySelector('i');
        if (selectedIcon) {
            selectedIcon.classList.remove('text-muted');
            selectedIcon.classList.add('text-primary');
        }
        const selectedH6 = selectedCard.querySelector('h6');
        if (selectedH6) {
            selectedH6.classList.add('text-primary');
        }
        
        // Fetch and update child work zones
        fetch(`/works-list/${zoneId}`)
            .then(response => response.json())
            .then(data => {
                const childWorkZone = document.getElementById('childWorkZone');
                if (childWorkZone) {
                    childWorkZone.innerHTML = '<option value="">Barchasi</option>';
                    data.forEach(zone => {
                        const option = document.createElement('option');
                        option.value = zone.id;
                        option.textContent = zone.name;
                        childWorkZone.appendChild(option);
                    });
                    
                    // Auto-submit form after updating child zones
                    setTimeout(() => {
                        document.getElementById('workZoneFilterForm').submit();
                    }, 300);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Submit form anyway
                document.getElementById('workZoneFilterForm').submit();
            });
    }
    
    // Add hover effect to work zone cards
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.work-zone-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                if (!this.classList.contains('border-primary')) {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                }
            });
            
            card.addEventListener('mouseleave', function() {
                if (!this.classList.contains('border-primary')) {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '';
                }
            });
        });
    });
</script>
@endsection
