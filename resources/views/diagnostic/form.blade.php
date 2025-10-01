{{-- resources/views/diagnostic/form.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  :root{
    --primary:#522583; --muted:#6b7280; --border:#e5e7eb; --card:#fff;
    --shadow:0 1px 2px rgba(16,24,40,.04),0 1px 3px rgba(16,24,40,.06);
  }
  /* Header */
  .df-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
  .df-title{margin:0;font-size:22px;font-weight:800}
  .df-sub{margin:2px 0 0;color:var(--muted);font-size:13px}

  /* Card + fields */
  .df-card{background:var(--card);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow);max-width:860px}
  .df-pad{padding:18px}
  .df-group{display:grid;gap:14px}
  .df-item{padding:14px 16px;border:1px solid #eef2f7;background:#fff;border-radius:12px}
  .df-label{font-weight:700;margin:0 0 8px}

  .df-input,.df-select,.df-text{
    width:100%;border:1px solid var(--border);border-radius:10px;background:#fff;
    padding:10px 12px;font-size:14px;outline:none;transition:.15s
  }
  .df-input:focus,.df-select:focus,.df-text:focus{border-color:#c7b5e0;box-shadow:0 0 0 3px rgba(82,37,131,.12)}

  /* Range */
  .df-range-wrap{display:flex;align-items:center;gap:10px}
  .df-range{appearance:none;width:100%;height:6px;border-radius:999px;background:#e5e7eb;outline:none}
  .df-range::-webkit-slider-thumb{
    -webkit-appearance:none;appearance:none;width:18px;height:18px;border-radius:50%;
    background:var(--primary);cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,.25)
  }
  .df-range::-moz-range-thumb{
    width:18px;height:18px;border:0;border-radius:50%;background:var(--primary);cursor:pointer
  }
  .df-out{display:inline-block;min-width:42px;text-align:center;padding:6px 8px;border-radius:8px;font-weight:700;background:#efe6fb;color:#3b0ca3}

  /* Actions */
  .df-actions{margin-top:16px;display:flex;gap:10px}
  .btn{display:inline-flex;align-items:center;gap:8px;border:0;border-radius:10px;padding:10px 16px;font-weight:700;cursor:pointer;text-decoration:none}
  .btn-primary{background:var(--primary);color:#fff}
  .btn-secondary{background:#eef2f7;color:#374151}
</style>

<div class="df-head">
  <div>
    <h2 class="df-title">Diagnóstico — {{ $employee->name }}</h2>
    <p class="df-sub">Por favor responda todas las preguntas a continuación</p>
  </div>
</div>

<form method="POST" action="{{ route('diagnostic.submit',$employee) }}" class="df-card df-pad">
  @csrf

  <div class="df-group">
    @foreach($template->schema['items'] as $i)
      <div class="df-item">
        <label class="df-label">{{ $i['label'] }}</label>

        @if($i['type']==='mcq')
          <select name="answers[{{ $i['key'] }}]" class="df-select">
            @foreach($i['options'] as $idx=>$opt)
              <option value="{{ $idx }}">{{ $opt }}</option>
            @endforeach
          </select>

        @elseif($i['type']==='boolean')
          <select name="answers[{{ $i['key'] }}]" class="df-select">
            <option value="1">Verdadero</option>
            <option value="0">Falso</option>
          </select>

        @elseif($i['type']==='scale')
          @php $min = $i['min'] ?? 1; $max = $i['max'] ?? 5; @endphp
          <div class="df-range-wrap">
            <input class="df-range" type="range" min="{{ $min }}" max="{{ $max }}" value="{{ $min }}"
                   name="answers[{{ $i['key'] }}]" oninput="this.nextElementSibling.textContent=this.value">
            <span class="df-out">{{ $min }}</span>
          </div>

        @elseif($i['type']==='short_text')
          <input type="text" name="answers[{{ $i['key'] }}]" class="df-input" placeholder="Escribe tu respuesta...">
        @endif
      </div>
    @endforeach

    <div class="df-item">
      <label class="df-label">Puntuación manual (opcional)</label>
      <input type="number" name="manual_score" step="0.01" class="df-input" placeholder="e.g., 84.5">
      <div style="color:var(--muted);font-size:12px;margin-top:6px">Si se deja en blanco, la puntuación se calculará automáticamente.</div>
    </div>
  </div>

  <div class="df-actions">
    <button class="btn btn-primary" type="submit">Entregar</button>
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
  </div>
</form>
@endsection
