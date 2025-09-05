{{-- resources/views/reports/mystery.blade.php --}}
<!doctype html>
<html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans;} .h{color:#522583;} table{width:100%;border-collapse:collapse} td,th{border:1px solid #ddd;padding:6px}</style></head>
<body>
<h2 class="h">Mystery Evaluation Report</h2>
<p><strong>Company:</strong> {{ auth()->user()->company->name }}<br>
<strong>Employee:</strong> {{ $evaluation->employee->name }}<br>
<strong>Month:</strong> {{ $evaluation->monthKey }}<br>
<strong>Score:</strong> {{ $evaluation->score }}</p>
<table>
<thead><tr><th>Item</th><th>Answer</th></tr></thead>
<tbody>
@foreach($evaluation->checklist->schema['items'] as $i)
<tr><td>{{ $i['label'] }}</td><td>{{ $evaluation->answers[$i['key']] ?? '-' }}</td></tr>
@endforeach
</tbody>
</table>
<p>Video: {{ asset('storage/'.$evaluation->video_path) }}</p>
</body></html>
