{{-- resources/views/reports/diagnostic.blade.php --}}
<!doctype html>
<html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans;} .h{color:#522583;} table{width:100%;border-collapse:collapse} td,th{border:1px solid #ddd;padding:6px}</style></head>
<body>
<h2 class="h">Diagnostic Result</h2>
<p><strong>Company:</strong> {{ auth()->user()->company->name }}<br>
<strong>Employee:</strong> {{ $employee->name }}<br>
<strong>Date:</strong> {{ $result->submitted_at->format('Y-m-d') }}<br>
<strong>Total Score:</strong> {{ $result->score }}</p>
<table>
<thead><tr><th>Item</th><th>Answer</th></tr></thead>
<tbody>
@php $tpl = \App\Models\TestTemplate::where('is_active',true)->first(); @endphp
@foreach($tpl->schema['items'] as $i)
<tr><td>{{ $i['label'] }}</td><td>{{ $result->answers[$i['key']] ?? '-' }}</td></tr>
@endforeach
</tbody>
</table>
</body></html>
