@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
.stat-card .icon-box.blue   { background: #eff6ff; color: #3b82f6; }
.stat-card .icon-box.yellow { background: #fffbeb; color: #f59e0b; }
.stat-card .icon-box.green  { background: #f0fdf4; color: #22c55e; }
.stat-card .icon-box.red    { background: #fef2f2; color: #ef4444; }
.stat-card .stat-value      { font-size: 1.75rem; font-weight: 700; color: #1e293b; }
.stat-card .stat-label      { font-size: .8rem; color: #64748b; font-weight: 500; }
.stat-card .stat-pct        { font-size: .75rem; color: #94a3b8; }
</style>
@endpush

@section('content')

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    @foreach([
        ['label'=>'Total Projects', 'value'=>$total,    'pct'=>100,            'icon'=>'bi-folder2',         'box'=>'blue'],
        ['label'=>'Pending',        'value'=>$pending,  'pct'=>$pct($pending), 'icon'=>'bi-hourglass-split', 'box'=>'yellow'],
        ['label'=>'Approved',       'value'=>$approved, 'pct'=>$pct($approved),'icon'=>'bi-check-circle',    'box'=>'green'],
        ['label'=>'Rejected',       'value'=>$rejected, 'pct'=>$pct($rejected),'icon'=>'bi-x-circle',        'box'=>'red'],
    ] as $c)
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box {{ $c['box'] }}">
                    <i class="bi {{ $c['icon'] }}"></i>
                </div>
                <div>
                    <div class="stat-label">{{ $c['label'] }}</div>
                    <div class="stat-value">{{ $c['value'] }}</div>
                    <div class="stat-pct">{{ $c['pct'] }}% of total</div>
                </div>
            </div>
            <div class="progress mt-3" style="height:3px">
                <div class="progress-bar bg-{{ ['blue'=>'primary','yellow'=>'warning','green'=>'success','red'=>'danger'][$c['box']] }}"
                     style="width:{{ $c['pct'] }}%"></div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-3">
    {{-- Chart --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart me-2 text-primary"></i>Submissions — Last 7 Days
            </div>
            <div class="card-body">
                <canvas id="chart" height="110"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history me-2 text-primary"></i>Recent Projects
            </div>
            <ul class="list-group list-group-flush">
                @forelse($recent as $p)
                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                    <div>
                        <a href="{{ route('projects.show', $p) }}" class="fw-semibold text-decoration-none text-dark">
                            {{ Str::limit($p->title, 28) }}
                        </a>
                        <div class="text-muted" style="font-size:.75rem">
                            {{ $p->user->name }} · {{ $p->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <span class="badge {{ $p->statusBadgeClass() }}">{{ ucfirst($p->status) }}</span>
                </li>
                @empty
                <li class="list-group-item text-muted text-center py-4">No projects yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function(){
    const map = @json($chartData);
    const labels = [], data = [];
    for(let i = 6; i >= 0; i--){
        const d = new Date();
        d.setDate(d.getDate() - i);
        const key = d.toISOString().slice(0,10);
        labels.push(d.toLocaleDateString('en',{month:'short',day:'numeric'}));
        data.push(map[key] ?? 0);
    }
    new Chart(document.getElementById('chart'),{
        type:'bar',
        data:{
            labels,
            datasets:[{
                label:'Submissions', data,
                backgroundColor:'rgba(59,130,246,.7)',
                borderRadius:5, borderSkipped:false,
            }]
        },
        options:{
            responsive:true,
            plugins:{legend:{display:false}},
            scales:{y:{beginAtZero:true, ticks:{stepSize:1}, grid:{color:'#f1f5f9'}},
                    x:{grid:{display:false}}}
        }
    });
})();
</script>
@endpush
