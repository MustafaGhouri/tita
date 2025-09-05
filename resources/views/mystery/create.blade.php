{{-- resources/views/mystery/create.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  :root{
    --primary:#522583; --muted:#6b7280; --border:#e5e7eb; --card:#fff;
    --shadow:0 1px 2px rgba(16,24,40,.04),0 1px 3px rgba(16,24,40,.06);
    --bg:#f8fafc;
  }
  /* Header */
  .mc-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
  .mc-title{margin:0;font-size:22px;font-weight:800}
  .mc-sub{margin:2px 0 0;color:var(--muted);font-size:13px}
  .mc-back{color:var(--primary);font-weight:600;text-decoration:none}

  /* Card + form */
  .mc-card{background:var(--card);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow);max-width:860px}
  .mc-pad{padding:18px}
  .mc-group{display:grid;gap:16px}
  .mc-item{padding:14px 16px;border:1px solid #eef2f7;background:#fff;border-radius:12px}
  .mc-label{font-weight:700;margin:0 0 8px}
  .mc-help{color:var(--muted);font-size:12px;margin-top:6px}

  /* Controls */
  .mc-select,.mc-input,.mc-textarea{
    width:100%;border:1px solid var(--border);border-radius:10px;background:#fff;outline:none;
    padding:10px 12px;font-size:14px;transition:.15s
  }
  .mc-select:focus,.mc-input:focus,.mc-textarea:focus{box-shadow:0 0 0 3px rgba(82,37,131,.12);border-color:#c7b5e0}
  .mc-textarea{min-height:110px;resize:vertical;background:#fff}

  /* Range + output bubble */
  .mc-range-wrap{display:flex;align-items:center;gap:10px}
  .mc-range{appearance:none;width:100%;height:6px;border-radius:999px;background:#e5e7eb;outline:none}
  .mc-range::-webkit-slider-thumb{
    -webkit-appearance:none;appearance:none;width:18px;height:18px;border-radius:50%;
    background:var(--primary);cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,.25)
  }
  .mc-range::-moz-range-thumb{
    width:18px;height:18px;border:0;border-radius:50%;background:var(--primary);cursor:pointer
  }
  .mc-out{
    display:inline-block;min-width:42px;text-align:center;padding:6px 8px;border-radius:8px;
    font-weight:700;background:#efe6fb;color:#3b0ca3
  }

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
    <div class="alert alert-success">
        {{ session('ok') }}
    </div>
@endif

{{-- Global validation errors list --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>There were some problems with your input:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $msg)
                <li>{{ $msg }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mc-head">
  <div>
    <h2 class="mc-title">New Evaluation — {{ $employee->name }} ({{ $month }})</h2>
    <p class="mc-sub">Fill the checklist and upload evidence video.</p>
  </div>
  <a class="mc-back" href="{{ url()->previous() }}">← Back</a>
</div>

<form method="POST" action="{{ route('mystery.store',$employee) }}" enctype="multipart/form-data" class="mc-card mc-pad">
  @csrf
  <input type="hidden" name="checklist_id" value="{{ $checklist->id }}">
  <input type="hidden" name="monthKey" value="{{ $month }}">

  <div class="mc-group">
    @foreach($checklist->schema['items'] as $item)
      <div class="mc-item">
        <label class="mc-label">{{ $item['label'] }}</label>

        @if($item['type']==='yes_no')
          <select name="answers[{{ $item['key'] }}]" class="mc-select">
            <option value="yes">Yes</option>
            <option value="no">No</option>
          </select>

        @elseif($item['type']==='scale')
          @php $min = $item['min'] ?? 1; $max = $item['max'] ?? 5; @endphp
          <div class="mc-range-wrap">
            <input class="mc-range" type="range" min="{{ $min }}" max="{{ $max }}" value="{{ $min }}"
                   name="answers[{{ $item['key'] }}]" oninput="this.nextElementSibling.textContent=this.value">
            <span class="mc-out">{{ $min }}</span>
          </div>

        @elseif($item['type']==='note')
          <textarea name="answers[{{ $item['key'] }}]" class="mc-textarea" placeholder="Write notes..."></textarea>
        @endif

        <div class="mc-help">Weight: {{ $item['weight'] ?? 1 }}</div>
      </div>
    @endforeach

    <div class="mc-item">
      <label class="mc-label">Upload Video (MP4/AVI/MOV)</label>
      <input class="mc-file" type="file" name="video" required accept="video/mp4,video/avi,video/quicktime">
      <div class="mc-help">Max size depends on server limits.</div>
    </div>
  </div>

  <div class="mc-actions">
    <button class="btn btn-primary" type="submit">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M13 11h4v2h-4v4h-2v-4H7v-2h4V7h2v4Z"/></svg>
      Save Evaluation
    </button>
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
  </div>
</form>
@endsection
