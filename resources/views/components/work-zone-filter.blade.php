<!-- Work Zone Filter Component -->
<div class="user-kpi-dashboard-filters border-bottom bg-white px-3 py-3">
    <form action="{{ $actionUrl }}" method="GET" id="workZoneFilterForm{{ isset($suffix) ? $suffix : '' }}">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
                @if($showLabel)
                    <span class="text-muted small text-uppercase mb-0">Ish Hududlari</span>
                @endif
            </div>
        </div>

        <div class="mb-2 d-none d-md-block">
            @if($showLabel)
                <span class="d-block small text-muted mb-1">
                    <i class="fa fa-map-marker text-primary"></i> Hududlar
                </span>
            @endif
            <div class="user-kpi-zone-pills">
                @foreach($parentWorkZones as $parentZone)
                    @php $isActive = (int) $selectedWorkZoneId === (int) $parentZone->id; @endphp
                    <a href="{{ $actionUrl }}?work_zone_id={{ $parentZone->id }}"
                       class="btn btn-sm {{ $isActive ? 'btn-primary' : 'btn-outline-primary' }} mb-1"
                       title="{{ $parentZone->name }}"
                       @if($isActive) aria-current="true" @endif>
                        <span class="text-truncate d-inline-block" style="max-width: 14rem;">{{ $parentZone->name }}</span>
                        <span class="badge badge-light text-dark ml-1">{{ $parentZone->childs()->count() }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <input type="hidden" name="work_zone_id" value="{{ $selectedWorkZoneId }}">
        
        <div class="form-group mb-0">
            <label for="childWorkZone{{ isset($suffix) ? $suffix : '' }}" class="small text-muted mb-1 d-block">
                <i class="fa fa-sitemap text-info"></i> Qo'shimcha ish hududi (ixtiyoriy)
            </label>
            <select name="child_work_zone_id"
                    id="childWorkZone{{ isset($suffix) ? $suffix : '' }}"
                    class="form-control form-control-sm"
                    style="max-width: 28rem;"
                    @if($childWorkZones->isEmpty()) disabled @endif
                    onchange="this.form.submit()">
                <option value="">
                    {{ $childWorkZones->isEmpty() ? 'Bu hududda bo\'lim yo\'q' : 'Barchasi' }}
                </option>
                @foreach($childWorkZones as $childZone)
                    <option value="{{ $childZone->id }}" {{ (int) $selectedChildWorkZoneId === (int) $childZone->id ? 'selected' : '' }}>
                        {{ $childZone->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
</div>

@push('styles')
<style>
    .user-kpi-dashboard-filters .user-kpi-zone-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        align-items: center;
    }
    .user-kpi-dashboard-filters .user-kpi-zone-pills .btn {
        text-align: left;
    }
</style>
@endpush
