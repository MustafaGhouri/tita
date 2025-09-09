{{-- resources/views/coaching/form.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  :root{
    --primary:#522583; --accent:#07d421; --muted:#6b7280; --border:#e5e7eb; --card:#fff;
    --shadow:0 1px 2px rgba(16,24,40,.04),0 1px 3px rgba(16,24,40,.06);
  }
  .cf-card{background:var(--card);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow);max-width:860px}
  .cf-pad{padding:20px}
  .cf-title{font-size:22px;font-weight:800;margin:0 0 14px}
  .cf-grid{display:grid;gap:16px}
  @media(min-width:768px){.cf-grid{grid-template-columns:1fr 1fr}}

  .cf-label{font-weight:600;font-size:14px;margin-bottom:6px;display:block}
  .cf-input,.cf-textarea{
    width:100%;border:1px solid var(--border);border-radius:10px;background:#fff;outline:none;
    padding:10px 12px;font-size:14px;transition:.15s
  }
  .cf-input:focus,.cf-textarea:focus{border-color:#c7b5e0;box-shadow:0 0 0 3px rgba(82,37,131,.12)}
  .cf-textarea{min-height:110px;resize:vertical}

  .cf-file{display:block;width:100%;border:1px dashed #cbd5e1;border-radius:12px;padding:16px;background:#fafcff;font-size:14px}
  .cf-file:hover{background:#f5f7ff}

  .cf-actions{display:flex;gap:12px;margin-top:20px}
  .btn{display:inline-flex;align-items:center;gap:8px;border:0;border-radius:10px;padding:10px 16px;font-weight:700;cursor:pointer;text-decoration:none}
  .btn-primary{background:var(--primary);color:#fff}
  .btn-secondary{background:#eef2f7;color:#374151}
</style>

<div class="cf-card cf-pad">
  <h2 class="cf-title">{{ $session->exists ? 'Edit Coaching Session' : 'New Coaching Session' }}</h2>

  <form method="POST" enctype="multipart/form-data"
        action="{{ $session->exists? route('coaching.update',[$employee,$session]) : route('coaching.store',$employee) }}">
    @csrf
    @if($session->exists) @method('PUT') @endif

    <div class="cf-grid">
      <div>
        <label class="cf-label">Date</label>
        <input type="date" name="date" value="{{ old('date',optional($session->date)->format('Y-m-d')) }}" class="cf-input" required>
      </div>
      <div>
        <label class="cf-label">Follow-up date</label>
        <input type="date" name="follow_up_date" value="{{ old('follow_up_date',optional($session->follow_up_date)->format('Y-m-d')) }}" class="cf-input">
      </div>
    </div>

    <div class="mt-3">
      <label class="cf-label">Summary</label>
      <textarea name="observations" class="cf-textarea">{{ old('observations', is_array($session->observations)? json_encode($session->observations):$session->observations) }}</textarea>
    </div>

    <div class="mt-3">
      <label class="cf-label">Recommendations</label>
      <textarea name="recommendations" class="cf-textarea">{{ old('recommendations', is_array($session->recommendations)? json_encode($session->recommendations):$session->recommendations) }}</textarea>
    </div>

    <div class="mt-3">
      <label class="cf-label">Attachments</label>
      <input type="file" name="attachments[]" multiple class="cf-file">
      <div class="muted" style="font-size:12px;margin-top:4px">You can upload multiple files (PDF, images, docs)</div>
    </div>

    <div class="cf-actions">
      <button class="btn btn-primary" type="submit">Save Session</button>
      <a href="{{ route('coaching.index',$employee) }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
