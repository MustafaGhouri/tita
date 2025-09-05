{{-- resources/views/employees/index.blade.php --}}
@extends('layouts.app')

@section('content')
{{-- DataTables (CDN) --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<style>
  /* ---- Your existing styles (kept) ---- */
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
  .slider{position:absolute;inset:0;background:#e5e7eb;border-radius:999px;transition:.2s;box-shadow:inset 0 0 0 1px #d1d5db}
  .slider:before{content:"";position:absolute;height:18px;width:18px;left:3px;top:3px;background:#fff;border-radius:50%;transition:.2s;box-shadow:0 1px 2px rgba(0,0,0,.15)}
  .switch input:checked + .slider{background:#a7f3d0}
  .switch input:checked + .slider:before{transform:translateX(22px)}

  .table-wrap{overflow:auto;border-top:1px solid #eef2f7}
  table.emp{width:100%;border-collapse:separate;border-spacing:0;min-width:820px}
  table.emp thead th{
    text-align:left;font-size:12px;color:#6b7280;font-weight:700;letter-spacing:.02em;
    padding:14px;border-bottom:1px solid #eef2f7;background:#f8fafc;position:sticky;top:0;z-index:1
  }
  table.emp tbody td{padding:14px;border-bottom:1px solid #f1f5f9;font-size:14px;color:#111827;vertical-align:middle}
  tr.emp-row:hover td{background:#fbfdff}

  .badge{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700}
  .dot{width:8px;height:8px;border-radius:999px}
  .badge-active{background:#dcfce7;color:#166534;border:1px solid #bbf7d0}
  .badge-active .dot{background:#16a34a}
  .badge-inactive{background:#fee2e2;color:#991b1b;border:1px solid #fecaca}
  .badge-inactive .dot{background:#ef4444}

  .actions{display:flex;align-items:center;gap:10px;justify-content:center}
  .act-btn{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;cursor:pointer;background:#fff;border:1px solid #e5e7eb;transition:.15s}
  .act-btn:hover{background:#f3f4f6}
  .act-view svg{fill:#6d28d9}.act-edit svg{fill:#374151}.act-delete svg{fill:#dc2626}
  .act-btn svg{width:18px;height:18px}

  .page-pad{padding:16px}

  /* ---- DataTables minor polish ---- */
  .dataTables_wrapper .dataTables_filter{display:none} /* ham apna custom search use kar rahe */
  .dt-buttons{margin:10px 16px 0 16px}
  .dt-button{border:1px solid #e5e7eb;border-radius:8px;background:#fff;padding:6px 10px;font-weight:700;cursor:pointer;margin-right:6px}
  .dt-button:hover{background:#f3f4f6}
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
    <div class="search">
      <input id="dt-search" type="text" placeholder="Search employees...">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="m21 20-5.2-5.2a7 7 0 1 0-1.4 1.4L20 21l1-1ZM5 10a5 5 0 1 1 10 0 5 5 0 0 1-10 0Z"/></svg>
    </div>

    <form method="GET" class="toggle" action="">
      <label for="only_active">Show Active Only</label>
      <label class="switch">
        <input type="checkbox" name="only_active" id="only_active" value="1" {{ request('only_active')?'checked':'' }} onchange="this.form.submit()">
        <span class="slider"></span>
      </label>
    </form>
  </div>

  {{-- DataTable --}}
  <div class="table-wrap">
    <table class="emp" id="empTable">
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
        @forelse($employees as $e)
          <tr class="emp-row">
            <td>{{ $e->name ?? trim(($e->first_name ?? '').' '.($e->last_name ?? '')) }}</td>
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
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7Zm0 10a3 3 0 1 1 3-3 3 3 0 0 1-3 3Z"/></svg>
                </a>
                <a class="act-btn act-edit" href="{{ route('employees.edit',$e) }}" title="Edit">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="m3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm17.71-10.2a1 1 0 0 0 0-1.41L18.36 3.3a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.84-1.83Z"/></svg>
                </a>
                <form method="POST" action="{{ route('employees.destroy',$e) }}" style="display:inline" onsubmit="return confirm('Delete?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="act-btn act-delete" title="Delete">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 3h6l1 2h5v2H3V5h5l1-2ZM6 21a2 2 0 0 1-2-2V7h16v12a2 2 0 0 1-2 2H6Z"/></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="page-pad">No employees found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- DataTables handles pagination; server links removed --}}
</div>

<script>
  $(function () {
    // init DataTable
    const dt = $('#empTable').DataTable({
      pageLength: 10,
      lengthMenu: [[10,25,50,100,-1],[10,25,50,100,'All']],
      order: [], // keep your original order (disable default sort)
      dom: 'Bfrtip', // Buttons + default UI
      buttons: [
        { extend: 'copyHtml5',  title: 'Employees' },
        { extend: 'csvHtml5',   title: 'Employees' },
        { extend: 'excelHtml5', title: 'Employees' },
        { extend: 'pdfHtml5',   title: 'Employees' },
        { extend: 'print',      title: 'Employees' }
      ],
      language: { search: "", zeroRecords: "No matching employees found" }
    });

    // hook our search box
    $('#dt-search').on('keyup', function () {
      dt.search(this.value).draw();
    });
  });
</script>
@endsection
