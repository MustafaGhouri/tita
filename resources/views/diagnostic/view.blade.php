{{-- resources/views/diagnostic/view.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  :root{
    --primary:#522583; --muted:#6b7280; --border:#e5e7eb; --card:#fff;
    --shadow:0 1px 2px rgba(16,24,40,.04),0 1px 3px rgba(16,24,40,.06);
  }
  .dv-card{background:var(--card);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow);max-width:860px}
  .dv-pad{padding:18px}

  .dv-head{display:flex;align-items:flex-start;justify-content:space-between;gap:14px}
  .dv-name{margin:0;font-size:22px;font-weight:800}
  .dv-meta{margin-top:4px;color:var(--muted);font-size:13px}
  .btn{display:inline-flex;align-items:center;gap:8px;border:0;border-radius:10px;padding:10px 14px;font-weight:700;cursor:pointer;text-decoration:none}
  .btn-outline{background:#f8fafc;border:1px solid var(--border);color:var(--primary)}
  .btn-outline:hover{background:#f3f4f6}

  .score-badge{display:inline-flex;align-items:center;gap:8px;border-radius:999px;padding:6px 12px;font-weight:800}
  .score-green{background:#dcfce7;color:#166534}
  .score-yellow{background:#fef9c3;color:#b45309}
  .score-red{background:#fee2e2;color:#991b1b}

  .table-wrap{overflow:auto;margin-top:14px}
  table.dv{width:100%;border-collapse:separate;border-spacing:0}
  table.dv tr:nth-child(odd) td{background:#fcfdff}
  table.dv td{
    padding:12px 14px;border-bottom:1px solid #f1f5f9;font-size:14px;vertical-align:top
  }
  table.dv td:first-child{width:42%;font-weight:700}
</style>

@php
  $score = (int)($result->score ?? 0);
  $scoreCls = $score >= 80 ? 'score-green' : ($score >= 60 ? 'score-yellow' : 'score-red');
  $template = app(\App\Models\TestTemplate::class)->where('is_active', true)->first();
@endphp

<div class="dv-card dv-pad">
  <div class="dv-head">
    <div>
      <h2 class="dv-name">{{ $employee->name }}</h2>
      <div class="dv-meta">
        Enviado: {{ optional($result->submitted_at)->format('M d, Y • H:i') }}
      </div>
      <div style="margin-top:10px">
        <span class="score-badge {{ $scoreCls }}">{{ $score }}/100</span>
      </div>
    </div>

    <a class="btn btn-outline" href="{{ route('diagnostic.pdf',$employee) }}">
      Descargar PDF
    </a>
  </div>

  <div class="table-wrap">
    <table class="dv">
      <tbody>
        @foreach(($template->schema['items'] ?? []) as $i)
          <tr>
            <td>{{ $i['label'] }}</td>
            <td>{{ $result->answers[$i['key']] ?? '—' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
