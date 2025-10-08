{{-- resources/views/mystery/show.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  :root{
    --primary:#522583; --muted:#6b7280; --border:#e5e7eb; --bg:#f6f7fb;
    --card:#fff; --shadow:0 1px 2px rgba(16,24,40,.04),0 1px 3px rgba(16,24,40,.06);
  }
  .ms-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
  .ms-title{margin:0;font-size:22px;font-weight:800}
  .ms-chip{background:#efe6fb;color:#3b0ca3;padding:6px 10px;border-radius:999px;font-weight:700;font-size:12px;margin-left:10px}
  .ms-right{display:flex;align-items:center;gap:14px;color:#9ca3af}

  .card{background:var(--card);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow)}
  .card-pad{padding:16px}
  .card-head{padding:14px 16px;border-bottom:1px solid var(--border)}
  .card-title{margin:0;font-weight:700}
  .card-sub{margin:4px 0 0;color:var(--muted);font-size:13px}

  .profile{display:flex;align-items:center;gap:14px}
  .avatar{width:44px;height:44px;border-radius:999px;overflow:hidden;background:#e5e7eb;display:grid;place-items:center;font-weight:800;color:#374151}
  .avatar img{width:100%;height:100%;object-fit:cover}
  .prof-name{font-weight:700}
  .prof-meta{color:var(--muted);font-size:12px}
  .badge{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:4px 10px;font-size:12px;font-weight:700}
  .badge-active{background:#dcfce7;color:#166534}
  .dot{width:8px;height:8px;border-radius:999px;background:#16a34a}

  .btn{display:inline-flex;align-items:center;gap:8px;border:0;border-radius:10px;padding:10px 14px;font-weight:700;cursor:pointer}
  .btn-primary{background:var(--primary);color:#fff}

  .table-wrap{overflow:auto}
  table.ms{width:100%;border-collapse:separate;border-spacing:0}
  table.ms thead th{
    text-align:left;font-size:12px;color:#6b7280;font-weight:700;letter-spacing:.02em;
    padding:12px 16px;border-bottom:1px solid #eef2f7;background:#f8fafc
  }
  table.ms tbody td{padding:14px 16px;border-bottom:1px solid #f1f5f9;font-size:14px;color:#111827}
  table.ms tbody tr:last-child td{border-bottom:0}

  .score{display:inline-block;border-radius:999px;padding:4px 10px;font-size:12px;font-weight:700}
  .score.green{background:#dcfce7;color:#166534}
  .score.yellow{background:#fef9c3;color:#b45309}
  .score.red{background:#fee2e2;color:#991b1b}

  .link{color:var(--primary);font-weight:600}
  .mt-16{margin-top:16px}
  .mt-24{margin-top:24px}
  .sec-h{font-size:15px;font-weight:800;margin:16px 0 8px}
  .muted{color:var(--muted)}
</style>

@php
  $emp = $evaluation->employee;
  $initial = strtoupper(mb_substr($emp->name ?? 'U',0,1));
  // archive list (relation ka naam aapke project me evaluations() hai)
  $archive = method_exists($emp, 'evaluations') ? $emp->evaluations()->latest()->get() : collect([$evaluation]);

  // schema normalize (array hi chahiye)
  $schema = is_string($evaluation->checklist->schema)
            ? json_decode($evaluation->checklist->schema, true)
            : $evaluation->checklist->schema;

  // sections/items normalize
  $sections = [];
  if (isset($schema['sections']) && is_array($schema['sections'])) {
      $sections = $schema['sections'];
  } elseif (isset($schema['items']) && is_array($schema['items'])) {
      $sections = [[ 'title' => $schema['title'] ?? 'Checklist', 'items' => $schema['items'] ]];
  }

  $answers = is_string($evaluation->answers) ? json_decode($evaluation->answers,true) : $evaluation->answers;
  if (!is_array($answers)) $answers = [];
@endphp

{{-- Page heading --}}
<div class="ms-head">
  <div style="display:flex;align-items:center">
    <h1 class="ms-title">Módulo de comprador misterioso</h1>
    <span class="ms-chip">Perfil del empleado</span>
  </div>
  <div class="ms-right"></div>
</div>

{{-- Profile strip --}}
<div class="card card-pad" style="margin-bottom:16px;">
  <div class="profile">
    <div class="avatar">
      @if(!empty($emp->avatar_url))
        <img src="{{ $emp->avatar_url }}" alt="{{ $emp->name }}">
      @else
        {{ $initial }}
      @endif
    </div>
    <div style="flex:1">
      <div class="prof-name">{{ $emp->name }}</div>
      <div class="prof-meta">
        {{ $emp->position ?? 'Employee' }}
        @if(!empty($emp->employee_code)) • ID: {{ $emp->employee_code }} @endif
      </div>
    </div>
    <div>
      @php $isActive = ($emp->status ?? '') === 'ACTIVE'; @endphp
      <span class="badge {{ $isActive ? 'badge-active' : '' }}">
        <span class="dot"></span>{{ $isActive ? 'Activo' : 'Inactivo' }}
      </span>
    </div>
  </div>
</div>

{{-- Evaluation Archive table --}}
<div class="card">
  <div class="card-head">
    <h3 class="card-title">Archivo de evaluación eeeeeeeeeeeeeeeeeeee</h3>
    <p class="card-sub">Gestionar y realizar un seguimiento de las evaluaciones de compradores misteriosos para este empleado</p>
  </div>

  <div class="table-wrap">
    <table class="ms">
      <thead>
        <tr>
          <th style="width:22%; text-align:center; font-weight:900; font-size:16px;">Mes</th>
          <th style="width:14%; text-align:center; font-weight:900; font-size:16px;">Puntaje</th>
          <th style="width:20%; text-align:center; font-weight:900; font-size:16px;">Video</th>
          <th style="width:20%; text-align:center; font-weight:900; font-size:16px;">Comportamiento</th>
        </tr>
      </thead>
      <tbody>
        @forelse($archive as $ev)
          @php
            $score = (int)($ev->score ?? 0);
            $scoreClass = $score >= 80 ? 'green' : ($score >= 60 ? 'yellow' : 'red');
            $monthLabel = $ev->monthKey ?? \Illuminate\Support\Carbon::parse($ev->created_at)->isoFormat('MMMM YYYY');
            $videoUrl = !empty($ev->video_path) ? asset($ev->video_path) : null; // ✅ correct

            // dd($videoUrl);
          @endphp
          <tr style="text-align:center">
            <td>{{ $monthLabel }}</td>
            <td><span class="score {{ $scoreClass }}">{{ $score }}/100</span></td>
            <td>
              @if($videoUrl)
                <a class="link" href="{{ $videoUrl }}" target="_blank">Ver vídeo</a>
              @else
                —
              @endif
            </td>
            <td>
              <a class="link" href="{{ route('mystery.pdf',$ev) }}">Descargar PDF</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="color:#6b7280;padding:18px">Aún no hay evaluaciones.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Current evaluation details --}}
<div class="card mt-24">
  <div class="card-head">
    <h3 class="card-title">Evaluación actual</h3>
    <p class="card-sub">Month: {{ $evaluation->monthKey }} • Puntaje: {{ $evaluation->score }}/100</p>
  </div>

  <div class="card-pad">
    @php $videoUrl = !empty($evaluation->video_path) ? asset($evaluation->video_path) : null; @endphp
    @if($videoUrl)
    {{ $videoUrl }}
      <video controls style="width:100%;border:1px solid var(--border);border-radius:12px">
        <source src="{{ $videoUrl }}" type="video/mp4">
        <a href="{{ $videoUrl }}" class="link">Ver vídeo</a>
      </video>
    @else
      <div class="muted">No hay vídeo adjunto mazzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz.</div>
    @endif

    {{-- Answers by section --}}
    @foreach($sections as $sec)
      <div class="sec-h">{{ $sec['title'] ?? 'Section' }}</div>
      @if(!empty($sec['desc'])) <div class="muted" style="margin-bottom:6px">{{ $sec['desc'] }}</div> @endif
      <div class="table-wrap">
        <table class="ms" style="margin-top:6px">
          <tbody>
            @foreach(($sec['items'] ?? []) as $i)
              @php
                $k = $i['key'] ?? null;
                $label = $i['label'] ?? $k;
                $type = $i['type'] ?? 'text';
                $val = $k ? ($answers[$k] ?? null) : null;

                $display = '—';
                if(!is_null($val)){
                  switch($type){
                    case 'yes_no':
                      $display = ($val==='yes' || $val===1 || $val===true || $val==='1') ? 'SI' : 'No';
                      break;
                    case 'yes_no_na':
                      if(in_array(strtolower((string)$val), ['na','n/a'])) $display = 'N/A';
                      else $display = ($val==='yes' || $val===1 || $val===true || $val==='1') ? 'Si' : 'No';
                      break;
                    case 'scale':
                    case 'range':
                      $min = $i['min'] ?? 1; $max = $i['max'] ?? 5;
                      $display = $val . " (min {$min} – max {$max})";
                      break;
                    case 'note':
                    case 'textarea':
                      $display = nl2br(e($val));
                      break;
                    default:
                      $display = is_array($val) ? json_encode($val) : e((string)$val);
                  }
                }
              @endphp
              <tr>
                <td style="width:45%;padding:10px 8px;border-bottom:1px solid #f1f5f9;font-weight:600">{{ $label }}</td>
                <td style="padding:10px 8px;border-bottom:1px solid #f1f5f9">{!! $display !!}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endforeach

    <div class="mt-16">
      <a class="btn btn-primary" href="{{ route('mystery.pdf',$evaluation) }}">Descargar PDF</a>
    </div>
  </div>
</div>
@endsection
