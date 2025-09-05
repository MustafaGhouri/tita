{{-- resources/views/mystery/show.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  :root{
    --primary:#522583; --muted:#6b7280; --border:#e5e7eb; --bg:#f6f7fb;
    --card:#fff; --shadow:0 1px 2px rgba(16,24,40,.04),0 1px 3px rgba(16,24,40,.06);
  }
  /* page title row */
  .ms-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
  .ms-title{margin:0;font-size:22px;font-weight:800}
  .ms-chip{background:#efe6fb;color:#3b0ca3;padding:6px 10px;border-radius:999px;font-weight:700;font-size:12px;margin-left:10px}
  .ms-right{display:flex;align-items:center;gap:14px;color:#9ca3af}

  /* cards & layout */
  .card{background:var(--card);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow)}
  .card-pad{padding:16px}
  .card-head{padding:14px 16px;border-bottom:1px solid var(--border)}
  .card-title{margin:0;font-weight:700}
  .card-sub{margin:4px 0 0;color:var(--muted);font-size:13px}

  /* profile strip */
  .profile{display:flex;align-items:center;gap:14px}
  .avatar{width:44px;height:44px;border-radius:999px;overflow:hidden;background:#e5e7eb;display:grid;place-items:center;font-weight:800;color:#374151}
  .avatar img{width:100%;height:100%;object-fit:cover}
  .prof-name{font-weight:700}
  .prof-meta{color:var(--muted);font-size:12px}
  .badge{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:4px 10px;font-size:12px;font-weight:700}
  .badge-active{background:#dcfce7;color:#166534}
  .dot{width:8px;height:8px;border-radius:999px;background:#16a34a}

  /* action button */
  .btn{display:inline-flex;align-items:center;gap:8px;border:0;border-radius:10px;padding:10px 14px;font-weight:700;cursor:pointer}
  .btn-primary{background:var(--primary);color:#fff}
  .btn-primary svg{fill:#fff}

  /* table */
  .table-wrap{overflow:auto}
  table.ms{width:100%;border-collapse:separate;border-spacing:0}
  table.ms thead th{
    text-align:left;font-size:12px;color:#6b7280;font-weight:700;letter-spacing:.02em;
    padding:12px 16px;border-bottom:1px solid #eef2f7;background:#f8fafc
  }
  table.ms tbody td{padding:14px 16px;border-bottom:1px solid #f1f5f9;font-size:14px;color:#111827}
  table.ms tbody tr:last-child td{border-bottom:0}

  .score{display:inline-block;border-radius:999px;padding:4px 10px;font-size:12px;font-weight:700;color:#111}
  .score.green{background:#dcfce7;color:#166534}
  .score.yellow{background:#fef9c3;color:#b45309}
  .score.red{background:#fee2e2;color:#991b1b}

  .link{color:var(--primary);font-weight:600}
  .mt-16{margin-top:16px}
  .mt-24{margin-top:24px}
</style>

@php
  $emp = $evaluation->employee;
  $initial = strtoupper(mb_substr($emp->name ?? 'U',0,1));
  // Archive list (agar relation ho to use karein; warna current eval ko list me dikha dein)
  $archive = $emp->mysteryEvaluations ?? collect([$evaluation]);
@endphp

{{-- Page heading --}}
<div class="ms-head">
  <div style="display:flex;align-items:center">
    <h1 class="ms-title">Mystery Shopper Module</h1>
    <span class="ms-chip">Employee Profile</span>
  </div>
  <div class="ms-right">
    {{-- yahan top right icons ka placeholder (optional) --}}
  </div>
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
        <span class="dot"></span>{{ $isActive ? 'Active' : 'Inactive' }}
      </span>
    </div>
  </div>
</div>

{{-- Section header + button --}}
<!-- <div class="card" style="margin-bottom:16px;">
  <div class="card-head">
    <h3 class="card-title">Mystery Shopper Evaluations</h3>
    <p class="card-sub">Manage and track mystery shopper evaluations for this employee</p>
  </div>
  <div class="card-pad" style="display: none;">
    <a href="{{ route('mystery.create',$emp->id) }}" class="btn btn-primary">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M13 11h4v2h-4v4h-2v-4H7v-2h4V7h2v4Z"/></svg>
      New Evaluation
    </a>
  </div>
</div> -->

{{-- Evaluation Archive table --}}
<div class="card">
  <div class="card-head">
    <h3 class="card-title">Evaluation Archive</h3>
    <p class="card-sub">Manage and track mystery shopper evaluations for this employee</p>
  </div>

  <div class="table-wrap">
    <table class="ms">
      <thead>
        <tr>
          <th style="width:22%">Month</th>
          <th style="width:14%">Score</th>
          <!-- <th style="width:24%">Evaluator</th> -->
          <th style="width:20%">Video</th>
          <th style="width:20%">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($archive as $ev)
          @php
            $score = (int)($ev->score ?? 0);
            $scoreClass = $score >= 80 ? 'green' : ($score >= 60 ? 'yellow' : 'red');
            $monthLabel = $ev->monthKey ?? \Illuminate\Support\Carbon::parse($ev->created_at)->isoFormat('MMMM YYYY');
            $videoUrl = !empty($ev->video_path) ? asset('public/'.$ev->video_path) : null;
          @endphp
          <tr>
            <td>{{ $monthLabel }}</td>
            <td><span class="score {{ $scoreClass }}">{{ $score }}/100</span></td>
            <!-- <td>{{ $ev->evaluator->name ?? '—' }}</td> -->
            <td>
              @if($videoUrl)
                <a class="link" href="{{ $videoUrl }}" target="_blank">View Video</a>
              @else
                —
              @endif
            </td>
            <td>
<!--               <a class="link" href="{{ route('mystery.show',$ev) }}">View Details</a>
              @if($ev->pdf_path ?? false)
                &nbsp;•&nbsp;<a class="link" href="{{ asset('storage/'.$ev->pdf_path) }}" target="_blank">PDF</a>
              @else
              @endif -->
                &nbsp;•&nbsp;<a class="link" href="{{ route('mystery.pdf',$ev) }}">Download PDF</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="color:#6b7280;padding:18px">No evaluations yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Current evaluation details (video + answers) --}}
<div class="card mt-24">
  <div class="card-head">
    <h3 class="card-title">Current Evaluation</h3>
    <p class="card-sub">Month: {{ $evaluation->monthKey }} • Score: {{ $evaluation->score }}/100</p>
  </div>

  <div class="card-pad">
    @php $videoUrl = !empty($evaluation->video_path) ? asset('public/'.$evaluation->video_path) : null; @endphp
    @if($videoUrl)
      <video controls class="w-100" style="width:100%;border:1px solid var(--border);border-radius:12px">
        <source src="{{ $videoUrl }}" type="video/mp4">
        <a href="{{ $videoUrl }}" class="link">View Video</a>
      </video>
    @else
      <div style="color:#6b7280">No video attached.</div>
    @endif

    <div class="mt-16">
      <div class="card-title" style="font-size:16px">Answers</div>
      <table class="ms" style="margin-top:8px">
        <tbody>
          @foreach($evaluation->checklist->schema['items'] as $i)
            <tr>
              <td style="width:45%;padding:10px 8px;border-bottom:1px solid #f1f5f9;font-weight:600">{{ $i['label'] }}</td>
              <td style="padding:10px 8px;border-bottom:1px solid #f1f5f9">{{ $evaluation->answers[$i['key']] ?? '—' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-16">
      <a class="btn btn-primary" href="{{ route('mystery.pdf',$evaluation) }}">Download PDF</a>
    </div>
  </div>
</div>
@endsection
