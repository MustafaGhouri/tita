{{-- resources/views/auth/login.blade.php --}}
<x-guest-layout>
  <style>
    :root{
      --primary:#522583; --muted:#6b7280; --border:#e5e7eb; --card:#ffffff;
      --shadow:0 1px 2px rgba(16,24,40,.04),0 1px 3px rgba(16,24,40,.06);
      --bg:#f6f7fb;
    }
    .auth-wrap{min-height:calc(100vh - 0px);display:flex;align-items:center;justify-content:center;background:var(--bg);padding:28px}
    .auth-card{width:100%;max-width:440px;background:var(--card);border:1px solid var(--border);border-radius:16px;box-shadow:var(--shadow);padding:24px 24px 18px}
    .top-icon{display:grid;place-items:center;margin-bottom:14px}
    .top-icon .box{
        width:50%;
        height:50%;
        border-radius:16px;
       
        display:grid;
        place-items:center
        
    }
    .auth-title{margin:0 0 6px;font-size:24px;font-weight:800;text-align:center}
    .auth-sub{margin:0 0 18px;text-align:center;color:var(--muted);font-size:14px}
    .field{margin-bottom:14px}
    .label{display:block;font-weight:600;font-size:14px;margin-bottom:6px}
    .control{position:relative}
    .icon-left{position:absolute;inset:0 auto 0 0;width:40px;display:flex;align-items:center;justify-content:center;color:#9ca3af;pointer-events:none}
    .icon-right-btn{position:absolute;inset:0 0 0 auto;width:40px;display:flex;align-items:center;justify-content:center;border:0;background:transparent;color:#9ca3af;cursor:pointer}
    .input{
      width:100%;border:1px solid var(--border);border-radius:10px;background:#fff;
      padding:11px 44px 11px 44px;font-size:14px;outline:none;transition:.15s
    }
    .input:focus{border-color:#c7b5e0;box-shadow:0 0 0 3px rgba(82,37,131,.12)}
    .row-actions{display:flex;justify-content:flex-end;margin-top:4px}
    .link{color:var(--primary);font-size:13px;text-decoration:none}
    .link:hover{text-decoration:underline}
    .btn{width:100%;border:0;border-radius:10px;background:var(--primary);color:#fff;font-weight:700;padding:12px 14px;cursor:pointer;margin-top:6px}
    .err{margin-top:6px;color:#dc2626;font-size:12px}
    .foot{margin-top:14px;text-align:center;color:#9ca3af;font-size:12px}
  </style>

  <div class="auth-wrap">
    <div class="auth-card">
      {{-- Top Icon --}}
      <div class="top-icon">
        <div class="box">
          <!--<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="#fff">-->
          <!--  <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.33 0-8 2.17-8 4v2h16v-2c0-1.83-3.67-4-8-4Z"/>-->
          <!--</svg>-->
          <img src="{{ asset('public/assets/images/logo.png') }}" alt="logo">
        </div>
      </div>

      <h1 class="auth-title">Sign in to your account mazzzzzzzz</h1>
      <p class="auth-sub">Enter your credentials to access the system</p>

      <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        {{-- Username --}}
        <div class="field">
          <label class="label">Username</label>
          <div class="control">
            <span class="icon-left">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.33 0-8 2.17-8 4v2h16v-2c0-1.83-3.67-4-8-4Z"/>
              </svg>
            </span>
            <input
              class="input"
              type="text"
              name="email"
              value="{{ old('email') }}"
              placeholder="Enter username"
              required
              autofocus
            />
          </div>
          @error('email') <div class="err">{{ $message }}</div> @enderror
        </div>

        {{-- Password --}}
        <div class="field">
          <label class="label">Password</label>
          <div class="control">
            <span class="icon-left">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17 9h-1V7a4 4 0 10-8 0v2H7a2 2 0 00-2 2v7a2 2 0 002 2h10a2 2 0 002-2v-7a2 2 0 00-2-2Zm-7-2a2 2 0 114 0v2H10V7Zm7 11H7v-7h10v7Z"/>
              </svg>
            </span>
            <input
              id="password"
              class="input"
              type="password"
              name="password"
              placeholder="Enter password"
              required
            />
            <button type="button" id="togglePassword" class="icon-right-btn" aria-label="Show password">
              <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7Zm0 12a5 5 0 1 1 5-5 5 5 0 0 1-5 5Zm0-8a3 3 0 1 0 3 3 3 3 0 0 0-3-3Z"/>
              </svg>
            </button>
          </div>
          @error('password') <div class="err">{{ $message }}</div> @enderror
        </div>

        <div class="row-actions">
          <a href="{{ route('password.request') }}" class="link">Forgot password?</a>
        </div>

        <button type="submit" class="btn">Login</button>
      </form>

      <div class="foot">© {{ now()->year }} Attento. All rights reserved.</div>
    </div>
  </div>

  {{-- Password toggle --}}
  <script>
    (function(){
      const input = document.getElementById('password');
      const btn   = document.getElementById('togglePassword');
      const swap  = (isOpen)=>(
        isOpen
          ? '<svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M2.81 2.81 1.39 4.22l3.06 3.06C2.83 8.3 1.53 9.99 1 12c1.73 3.89 6 7 11 7 2.02 0 3.9-.51 5.53-1.39l3.25 3.25 1.41-1.41L2.81 2.81Z"/></svg>'
          : '<svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7Zm0 12a5 5 0 1 1 5-5 5 5 0 0 1-5 5Zm0-8a3 3 0 1 0 3 3 3 3 0 0 0-3-3Z"/></svg>'
      );
      btn?.addEventListener('click', function(){
        const open = input.type === 'password';
        input.type = open ? 'text' : 'password';
        this.innerHTML = swap(open);
      });
    })();
  </script>
</x-guest-layout>
