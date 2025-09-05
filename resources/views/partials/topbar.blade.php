{{-- resources/views/partials/topbar.blade.php --}}
<div class="h-14 bg-white border-b flex items-center justify-between px-6">
  <div class="text-lg font-semibold">Client Portal</div>
  <div class="flex items-center gap-3">
    <span>{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
    <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-[var(--primary)] hover:underline">Logout</button></form>
  </div>
</div>