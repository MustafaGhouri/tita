@extends('layouts.app')

@section('content')
{{-- Page-scoped tweaks so design crisp rahe --}}
<style>
  /* equal height KPI cards on one row */
  .grid.grid-4 > .card { display:flex; }
  .grid.grid-4 > .card .kpi{ width:100% }

  /* KPI numbers a bit bolder */
  .kpi-value{ font-size:30px; font-weight:800; line-height:1.1 }

  /* Quick actions: ensure icons visible even when span empty */
  .qa-btn .qa-icon svg{ width:16px; height:16px; fill:#fff }
  .qa-btn .left{ gap:10px }
  .qa-btn{ transition:transform .04s ease }
  .qa-btn:active{ transform:translateY(1px) }
     
  /* Activity list spacing */
  .activity-item .link{ white-space:nowrap }

  /* Small top margin between rows on narrow screens */
  @media (max-width:1100px){
    .grid.grid-4{ row-gap:16px }
  }
</style>

<div class="page-head">
  <div>
    <h1>Dashboard</h1>
    <p>¡Bienvenidos de nuevo! Esto es lo que está pasando con tu equipo.</p>
  </div>
</div>

<!-- KPI cards -->
<div class="grid grid-4">
  <div class="card pad">
    <div class="kpi">
      <div>
        <div class="kpi-title">Empleados activas</div>
        <div class="kpi-value">{{ $activeEmployees }}</div>
        <div class="kpi-hint">+2 this month</div>
      </div>
      <div class="kpi-icon bg-purple-50">
        <svg class="text-purple-600" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
          <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-3.53 0-8 1.77-8 4v2h16v-2c0-2.23-4.47-4-8-4Z"/>
        </svg>
      </div>
    </div>
  </div>

  <div class="card pad">
    <div class="kpi">
      <div>
        <div class="kpi-title">Diagnóstico completado</div>
        <div class="kpi-value">{{ $diagCompleted }}</div>
        <div class="kpi-hint">+3 esta semana</div>
      </div>
      <div class="kpi-icon bg-indigo-50">
        <svg class="text-indigo-600" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
          <path d="M9 11h6v2H9v-2Zm3-9 2 2h3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h3l2-2Z"/>
        </svg>
      </div>
    </div>
  </div>

  <div class="card pad">
    <div class="kpi">
      <div>
        <div class="kpi-title">Evaluaciones (este mes)</div>
        <div class="kpi-value">{{ $evalsThisMonth }}</div>
        <div class="card-sub">Objetivo: 24</div>
      </div>
      <div class="kpi-icon bg-orange-50">
        <svg class="text-orange-500" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
          <path d="m21 20-5.2-5.2a7 7 0 1 0-1.4 1.4L20 21l1-1ZM5 10a5 5 0 1 1 10 0 5 5 0 0 1-10 0Z"/>
        </svg>
      </div>
    </div>
  </div>

  <div class="card pad">
    <div class="kpi">
      <div>
        <div class="kpi-title">Próximo entrenamiento</div>
        <div class="kpi-value">{{ $upcomingCoach }}</div>
        <div class="card-sub">Siguiente: Mañana</div>
      </div>
      <div class="kpi-icon bg-emerald-50">
        <svg class="text-emerald-600" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
          <path d="M20 17V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v11H0v2h24v-2h-4ZM6 6h12v9H6V6Z"/>
        </svg>
      </div>
    </div>
  </div>
</div>

<!-- Main grid -->
<div class="grid grid-3 mt-24">
  <!-- Recent Activity -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Actividad reciente</h3>
      <p class="card-sub">Últimas pruebas y evaluaciones</p>
    </div>
    <div class="activity">
      <ul class="activity-list">
        @forelse($recentActivities as $item)
          <li class="activity-item">
            {{-- Icon bubble per type --}}
            <div class="bubble {{ $item['type'] === 'diagnostic' ? 'b-purple' : 'b-indigo' }}">
              @if($item['type'] === 'diagnostic')
                {{-- Diagnostic icon --}}
                <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor">
                  <path d="M19 3H5a2 2 0 0 0-2 2v14l4-4h12a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Z"/>
                </svg>
              @else
                {{-- Evaluation/search icon --}}
                <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor">
                  <path d="M12 2a10 10 0 1 0 6.32 17.74L22 22l-2.26-3.68A10 10 0 0 0 12 2Z"/>
                </svg>
              @endif
            </div>

            <div class="w-100">
              <div class="activity-text">
                <strong>{{ $item['employee'] }}</strong> {{ $item['label'] }}
                {{-- Optional score display for diagnostics:
                @if(($item['type'] ?? null) === 'diagnostic' && isset($item['score']))
                  — Score: {{ $item['score'] }}
                @endif
                --}}
              </div>
              <div class="activity-meta">
                {{ optional($item['when'])->diffForHumans() }}
              </div>
            </div>

            <!--<a class="link" href="{{ $item['url'] }}">View</a>-->
          </li>
        @empty
          <li class="activity-item"><div class="activity-text">Ninguna actividad reciente.</div></li>
        @endforelse
      </ul>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Acciones rápidas</h3>
      <p class="card-sub">Tareas comunes</p>
    </div>
    <div class="qa">
      <a href="{{ route('employees.create') }}" class="qa-btn bg-purple">
        <span class="left">
          <span class="qa-icon">
            <svg viewBox="0 0 24 24"><path d="M13 11h4v2h-4v4h-2v-4H7v-2h4V7h2v4Z"/></svg>
          </span>
          Agregar empleado
        </span>
      </a>

      {{-- Uncomment and wire routes as needed
      <a href="{{ route('diagnostics.index') }}" class="qa-btn bg-blue">
        <span class="left">
          <span class="qa-icon">
            <svg viewBox="0 0 24 24"><path d="M9 11h6v2H9v-2Zm3-9 2 2h3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h3l2-2Z"/></svg>
          </span>
          Run Diagnostic
        </span>
      </a>

      <a href="{{ route('mystery.create') }}" class="qa-btn bg-orange">
        <span class="left">
          <span class="qa-icon">
            <svg viewBox="0 0 24 24"><path d="m21 20-5.2-5.2a7 7 0 1 0-1.4 1.4L20 21l1-1ZM5 10a5 5 0 1 1 10 0 5 5 0 0 1-10 0Z"/></svg>
          </span>
          New Evaluation
        </span>
      </a>

      <a href="{{ route('coaching.create') }}" class="qa-btn bg-green">
        <span class="left">
          <span class="qa-icon">
            <svg viewBox="0 0 24 24"><path d="M20 17V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v11H0v2h24v-2h-4ZM6 6h12v9H6V6Z"/></svg>
          </span>
          Schedule Coaching
        </span>
      </a>
      --}}
    </div>
  </div>
</div>
@endsection
