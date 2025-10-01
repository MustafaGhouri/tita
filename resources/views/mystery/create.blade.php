{{-- resources/views/mystery/create.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  :root{
    --primary:#522583; --muted:#6b7280; --border:#e5e7eb; --card:#fff;
    --shadow:0 1px 2px rgba(16,24,40,.04),0 1px 3px rgba(16,24,40,.06);
    --bg:#f8fafc; --row:#eef3fb;
  }
  body{background:var(--bg)}
  .mc-wrap{max-width:980px;margin:0 auto}

  /* Header */
  .mc-head{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px}
  .mc-title{margin:0;font-size:22px;font-weight:800}
  .mc-sub{margin:6px 0 0;color:var(--muted);font-size:13px}
  .mc-back{color:var(--primary);font-weight:600;text-decoration:none}
  .mc-score{margin-top:6px;font-size:13px;background:#efe6fb;color:#3b0ca3;border-radius:10px;padding:6px 10px;display:inline-flex;gap:10px;align-items:center}
  .mc-bar{height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden}
  .mc-bar>span{display:block;height:100%;background:var(--primary);width:0%}

  /* Card + form */
  .mc-card{background:var(--card);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow)}
  .mc-pad{padding:18px}
  .mc-group{display:grid;gap:16px}
  .mc-item{padding:14px 16px;border:1px solid #eef2f7;background:#fff;border-radius:12px}
  .mc-item-hd{display:flex;align-items:center;justify-content:space-between;gap:12px}
  .mc-label{font-weight:800;margin:0}
  .mc-help{color:var(--muted);font-size:12px;margin-top:6px}

  /* Section */
  .mc-sec{margin-top:10px}
  .mc-sec h3{margin:0 0 8px;font-size:18px;font-weight:900}
  .mc-sec>p{margin:0 0 12px;font-size:13px;color:var(--muted)}

  /* Inputs */
  .mc-select,.mc-input,.mc-textarea{
    width:100%;border:1px solid var(--border);border-radius:10px;background:#fff;outline:none;
    padding:10px 12px;font-size:14px;transition:.15s
  }
  .mc-select:focus,.mc-input:focus,.mc-textarea:focus{box-shadow:0 0 0 3px rgba(82,37,131,.12);border-color:#c7b5e0}
  .mc-textarea{min-height:110px;resize:vertical;background:#fff}

  /* Matrix table (JotForm-like) */
  .mx{width:100%;border-collapse:separate;border-spacing:0;border:1px solid #d7dbe3;border-radius:12px;overflow:hidden}
  .mx thead th{font-size:14px;color:#334155;background:#eef2f7;border-bottom:1px solid #d7dbe3;padding:10px 12px}
  .mx thead th.mx-blank{background:transparent;border-right:1px solid #d7dbe3}
  .mx tbody td{border-bottom:1px solid #e9edf5;border-right:1px solid #e9edf5;padding:12px}
  .mx tbody tr:last-child td{border-bottom:0}
  .mx tbody td:first-child{background:var(--row);font-weight:600}
  .mx td.mx-center{width:90px;text-align:center;vertical-align:middle}
  .mx input[type=radio]{width:18px;height:18px;accent-color:#522583;cursor:pointer}

  /* File */
  .mc-file{display:block;width:100%;border:1px dashed #cbd5e1;border-radius:12px;padding:16px;background:#fafcff}
  .mc-file:hover{background:#f5f7ff}

  /* Actions */
  .mc-actions{display:flex;gap:10px;align-items:center;margin-top:16px}
  .btn{display:inline-flex;align-items:center;gap:8px;border:0;border-radius:10px;padding:10px 16px;font-weight:700;cursor:pointer}
  .btn-primary{background:var(--primary);color:#fff}
  .btn-secondary{background:#eef2f7;color:#111827}
  .btn svg{fill:currentColor}
</style>

{{-- Success flash --}}
@if (session('ok'))
  <div class="alert alert-success">{{ session('ok') }}</div>
@endif

{{-- Global validation errors list --}}
@if ($errors->any())
  <div class="alert alert-danger">
    <strong>Hubo algunos problemas con tu entrada:</strong>
    <ul class="mb-0">
      @foreach ($errors->all() as $msg)
        <li>{{ $msg }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="mc-wrap">
  <div class="mc-head">
    <div>
      <h2 class="mc-title">Nueva evaluación — {{ $employee->name }} ({{ $month }})</h2>
      <p class="mc-sub">Complete la lista de verificación y cargue el video de evidencia.</p>
      <div class="mc-score">
        <span><strong>Puntaje:</strong> <span id="mcScore">0</span> / <span id="mcMax">0</span></span>
        <span>|</span>
        <span><strong>Por ciento:</strong> <span id="mcPct">0%</span></span>
      </div>
    </div>
    <a class="mc-back" href="{{ url()->previous() }}">← Atrás</a>
  </div>

  <form method="POST" action="{{ route('mystery.store',$employee) }}" enctype="multipart/form-data" class="mc-card mc-pad" id="mcForm">
    @csrf
    <input type="hidden" name="checklist_id" value="{{ $checklist->id }}">
    <input type="hidden" name="monthKey" value="{{ $month }}">

    @php
      $schema = $checklist->schema;
      $sections = [];
      if (isset($schema['sections']) && is_array($schema['sections'])) {
        $sections = $schema['sections'];
      } elseif (isset($schema['items']) && is_array($schema['items'])) {
        $sections = [[ 'title' => null, 'desc' => null, 'items' => $schema['items'] ]];
      }
      $yn   = $schema['yes_no_labels'] ?? ['yes'=>'Yes','no'=>'No'];
      $ynna = $schema['yes_no_na_labels'] ?? ['yes'=>'Yes','no'=>'No','na'=>'N/A'];

      // Helper to flush a matrix group
      function render_matrix($groupType, $rows) {
        // Header labels per screenshot
        $hdr = $groupType === 'yes_no' ? ['no'=>'No','yes'=>'Yeah'] : ['no'=>'No','yes'=>'Yeah','na'=>'N/A'];
        echo '<table class="mx"><thead><tr>';
        echo '<th class="mx-blank"></th>';
        foreach($hdr as $lab){ echo '<th class="mx-center">'.e($lab).'</th>'; }
        echo '</tr></thead><tbody>';
        foreach($rows as $r){
          $key = $r['key']; $label = $r['label']; $w = (int)($r['weight'] ?? 0);
          echo '<tr class="mx-row" data-type="'.$groupType.'" data-weight="'.$w.'">';
          echo '<td>'.e($label).($w>0 ? ' ('.$w.' pts)' : '').'</td>';
          // columns
          foreach($hdr as $val => $lab){
            // Only render options allowed for the item type
            if($groupType==='yes_no' && $val==='na'){ echo '<td class="mx-center">—</td>'; continue; }
            $id = 'fld-'.$key.'-'.$val;
            echo '<td class="mx-center"><input id="'.$id.'" type="radio" name="answers['.$key.']" value="'.$val.'"></td>';
          }
          echo '</tr>';
        }
        echo '</tbody></table>';
      }
    @endphp

    <div class="mc-group">
      @foreach($sections as $sec)
        <div class="mc-sec">
          @if(!empty($sec['title'])) <h3>{{ $sec['title'] }}</h3> @endif
          @if(!empty($sec['desc']))  <p>{{ $sec['desc'] }}</p> @endif

          @php
            // Group contiguous radio items into matrix blocks
            $pendingType = null; $pending = [];
            $flush = function() use (&$pendingType,&$pending){
              if($pendingType && count($pending)){
                render_matrix($pendingType, $pending);
              }
              $pendingType = null; $pending = [];
            };
          @endphp

          @foreach($sec['items'] as $item)
            @php
              $type = $item['type'] ?? 'text';
              $key  = $item['key'] ?? \Illuminate\Support\Str::slug(($item['label'] ?? 'field').'-'.uniqid());
              $label= $item['label'] ?? \Illuminate\Support\Str::headline($key);
              $help = $item['help']  ?? null;
              $req  = (bool)($item['required'] ?? false);
              $min  = $item['min'] ?? 1;  $max = $item['max'] ?? 5; $step = $item['step'] ?? 1;
              $opts = $item['options'] ?? [];
              $def  = $item['default'] ?? null;
              $weight = (int)($item['weight'] ?? 0);
            @endphp

            @if(in_array($type,['yes_no','yes_no_na']))
              @php
                if($pendingType===null || $pendingType===$type){
                  $pendingType = $type; $pending[] = compact('key','label','weight');
                } else {
                  $flush(); $pendingType = $type; $pending = [compact('key','label','weight')];
                }
              @endphp
            @else
              @php $flush(); @endphp

              {{-- Non-radio items (date/text/number/select/note) --}}
              <div class="mc-item" data-weight="{{ $weight }}" data-type="{{ $type }}">
                <div class="mc-item-hd">
                  <label class="mc-label" for="fld-{{ $key }}">
                    {{ $label }} @if($req)<span title="required" style="color:#ef4444">*</span>@endif
                  </label>
                </div>

                @if($type === 'date')
                  <input id="fld-{{ $key }}" class="mc-input" type="date" name="answers[{{ $key }}]" value="{{ $def }}" {{ $req?'required':'' }}>

                @elseif($type === 'number')
                  <input id="fld-{{ $key }}" class="mc-input" type="number" name="answers[{{ $key }}]" value="{{ $def }}" {{ $req?'required':'' }}>

                @elseif($type === 'text')
                  <input id="fld-{{ $key }}" class="mc-input" type="text" name="answers[{{ $key }}]" value="{{ $def }}" {{ $req?'required':'' }}>

                @elseif($type === 'select')
                  <select id="fld-{{ $key }}" class="mc-select" name="answers[{{ $key }}]" {{ $req?'required':'' }}>
                    @foreach($opts as $ov => $ot)
                      @php $value = is_string($ov) ? $ov : $ot; @endphp
                      <option value="{{ $value }}" @selected(($def ?? '')==$value)>{{ $ot }}</option>
                    @endforeach
                  </select>

                @elseif($type === 'note' || $type === 'textarea')
                  <textarea id="fld-{{ $key }}" class="mc-textarea" name="answers[{{ $key }}]" placeholder="Write here...">{{ $def }}</textarea>
                @endif

                @if($help)<div class="mc-help">{{ $help }}</div>@endif
              </div>
            @endif
          @endforeach

          @php $flush(); @endphp
        </div>
      @endforeach

      {{-- Evidence video (keep as before) --}}
      <div class="mc-item">
        <div class="mc-item-hd">
          <label class="mc-label">Subir vídeo (MP4/AVI/MOV)</label>
        </div>
        <input class="mc-file" type="file" name="video" required accept="video/mp4,video/avi,video/quicktime,video/x-msvideo,video/x-matroska">
        <div class="mc-help">El tamaño máximo depende de los límites del servidor.</div>
      </div>
    </div>

    <div class="mc-actions">
      <button class="btn btn-primary" type="submit">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M13 11h4v2h-4v4h-2v-4H7v-2h4V7h2v4Z"/></svg>
        Guardar evaluación
      </button>
      <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

<script>
(function(){
  const form = document.getElementById('mcForm');
  const scoreEl = document.getElementById('mcScore');
  const maxEl = document.getElementById('mcMax');
  const pctEl = document.getElementById('mcPct');
  const bar = document.createElement('div');
  bar.className = 'mc-bar';
  const fill = document.createElement('span');
  bar.appendChild(fill);
  document.querySelector('.mc-score').appendChild(bar);

  function allScoreItems() {
    return form.querySelectorAll('.mc-item[data-weight], .mx-row[data-weight]');
  }

  function getMax(){
    let total = 0;
    allScoreItems().forEach(el=>{
      const w = parseFloat(el.dataset.weight||0);
      const type = el.dataset.type;
      if(!w) return;
      if(type==='yes_no' || type==='yes_no_na' || type==='range' || type==='scale'){ total += w; }
    });
    return total;
  }

  function getScore(){
    let sum = 0;
    allScoreItems().forEach(el=>{
      const w = parseFloat(el.dataset.weight||0);
      const type = el.dataset.type;
      if(!w) return;

      if(type==='yes_no' || type==='yes_no_na'){
        const ch = el.querySelector('input[type=radio]:checked');
        if(ch && ch.value==='yes') sum += w;
      } else if(type==='range' || type==='scale'){
        const inp = el.querySelector('input[type=range]');
        if(!inp) return;
        const min = parseFloat(inp.min||1), max = parseFloat(inp.max||5);
        const val = parseFloat(inp.value||min);
        if(max>min){ sum += w * ((val-min)/(max-min)); }
      }
    });
    return sum;
  }

  function updateScore(){
    const max = getMax();
    const got = getScore();
    const pct = max>0 ? Math.round((got/max)*100) : 0;
    scoreEl.textContent = Math.round(got);
    maxEl.textContent = Math.round(max);
    pctEl.textContent = pct + '%';
    document.querySelector('.mc-bar > span').style.width = pct + '%';
  }

  form.addEventListener('input', updateScore);
  updateScore();
})();
</script>
@endsection
