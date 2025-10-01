{{-- resources/views/employees/show.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  :root{
    --primary:#522583; --muted:#6b7280; --border:#e5e7eb; --card:#fff;
    --shadow:0 1px 2px rgba(16,24,40,.04),0 1px 3px rgba(16,24,40,.06);
  }

  /* Grid layout (no Tailwind) */
  .es-grid{display:grid;grid-template-columns:1fr 2fr;gap:18px}
  @media (max-width:992px){ .es-grid{grid-template-columns:1fr} }

  /* Card */
  .card{background:var(--card);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow)}
  .pad{padding:18px}
  .card-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
  .card-title{margin:0;font-weight:700}
  .muted{color:var(--muted)}
  .link{color:var(--primary);text-decoration:none;font-weight:600}

  /* Profile */
  .profile{display:flex;align-items:flex-start;gap:12px}
  .avatar{width:56px;height:56px;border-radius:999px;overflow:hidden;background:#e5e7eb;display:grid;place-items:center;font-weight:800;color:#374151}
  .avatar img{width:100%;height:100%;object-fit:cover}
  .p-name{font-size:20px;font-weight:800;margin:0}
  .p-pos{margin-top:2px;color:var(--muted)}
  .pill{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:700;border:0;cursor:pointer}
  .dot{width:8px;height:8px;border-radius:999px}
  .pill-active{background:#dcfce7;color:#166534}
  .pill-active .dot{background:#16a34a}
  .pill-inactive{background:#e5e7eb;color:#374151}
  .pill-inactive .dot{background:#9ca3af}

  /* Buttons */
  .btn{display:inline-flex;align-items:center;gap:8px;border:0;border-radius:10px;padding:10px 14px;font-weight:700;cursor:pointer}
  .btn-primary{background:var(--primary);color:#fff}
  .btn-accent{background:#07d421;color:#0f172a}
  .btn-disabled{background:#eef2f7;color:#6b7280;cursor:not-allowed}

  /* Table */
  .table-wrap{overflow:auto;margin-top:6px}
  table.clean{width:100%;border-collapse:separate;border-spacing:0}
  table.clean thead th{
    text-align:left;font-size:12px;color:#6b7280;font-weight:700;letter-spacing:.02em;
    padding:12px 14px;border-bottom:1px solid #eef2f7;background:#f8fafc
  }
  table.clean tbody td{padding:12px 14px;border-bottom:1px solid #f1f5f9;font-size:14px}
  table.clean tbody tr:last-child td{border-bottom:0}

  /* Score badge */
  .score{display:inline-block;border-radius:999px;padding:4px 10px;font-size:12px;font-weight:700}
  .score.green{background:#dcfce7;color:#166534}
  .score.yellow{background:#fef9c3;color:#b45309}
  .score.red{background:#fee2e2;color:#991b1b}
</style>

@php
  $initial = strtoupper(mb_substr($employee->name ?? 'U',0,1));
@endphp

<div class="es-grid">
  {{-- LEFT: Profile --}}
  <section class="card pad">
    <div class="card-head">
      <div>
        <h2 class="p-name">{{ $employee->name }}</h2>
        <div class="p-pos">{{ $employee->position ?? '-' }}</div>
      </div>

      <form method="POST" action="{{ route('employees.toggle',$employee) }}">
        @csrf
        @if($employee->status === 'ACTIVE')
          <button class="pill pill-active" title="Toggle status">
            <span class="dot"></span> Activo
          </button>
        @else
          <button class="pill pill-inactive" title="Toggle status">
            <span class="dot"></span> Inactivo
          </button>
        @endif
      </form>
    </div>

    <div class="profile" style="align-items:center;">
      <div class="avatar">
        @if(!empty($employee->avatar_url))
          <img src="{{ $employee->avatar_url }}" alt="{{ $employee->name }}">
        @else
          {{ $initial }}
        @endif
      </div>
      <div>
        <div><span class="muted">Correo electrónico:</span class="p-pos"> {{ $employee->email ?? '-' }}</div>
      </div>
    </div>
  </section>

  {{-- RIGHT: Tabs/blocks --}}
  <div style="display:grid;gap:18px">
    {{-- Mystery Shopper --}}
    <section class="card pad">
      <div class="card-head">
        <h3 class="card-title">Comprador misterioso</h3>
        @if($canNewMystery)
          <a class="btn btn-primary" href="{{ route('mystery.create',$employee) }}">Nueva evaluación</a>
        @else
          <button class="btn btn-disabled" disabled>Sólo una evaluación por mes</button>
        @endif
      </div>

      <div class="table-wrap">
        <table class="clean">
          <thead>
            <tr>
              <th style="width:28% text-align: center; font-weight:900; font-size:14px;">Mes</th>
              <th style="width:14% text-align: center; font-weight:900; font-size:14px;">Puntaje</th>
              <th style="width:22% text-align: center; font-weight:900; font-size:14px;">Video</th>
              <th style="width:36% text-align: center; font-weight:900; font-size:14px;">Comportamiento</th>
            </tr>
          </thead>
          <tbody>
          @foreach($employee->evaluations()->latest()->paginate(10) as $ev)
            @php
              $score = (int)($ev->score ?? 0);
              $class = $score >= 80 ? 'green' : ($score >= 60 ? 'yellow' : 'red');
              $video = !empty($ev->video_path) ? asset('storage/'.$ev->video_path) : null;
            @endphp
            <tr>
              <td>{{ $ev->monthKey }}</td>
              <td><span class="score {{ $class }}">{{ $score }}</span></td>
              <td>
                @if($video)
                  <a class="link" target="_blank" href="{{ $video }}">Ver vídeo</a>
                @else
                  <span class="muted">—</span>
                @endif
              </td>
              <td>
                <a class="link" href="{{ route('mystery.show',$ev) }}">Ver informe</a>
                &nbsp;·&nbsp;
                <a class="link" href="{{ route('mystery.pdf',$ev) }}">PDF</a>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </section>

    {{-- Diagnostic Test --}}
    <section class="card pad">
      <div class="card-head">
        <h3 class="card-title">Prueba de Diagnóstico</h3>
        @if(!$diagnostic)
          <a class="btn btn-primary" href="{{ route('diagnostic.start',$employee) }}">Ejecutar prueba de diagnóstico</a>
        @else
          <button class="btn btn-disabled" disabled>Diagnóstico completado</button>
        @endif
      </div>

      @if($diagnostic)
        <div>
          Puntaje: <strong>{{ $diagnostic->score }}</strong>
          &nbsp;·&nbsp;<a class="link" href="{{ route('diagnostic.view',$employee) }}">vista</a>
          &nbsp;·&nbsp;<a class="link" href="{{ route('diagnostic.pdf',$employee) }}">PDF</a>
        </div>
      @endif
    </section>

    {{-- Coaching Sessions --}}
    <section class="card pad">
      <div class="card-head">
        <h3 class="card-title">Sesiones de entrenamiento</h3>
        <a class="btn btn-accent" href="{{ route('coaching.create',$employee) }}">Nueva sesión</a>
      </div>
      <a class="link" href="{{ route('coaching.index',$employee) }}">Abrir lista de coaching y filtrar</a>
    </section>
  </div>
</div>
@endsection
