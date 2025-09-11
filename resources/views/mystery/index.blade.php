@extends('layouts.app')

@section('content')
<style>
  :root{ --primary:#522583; --muted:#6b7280; --border:#e5e7eb; --card:#fff; --shadow:0 1px 2px rgba(16,24,40,.04),0 1px 3px rgba(16,24,40,.06) }
  .head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
  .title{margin:0;font-size:22px;font-weight:800}
  .search{display:flex;gap:8px}
  .input{border:1px solid var(--border);border-radius:10px;padding:10px 12px;outline:none}
  .btn{border:0;background:var(--primary);color:#fff;border-radius:10px;padding:10px 14px;font-weight:700}

  .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
  @media (max-width:900px){ .grid{grid-template-columns:repeat(2,1fr)} }
  @media (max-width:640px){ .grid{grid-template-columns:1fr} }

  .card{
    position:relative;background:#fff;border:1px solid var(--border);
    border-radius:12px;box-shadow:var(--shadow);padding:14px;
    transition:transform .08s ease, box-shadow .15s ease, border-color .15s ease, background .15s ease
  }
  .card:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(0,0,0,.06)}
  .card.is-active{border-color:#c7b5e0;background:linear-gradient(0deg,#fbfaff,#ffffff)}
  .card.is-active::before{
    /* purple left accent like a professional ribbon */
    content:"";position:absolute;left:-1px;top:-1px;bottom:-1px;width:6px;border-radius:12px 0 0 12px;
    background:linear-gradient(180deg,#6d28d9,#a78bfa);
  }

  .name{font-weight:800}
  .muted{color:var(--muted);font-size:12px}

  /* status badges */
  .badge{
    display:inline-flex;align-items:center;gap:6px;border-radius:999px;
    padding:4px 10px;font-size:12px;font-weight:700;border:1px solid transparent
  }
  .badge-active{background:#dcfce7;color:#166534;border-color:#bbf7d0}
  .badge-inactive{background:#fee2e2;color:#991b1b;border-color:#fecaca}
  .dot{width:8px;height:8px;border-radius:999px;background:#16a34a}
  .badge-inactive .dot{background:#991b1b}

  /* “has mystery shopper” pill */
  .pill{
    display:inline-flex;align-items:center;gap:6px;
    background:#522583;color:#fff;padding:4px 10px;border-radius:999px;
    font-size:12px;font-weight:700
  }
  .pill .dot{background:#fff;width:6px;height:6px}
</style>

<div class="head">
<!--   <h1 class="title">Mystery Shopper — Employees</h1>
  <form method="GET" class="search">
    <input class="input" type="text" name="q" value="{{ $q }}" placeholder="Search employee...">
    <button class="btn" type="submit">Search</button>
  </form> -->
</div>

<div class="grid">
  @forelse($employees as $e)
    @php
      // Has at least one mystery evaluation?
      $hasEval = property_exists($e,'evaluations_count')
        ? ($e->evaluations_count > 0)
        : (method_exists($e,'evaluations') ? $e->evaluations()->exists() : false);

      $isActive = strtoupper($e->status ?? '') === 'ACTIVE';
    @endphp

    <a class="card {{ $hasEval ? 'is-active' : '' }}" href="{{ route('mystery.employee', $e) }}">
      {{-- top row: name + pill when has evals --}}
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
        <div class="name">{{ $e->name }}</div>
        @if($hasEval)
          <span class="pill"><span class="dot"></span> Mystery shopper added</span>
        @endif
      </div>

      {{-- status row --}}
      <div class="muted" style="display:flex;align-items:center;gap:8px;margin-top:2px">
        Status:
        <span class="badge {{ $isActive ? 'badge-active' : 'badge-inactive' }}">
          <span class="dot"></span>{{ $isActive ? 'Active' : 'Inactive' }}
        </span>
      </div>

      {{-- role --}}
      <div class="muted" style="margin-top:6px">Role: {{ $e->position ?? '—' }}</div>
    </a>
  @empty
    <div class="card">No employees found.</div>
  @endforelse
</div>

<div style="margin-top:12px">
  {{ $employees->withQueryString()->links() }}
</div>
@endsection
