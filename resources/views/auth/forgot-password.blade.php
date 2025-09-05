{{-- resources/views/auth/forgot-password.blade.php --}}
<x-guest-layout>
  <style>
    :root{
      --primary:#522583; --muted:#6b7280; --border:#e5e7eb; --card:#ffffff;
      --shadow:0 1px 2px rgba(16,24,40,.04),0 1px 3px rgba(16,24,40,.06);
      --bg:#f6f7fb; --success:#16a34a; --danger:#dc2626; --info:#2563eb;
    }
    .auth-wrap{min-height:calc(100vh - 0px);display:flex;align-items:center;justify-content:center;background:var(--bg);padding:28px}
    .auth-card{width:100%;max-width:440px;background:var(--card);border:1px solid var(--border);border-radius:16px;box-shadow:var(--shadow);padding:24px 24px 18px}
    .top-icon{display:grid;place-items:center;margin-bottom:14px}
    .top-icon .box{width:56px;height:56px;border-radius:16px;background:var(--primary);display:grid;place-items:center}
    .auth-title{margin:0 0 6px;font-size:24px;font-weight:800;text-align:center}
    .auth-sub{margin:0 0 18px;text-align:center;color:var(--muted);font-size:14px}
    .field{margin-bottom:14px}
    .label{display:block;font-weight:600;font-size:14px;margin-bottom:6px}
    .control{position:relative}
    .icon-left{position:absolute;inset:0 auto 0 0;width:40px;display:flex;align-items:center;justify-content:center;color:#9ca3af;pointer-events:none}
    .input{width:100%;border:1px solid var(--border);border-radius:10px;background:#fff;padding:11px 14px 11px 44px;font-size:14px;outline:none;transition:.15s}
    .input:focus{border-color:#c7b5e0;box-shadow:0 0 0 3px rgba(82,37,131,.12)}
    .btn{width:100%;border:0;border-radius:10px;background:var(--primary);color:#fff;font-weight:700;padding:12px 14px;cursor:pointer;margin-top:6px}
    .btn[disabled]{opacity:.6;cursor:not-allowed}
    .link{color:var(--primary);font-size:13px;text-decoration:none}
    .link:hover{text-decoration:underline}
    .row-actions{display:flex;justify-content:space-between;align-items:center;margin-top:4px}
    .err{margin-top:6px;color:var(--danger);font-size:12px}
    .alert{border-radius:10px;border:1px solid var(--border);padding:10px 12px;font-size:13px;margin-bottom:12px;display:flex;gap:8px;align-items:flex-start}
    .alert.success{border-color:#bbf7d0;background:#f0fdf4;color:#14532d}
    .alert.info{border-color:#bfdbfe;background:#eff6ff;color:#1e3a8a}
    .foot{margin-top:14px;text-align:center;color:#9ca3af;font-size:12px}
  </style>

  <div class="auth-wrap">
    <div class="auth-card">
      {{-- Top Icon --}}
      <div class="top-icon">
        <div class="box" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="#fff">
            <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5L4 8V6l8 5 8-5v2Z"/>
          </svg>
        </div>
      </div>

      <h1 class="auth-title">Forgot your password?</h1>
      <p class="auth-sub">
        Enter your email and we’ll send you a link to reset it.
      </p>

      {{-- Session Status (success message after sending reset link) --}}
      @if (session('status'))
        <div class="alert success" role="status">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="margin-top:2px">
            <path d="M12 22A10 10 0 1 1 22 12 10.011 10.011 0 0 1 12 22Zm-1-6 7-7-1.414-1.414L11 13.172 7.414 9.586 6 11Z"/>
          </svg>
          <span>{{ session('status') }}</span>
        </div>
      @endif

      {{-- Info hint (first time view) --}}
      @if (!session('status'))
        <div class="alert info" role="note">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="margin-top:2px">
            <path d="M11 17h2v-6h-2v6Zm0-8h2V7h-2v2Zm1 13A10 10 0 1 1 22 12 10.011 10.011 0 0 1 12 22Z"/>
          </svg>
          <span>We’ll email a secure reset link if the address matches an account.</span>
        </div>
      @endif

      <form method="POST" action="{{ route('password.email') }}" novalidate id="forgotForm">
        @csrf

        {{-- Email --}}
        <div class="field">
          <label class="label" for="email">Email address</label>
          <div class="control">
            <span class="icon-left">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5L4 8V6l8 5 8-5v2Z"/>
              </svg>
            </span>
            <input
              class="input"
              id="email"
              type="email"
              name="email"
              value="{{ old('email') }}"
              placeholder="you@example.com"
              autocomplete="email"
              required
              autofocus
            />
          </div>
          @error('email') <div class="err">{{ $message }}</div> @enderror
        </div>

        <div class="row-actions">
          <a href="{{ route('login') }}" class="link">Back to login</a>
        </div>

        <button type="submit" class="btn" id="sendBtn">
          Email Password Reset Link
        </button>
      </form>

      <div class="foot">© {{ now()->year }} Attento. All rights reserved.</div>
    </div>
  </div>

  <script>
    // Optional: prevent double submit
    (function(){
      const form = document.getElementById('forgotForm');
      const btn  = document.getElementById('sendBtn');
      form?.addEventListener('submit', function(){
        btn.disabled = true;
        btn.textContent = 'Sending...';
      });
    })();
  </script>
</x-guest-layout>
