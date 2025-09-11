<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Mystery Evaluation Report</title>
  <style>
    /* Dompdf-safe styles (no webfonts) */
    html,body{font-family: DejaVu Sans, sans-serif; font-size:12px; color:#111}
    .h{color:#522583; margin:0 0 8px}
    .muted{color:#6b7280}
    .meta{margin:0 0 14px}
    table{width:100%; border-collapse:collapse; table-layout:fixed}
    th,td{border:1px solid #ddd; padding:6px; vertical-align:top}
    th{background:#f3f4f6; font-weight:700}
    .sec{background:#efe6fb; color:#3b0ca3; font-weight:700}
    .r{text-align:right}
    .w-45{width:45%}
    .w-15{width:15%}
    .mb-6{margin-bottom:6px}
  </style>
</head>
<body>
@php
  // Normalize schema (array) and answers
  $schema = is_string($evaluation->checklist->schema)
            ? json_decode($evaluation->checklist->schema, true)
            : ($evaluation->checklist->schema ?? []);
  $answers = is_string($evaluation->answers)
            ? json_decode($evaluation->answers, true)
            : ($evaluation->answers ?? []);
  if (!is_array($answers)) $answers = [];

  // Build sections[] no matter what the original layout is
  $sections = [];
  if (isset($schema['sections']) && is_array($schema['sections'])) {
      $sections = $schema['sections'];
  } elseif (isset($schema['items']) && is_array($schema['items'])) {
      $sections = [[ 'title' => $schema['title'] ?? 'Checklist', 'items' => $schema['items'] ]];
  }

  // Pretty-print an answer based on type
  $show = function($item, $val) {
      $type = $item['type'] ?? 'text';
      if (is_null($val)) return '—';

      switch ($type) {
          case 'yes_no':
              return ($val==='yes' || $val===1 || $val===true || $val==='1') ? 'Yes' : 'No';
          case 'yes_no_na':
              $s = strtolower((string)$val);
              if (in_array($s, ['na','n/a'])) return 'N/A';
              return ($val==='yes' || $val===1 || $val===true || $val==='1') ? 'Yes' : 'No';
          case 'scale':
          case 'range':
              $min = $item['min'] ?? 1; $max = $item['max'] ?? 5;
              return $val . " (min {$min} – max {$max})";
          case 'note':
          case 'textarea':
              return nl2br(e($val));
          default:
              return is_array($val) ? e(json_encode($val)) : e((string)$val);
      }
  };

  $companyName = optional(auth()->user()->company)->name ?? '—';
  $videoUrl = !empty($evaluation->video_path) ? asset($evaluation->video_path) : null;
@endphp

<h2 class="h">Mystery Evaluation Report</h2>

<p class="meta">
  <strong>Company:</strong> {{ $companyName }}<br>
  <strong>Employee:</strong> {{ $evaluation->employee->name }}<br>
  <strong>Month:</strong> {{ $evaluation->monthKey }}<br>
  <strong>Score:</strong> {{ $evaluation->score }}/100
</p>

<table>
  <thead>
    <tr>
      <th class="w-45">Item</th>
      <th>Answer</th>
      <th class="w-15 r">Weight</th>
    </tr>
  </thead>
  <tbody>
    @foreach($sections as $sec)
      <tr>
        <td class="sec" colspan="3">{{ $sec['title'] ?? 'Section' }}</td>
      </tr>
      @foreach(($sec['items'] ?? []) as $i)
        @php
          $k = $i['key'] ?? null;
          $label = $i['label'] ?? $k ?? '—';
          $val = $k ? ($answers[$k] ?? null) : null;
          $weight = (float)($i['weight'] ?? 0);
        @endphp
        <tr>
          <td>{!! e($label) !!}</td>
          <td>{!! $show($i, $val) !!}</td>
          <td class="r">{{ $weight > 0 ? number_format($weight,0) : '—' }}</td>
        </tr>
      @endforeach
    @endforeach
  </tbody>
</table>

<p class="mb-6">
  <strong>Video:</strong>
  @if($videoUrl)
    {{ $videoUrl }}
  @else
    —
  @endif
</p>

<p class="muted">Generated on {{ now()->format('Y-m-d H:i') }}</p>
</body>
</html>
