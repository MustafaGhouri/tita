{{-- resources/views/employees/form.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  :root{
    --primary:#522583; --muted:#6b7280; --border:#e5e7eb; --card:#fff;
    --shadow:0 1px 2px rgba(16,24,40,.04),0 1px 3px rgba(16,24,40,.06);
  }
  .ef-card{background:var(--card);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow);max-width:720px}
  .ef-pad{padding:20px}
  .ef-title{margin:0 0 12px;font-size:22px;font-weight:800}
  .ef-grid{display:grid;gap:16px}
  @media(min-width:768px){ .ef-grid{grid-template-columns:1fr 1fr} }

  .ef-label{display:block;font-weight:600;margin-bottom:6px}
  .ef-input,.ef-select{
    width:100%;border:1px solid var(--border);border-radius:10px;background:#fff;
    padding:10px 12px;font-size:14px;outline:none;transition:.15s
  }
  .ef-input:focus,.ef-select:focus{border-color:#c7b5e0;box-shadow:0 0 0 3px rgba(82,37,131,.12)}
  .ef-help{color:var(--muted);font-size:12px;margin-top:4px}

  .ef-actions{margin-top:16px;display:flex;gap:10px}
  .btn{display:inline-flex;align-items:center;gap:8px;border:0;border-radius:10px;padding:10px 16px;font-weight:700;cursor:pointer;text-decoration:none}
  .btn-primary{background:var(--primary);color:#fff}
  .btn-secondary{background:#eef2f7;color:#374151}
</style>

<div class="ef-card ef-pad">
  <h2 class="ef-title">{{ $employee->exists ? 'Edit Employee' : 'New Employee' }}</h2>

  <form method="POST"
        action="{{ $employee->exists ? route('employees.update',$employee) : route('employees.store') }}">
    @csrf
    @if($employee->exists) @method('PUT') @endif

    <div class="ef-grid">
      <div>
        <label class="ef-label">First name *</label>
        <input class="ef-input" name="first_name" value="{{ old('first_name',$employee->first_name) }}" required>
      </div>

      <div>
        <label class="ef-label">Last name *</label>
        <input class="ef-input" name="last_name" value="{{ old('last_name',$employee->last_name) }}" required>
      </div>

      <div>
        <label class="ef-label">Email</label>
        <input class="ef-input" name="email" type="email" value="{{ old('email',$employee->email) }}">
      </div>

      <div>
        <label class="ef-label">Position</label>
        <input class="ef-input" name="position" value="{{ old('position',$employee->position) }}">
      </div>

      <div>
        <label class="ef-label">Status</label>
        <select class="ef-select" name="status">
          @foreach(['ACTIVE','INACTIVE'] as $s)
            <option value="{{ $s }}" @selected(old('status',$employee->status)===$s)>{{ $s }}</option>
          @endforeach
        </select>
        <div class="ef-help">Choose “INACTIVE” for former employees.</div>
      </div>
    </div>

    <div class="ef-actions">
      <button class="btn btn-primary" type="submit">
        {{ $employee->exists ? 'Actualizar' : 'Crear' }}
      </button>
      <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
