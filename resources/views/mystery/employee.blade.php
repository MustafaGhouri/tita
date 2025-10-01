@extends('layouts.app')

@section('content')
<style>
  :root{ --primary:#522583; --muted:#6b7280; --border:#e5e7eb; --card:#fff; --shadow:0 1px 2px rgba(16,24,40,.04),0 1px 3px rgba(16,24,40,.06) }

  .head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
  .title{margin:0;font-size:22px;font-weight:800}
  .chip{background:#efe6fb;color:#3b0ca3;padding:6px 10px;border-radius:999px;font-weight:700;font-size:12px;margin-left:10px}

  .card{background:var(--card);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow)}
  .pad{padding:16px}
  .card-h{padding:14px 16px;border-bottom:1px solid var(--border)}
  .card-t{margin:0;font-weight:700}
  .card-s{margin:4px 0 0;color:var(--muted);font-size:13px}

  .profile{display:flex;align-items:center;gap:14px}
  .avatar{width:44px;height:44px;border-radius:999px;overflow:hidden;background:#e5e7eb;display:grid;place-items:center;font-weight:800;color:#374151}
  .avatar img{width:100%;height:100%;object-fit:cover}
  .name{font-weight:800}
  .meta{color:var(--muted);font-size:12px}

  .badge{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:4px 10px;font-size:12px;font-weight:700}
  .badge-active{background:#dcfce7;color:#166534}
  .badge-inactive{background:#fee2e2;color:#991b1b}
  .dot{width:8px;height:8px;border-radius:999px;background:#16a34a}
  .badge-inactive .dot{background:#991b1b}

  .btn{display:inline-flex;align-items:center;gap:8px;border:0;border-radius:10px;padding:10px 16px;font-weight:700;cursor:pointer}
  .btn svg{fill:currentColor}
  .btn-primary{background:var(--primary);color:#fff}
  .btn-disabled{background:#ede9fe;color:#6b21a8;cursor:not-allowed}

  .table{width:100%;border-collapse:separate;border-spacing:0}
  .table thead th{padding:12px 16px;text-align:left;font-size:12px;color:#6b7280;border-bottom:1px solid #eef2f7;background:#f8fafc}
  .table tbody td{padding:14px 16px;border-bottom:1px solid #f1f5f9}

  .score{display:inline-block;border-radius:999px;padding:4px 10px;font-size:12px;font-weight:700}
  .green{background:#dcfce7;color:#166534} .yellow{background:#fef9c3;color:#b45309} .red{background:#fee2e2;color:#991b1b}
  .link{color:var(--primary);font-weight:600}

  /* ===== Modal for company code ===== */
  .ms-modal{position:fixed;inset:0;background:rgba(0,0,0,.45);display:none;align-items:center;justify-content:center;z-index:60}
  .ms-box{background:#fff;border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow);padding:16px;width:420px;max-width:calc(100% - 32px)}
  .ms-box-h{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
  .ms-box-t{font-weight:800}
  .ms-input{width:100%;border:1px solid var(--border);border-radius:10px;padding:10px 12px;outline:none}
  .ms-input:focus{box-shadow:0 0 0 3px rgba(82,37,131,.12);border-color:#c7b5e0}
  .ms-close{border:0;background:transparent;cursor:pointer;font-size:16px;line-height:1}
  .ms-desc{color:#6b7280;font-size:13px;margin:6px 0 12px}
  .ms-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:12px}
</style>

@php
  $emp = $employee;
  $initial = strtoupper(mb_substr($emp->name ?? 'U',0,1));
  $isActive = strtoupper($emp->status ?? '') === 'ACTIVE';
@endphp

{{-- error flash (e.g., invalid code, monthly limit) --}}
@if ($errors->any())
  <div class="card pad" style="margin-bottom:12px;background:#fef2f2;border-color:#fecaca">
    @foreach ($errors->all() as $msg)
      <div style="color:#991b1b;font-weight:600">{{ $msg }}</div>
    @endforeach
  </div>
@endif
@if (session('ok'))
  <div class="card pad" style="margin-bottom:12px;background:#ecfdf5;border-color:#bbf7d0;color:#065f46;font-weight:600">
    {{ session('ok') }}
  </div>
@endif

<div class="head">
  <div style="display:flex;align-items:center;gap:8px">
    <h1 class="title">Módulo de comprador misterioso</h1>
    <span class="chip">Perfil del empleado</span>
  </div>
</div>

{{-- profile strip --}}
<div class="card pad" style="margin-bottom:16px">
  <div class="profile">
    <div class="avatar">
      @if(!empty($emp->avatar_url))
        <img src="{{ $emp->avatar_url }}" alt="{{ $emp->name }}">
      @else
        {{ $initial }}
      @endif
    </div>
    <div style="flex:1">
      <div class="name">{{ $emp->name }}</div>
      <div class="meta">
        {{ $emp->position ?? 'Sales Associate' }}
        @if(!empty($emp->employee_code)) • ID: {{ $emp->employee_code }} @endif
      </div>
    </div>
    <div>
      <span class="badge {{ $isActive ? 'badge-active' : 'badge-inactive' }}">
        <span class="dot"></span>{{ $isActive ? 'Active' : 'Inactive' }}
      </span>
    </div>
  </div>
</div>

{{-- evaluations block --}}
<div class="card" style="margin-bottom:16px">
  <div class="card-h" style="display:flex;align-items:center;justify-content:space-between">
    <div>
      <h3 class="card-t">Evaluaciones de compradores misteriosos</h3>
      <p class="card-s">Gestionar y realizar un seguimiento de las evaluaciones de compradores misteriosos para este empleado</p>
    </div>

    @if($canNewMystery ?? true)
      {{-- 🔐 Open modal to enter company code --}}
      <button type="button" class="btn btn-primary" id="evalBtn">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M13 11h4v2h-4v4h-2v-4H7v-2h4V7h2v4Z"/></svg>
        Nueva evaluación
      </button>
    @else
      <button class="btn btn-disabled" type="button" disabled>Sólo una evaluación por mes</button>
    @endif
  </div>

  <div class="pad">
    <div class="card-t" style="margin:0 0 10px">Archivo de evaluación</div>
    <table class="table">
      <thead>
        <tr>
          <th>Mes</th>
          <th>Puntaje</th>
          <th>Evaluador</th>
          <th>Video</th>
          <th>Comportamiento</th>
        </tr>
      </thead>
      <tbody>
        @forelse($archive as $ev)
          @php
            $s = (int)($ev->score ?? 0);
            $cls = $s >= 80 ? 'green' : ($s >= 60 ? 'yellow' : 'red');
            $monthLabel = $ev->monthKey ?? \Illuminate\Support\Carbon::parse($ev->created_at)->isoFormat('MMMM YYYY');
            $videoUrl = !empty($ev->video_path) ? asset($ev->video_path) : null; // videos/... under public
            $evaluator = optional($ev->creator)->name ?? '—';
          @endphp
          <tr>
            <td>{{ $monthLabel }}</td>
            <td><span class="score {{ $cls }}">{{ $s }}/100</span></td>
            <td>{{ $evaluator }}</td>
            <td>
              @if($videoUrl)
                <a class="link" href="{{ $videoUrl }}" target="_blank">Ver vídeo</a>
              @else — @endif
            </td>
            <td>
              <a class="link" href="{{ route('mystery.show',$ev) }}">Ver detalles</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="color:#6b7280">Aún no hay evaluaciones.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- ===== Modal: Enter company code to unlock ===== --}}
<div class="ms-modal" id="codeModal" style="display:none">
  <div class="ms-box">
    <div class="ms-box-h">
      <div class="ms-box-t">Ingrese el código de seguridad</div>
      <button class="ms-close" id="codeClose" aria-label="Close">✕</button>
    </div>
    <p class="ms-desc">Sólo su equipo interno puede iniciar una nueva evaluación.</p>

    <form method="POST" action="{{ route('mystery.unlock', $employee) }}" id="codeForm">
      @csrf
      <input class="ms-input" name="code" id="codeInput" type="password" placeholder="Company code" autocomplete="one-time-code" required>
      <div class="ms-actions">
        <button class="btn btn-disabled" type="button" id="codeCancel">Cancelar</button>
        <button class="btn btn-primary" type="submit" id="codeGo">Comenzar</button>
      </div>
    </form>
  </div>
</div>

<script>
(function(){
  const btn = document.getElementById('evalBtn');
  const modal = document.getElementById('codeModal');
  const input = document.getElementById('codeInput');
  const cancel = document.getElementById('codeCancel');
  const close = document.getElementById('codeClose');

  if(btn){
    btn.addEventListener('click', () => {
      modal.style.display = 'flex';
      setTimeout(() => input && input.focus(), 80);
    });
  }
  const hide = () => { modal.style.display = 'none'; if(input) input.value=''; };
  cancel && cancel.addEventListener('click', hide);
  close && close.addEventListener('click', hide);
  modal && modal.addEventListener('click', (e) => { if(e.target === modal) hide(); });

  // press Enter to submit
  input && input.addEventListener('keydown', (e) => {
    if(e.key === 'Enter'){
      e.preventDefault();
      document.getElementById('codeForm').submit();
    }
  });
})();
</script>
@endsection
