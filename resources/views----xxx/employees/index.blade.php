{{-- resources/views/employees/index.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  /* ---- Employees page styles (custom CSS, no Tailwind) ---- */
  .page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
  .page-title{margin:0;font-size:22px;font-weight:800}
  .btn{display:inline-flex;align-items:center;gap:8px;border:0;border-radius:10px;padding:10px 14px;font-weight:700;cursor:pointer}
  .btn-purple{background:#522583;color:#fff;box-shadow:0 1px 2px rgba(16,24,40,.05)}
  .btn-purple svg{fill:#fff}

  .card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 2px rgba(16,24,40,.04),0 1px 3px rgba(16,24,40,.06)}
  .card-head{display:flex;align-items:center;gap:14px;padding:16px}
  .search{position:relative;flex:1}
  .search input{
    width:100%;height:40px;border:1px solid #e5e7eb;border-radius:10px;
    padding:0 14px 0 38px;background:#f8fafc;font-size:14px;outline:none
  }
  .search svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);width:18px;height:18px;fill:#9ca3af}

  .toggle{display:flex;align-items:center;gap:10px;margin-left:auto}
  .toggle label{font-size:14px;color:#374151}
  .switch{position:relative;width:46px;height:24px}
  .switch input{opacity:0;width:0;height:0}
  .slider{
    position:absolute;inset:0;background:#e5e7eb;border-radius:999px;transition:.2s;
    box-shadow:inset 0 0 0 1px #d1d5db
  }
  .slider:before{
    content:"";position:absolute;height:18px;width:18px;left:3px;top:3px;background:#fff;border-radius:50%;
    transition:.2s;box-shadow:0 1px 2px rgba(0,0,0,.15)
  }
  .switch input:checked + .slider{background:#a7f3d0}
  .switch input:checked + .slider:before{transform:translateX(22px)}

  .table-wrap{overflow:auto;border-top:1px solid #eef2f7}
  table.emp{width:100%;border-collapse:separate;border-spacing:0}
  table.emp thead th{
    text-align:left;font-size:12px;color:#6b7280;font-weight:700;letter-spacing:.02em;
    padding:14px;border-bottom:1px solid #eef2f7;background:#f8fafc
  }
  table.emp tbody td{padding:14px;border-bottom:1px solid #f1f5f9;font-size:14px;color:#111827}
  tr.emp-row:last-child td{border-bottom:0}
  tr.emp-row:hover td{background:#fbfdff}

  .emp-name{display:flex;align-items:center;gap:10px;font-weight:600}
  .avatar{
    width:34px;height:34px;border-radius:999px;background:#e5e7eb;display:grid;place-items:center;
    overflow:hidden;font-weight:700;color:#374151
  }
  .avatar img{width:100%;height:100%;object-fit:cover}
  .email{color:#4b5563}
  .position{color:#374151}

  .badge{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:4px 10px;font-size:12px;font-weight:600}
  .dot{width:8px;height:8px;border-radius:999px}
  .badge-active{background:#dcfce7;color:#166534}
  .badge-active .dot{background:#16a34a}
  .badge-inactive{background:#fee2e2;color:#991b1b}
  .badge-inactive .dot{background:#ef4444}

  /* ---- Actions icons ---- */
  .actions{display:flex;align-items:center;gap:14px;justify-content:center}
  .act-btn{
    display:inline-flex;align-items:center;justify-content:center;
    width:28px;height:28px;border-radius:6px;cursor:pointer;
    background:none;border:0;padding:0;transition:.15s
  }
  .act-btn:hover{background:#f3f4f6} /* light gray hover */
  .act-view svg{fill:#6d28d9}   /* purple eye */
  .act-edit svg{fill:#374151}   /* gray pencil */
  .act-toggle svg{fill:#16a34a} /* green power */
  .act-delete svg{fill:#dc2626} /* red trash */

  /* spacing under table card */
  .page-pad{padding:16px}
</style>

<div class="page-head">
  <h1 class="page-title">Employees</h1>
  <a href="{{ route('employees.create') }}" class="btn btn-purple">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path d="M13 11h4v2h-4v4h-2v-4H7v-2h4V7h2v4Z"/></svg>
    Add Employee
  </a>
</div>

<div class="card">
  {{-- header with search + toggle --}}
  <div class="card-head">
    <form method="GET" class="search">
      <input type="text" name="s" value="{{ $s }}" placeholder="Search employees...">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="m21 20-5.2-5.2a7 7 0 1 0-1.4 1.4L20 21l1-1ZM5 10a5 5 0 1 1 10 0 5 5 0 0 1-10 0Z"/></svg>
    </form>

    {{-- optional filter --}}
    <form method="GET" class="toggle">
      <label for="only_active">Show Active Only</label>
      <label class="switch">
        <input type="checkbox" name="only_active" id="only_active" value="1" {{ request('only_active')?'checked':'' }} onchange="this.form.submit()">
        <span class="slider"></span>
      </label>
    </form>
  </div>

  {{-- table --}}
  <div class="table-wrap">
   <table class="emp">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Position</th>
          <th>Status</th>
          <th style="text-align:center">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($employees as $e)
        <tr class="emp-row">
          <td>{{ $e->name }}</td>
          <td>{{ $e->email ?? '-' }}</td>
          <td>{{ $e->position ?? '-' }}</td>
          <td>
            @if($e->status === 'ACTIVE')
              <span class="badge badge-active"><span class="dot"></span> Active</span>
            @else
              <span class="badge badge-inactive"><span class="dot"></span> Inactive</span>
            @endif
          </td>
          <td>
            <div class="actions">
              <a class="act-btn act-view" href="{{ route('employees.show',$e) }}" title="View">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7Zm0 10a3 3 0 1 1 3-3 3 3 0 0 1-3 3Z"/></svg>
              </a>
              <a class="act-btn act-edit" href="{{ route('employees.edit',$e) }}" title="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path d="m3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm17.71-10.2a1 1 0 0 0 0-1.41L18.36 3.3a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.84-1.83Z"/></svg>
              </a>
         
              <form method="POST" action="{{ route('employees.destroy',$e) }}" style="display:inline" onsubmit="return confirm('Delete?')">
                @csrf @method('DELETE')
                <button type="submit" class="act-btn act-delete" title="Delete">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path d="M9 3h6l1 2h5v2H3V5h5l1-2ZM6 21a2 2 0 0 1-2-2V7h16v12a2 2 0 0 1-2 2H6Z"/></svg>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- pagination --}}
  <div class="page-pad">
    {{ $employees->links() }}
  </div>
</div>
@endsection
