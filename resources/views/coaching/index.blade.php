{{-- resources/views/coaching/index.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  :root{
    --primary:#522583; --muted:#6b7280; --border:#e5e7eb; --card:#fff;
    --shadow:0 1px 2px rgba(16,24,40,.04),0 1px 3px rgba(16,24,40,.06);
  }

  /* Page header */
  .ch-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
  .ch-title{margin:0;font-size:28px;font-weight:800}
  .ch-sub{margin:2px 0 0;color:var(--muted);font-size:13px}

  /* Tabs */
  .tabs{display:flex;gap:22px;border-bottom:1px solid var(--border);margin:10px 0 16px}
  .tab{position:relative;padding:10px 0;color:#374151;font-weight:600;text-decoration:none}
  .tab.active{color:var(--primary)}
  .tab.active:after{content:"";position:absolute;left:0;right:0;bottom:-1px;height:2px;background:var(--primary);border-radius:2px}

  /* Button */
  .btn{display:inline-flex;align-items:center;gap:8px;border:0;border-radius:10px;padding:10px 14px;font-weight:700;cursor:pointer}
  .btn-primary{background:var(--primary);color:#fff}
  .btn-primary svg{fill:#fff}

  /* Card + table */
  .card{background:var(--card);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow)}
  .pad{padding:16px}
  .card-head{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid var(--border)}
  .card-title{margin:0;font-weight:700}
  .table-wrap{overflow:auto}
  table.clean{width:100%;border-collapse:separate;border-spacing:0}
  table.clean thead th{
    text-align:left;font-size:12px;color:#6b7280;font-weight:700;letter-spacing:.02em;
    padding:12px 16px;border-bottom:1px solid #eef2f7;background:#f8fafc
  }
  table.clean tbody td{padding:12px 16px;border-bottom:1px solid #f1f5f9;font-size:14px;color:#111827}
  table.clean tbody tr:last-child td{border-bottom:0}

  .muted{color:var(--muted)}
  .link{color:var(--primary);font-weight:600;text-decoration:none}
  .pill-info{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;background:#eef2ff;color:#3730a3;font-weight:700;font-size:12px}
  .actions{display:flex;align-items:center;justify-content:center}
  .act-eye{display:inline-flex;width:28px;height:28px;align-items:center;justify-content:center;border-radius:6px;}
  .act-eye:hover{background:#f3f4f6}
  .act-eye svg{fill:#6d28d9}
  .mt-12{margin-top:12px}
  .mt-20{margin-top:20px}
  .alert{background:#fffbeb;border:1px solid #fde68a;padding:12px 14px;border-radius:12px}
  .alert-title{font-weight:700;margin-bottom:6px}
  .header-filter{margin:8px 0 14px}
</style>

{{-- Header --}}
<div class="ch-head">
  <div>
    <h2 class="ch-title">Perfil del empleado</h2>
    <p class="ch-sub" style="font-size: 30px;">{{ $employee->name }} @if($employee->position) – {{ $employee->position }} @endif</p>
  </div>
  {{-- top-right icons placeholder (optional) --}}
</div>

{{-- Tabs --}}
<!-- <nav class="tabs">
  <a class="tab" href="{{ route('employees.show',$employee) }}">Overview</a>
  <a class="tab" href="{{ route('employees.show',$employee) }}#evaluations">Evaluations</a>
  <a class="tab active" href="javascript:void(0)">Coaching</a>
  <a class="tab" href="{{ route('employees.show',$employee) }}#performance">Performance</a>
</nav> -->

{{-- small filter link --}}
<div class="header-filter">
  <a href="{{ request()->fullUrlWithQuery(['due'=>'soon']) }}" class="link">Seguimientos previstos para los próximos 30 días</a>
</div>

{{-- Coaching Sessions card --}}
<section class="card">
  <div class="card-head">
    <h3 class="card-title">Sesiones de entrenamiento</h3>
    <a class="btn btn-primary" href="{{ route('coaching.create',$employee) }}">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M13 11h4v2h-4v4h-2v-4H7v-2h4V7h2v4Z"/></svg>
      Nueva sesión
    </a>
  </div>

  <div class="table-wrap">
    <table class="clean">
      <thead>
        <tr>
          <th style="width:16%">Fecha</th>
          <th>Resumen</th>
          <th style="width:18%">Fecha de seguimiento</th>
          <th style="width:16%">Adjuntos</th>
          <th style="width:10%;text-align:center">Comportamiento</th>
        </tr>
      </thead>
      <tbody>
        @foreach($list as $s)
        {{-- {!! $s->attachments !!} --}}
          @php
            $files = is_array($s->attachments) ? count($s->attachments) : 0;
          @endphp
          <tr>
            <td>{{ optional($s->date)->format('M d, Y') }}</td>
            <td class="muted">
              {{ \Illuminate\Support\Str::limit($s->summary ?? '—', 85) }}
            </td>
            <td>{{ $s->follow_up_date ? $s->follow_up_date->format('M d, Y') : '—' }}</td>
            <td>
              @if($files)
                <span class="pill-info">
                  @foreach ($s->attachments as $attachment)
                    <a href="{{ $attachment }}" target="_blank">{{ $attachment }}</a>
                  @endforeach
                  {{-- <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"><path d="M8 17a5 5 0 0 1 0-7l5-5a4 4 0 0 1 6 6l-5 5"/></svg>
                  {{ $files }} {{ $files>1?'files':'file' }} --}}
                </span>
              @else
                <span class="muted">—</span>
              @endif
            </td>
            <td style="text-align:center">
              <a class="act-eye " href="{{ route('coaching.edit',[$employee,$s]) }}" title="View">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30"><path d="m3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm17.71-10.2a1 1 0 0 0 0-1.41L18.36 3.3a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.84-1.83Z"/></svg>
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="pad">
    {{ $list->links() }}
  </div>
</section>

{{-- Due Soon alert --}}
@if($due->count())
  <div class="mt-20 alert">
    <div class="alert-title">Due Soon</div>
    <ul style="margin:0;padding-left:18px">
      @foreach($due as $d)
        <li>{{ optional($d->employee)->name }} — {{ optional($d->follow_up_date)->format('M d, Y') }}</li>
      @endforeach
    </ul>
  </div>
@endif
@endsection
