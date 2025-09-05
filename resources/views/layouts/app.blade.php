<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Augusto</title>
  @vite(['resources/js/app.js'])
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon-16x16.png') }}">
<link rel="manifest" href="/site.webmanifest">
  <style>
    :root{
      --primary:#522583;
      --bg:#f6f7fb;
      --card:#ffffff;
      --muted:#6b7280;
      --border:#e5e7eb;
      --shadow:0 1px 2px rgba(16,24,40,.04),0 1px 3px rgba(16,24,40,.06);
    }
    /* ===== Base ===== */
    *{box-sizing:border-box} html,body{height:100%}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Inter,Arial,sans-serif;background:var(--bg);color:#111827}
    a{color:inherit;text-decoration:none}

    /* ===== Shell ===== */
    .shell{min-height:100vh;display:flex}
    .sidebar{width:240px;background:#fff;border-right:1px solid var(--border)}
    .brand{display:flex;align-items:center;gap:.75rem;padding:14px 16px;border-bottom:1px solid var(--border)}
    .brand img{height:34px;max-width:100%;display:block}
    .nav{padding:10px}
    .nav a{display:flex;align-items:center;gap:.6rem;padding:10px 12px;border-radius:8px;color:#111827}
    .nav a:hover{background:#f3e8ff}
    .nav a.active{background:#ede9fe;color:var(--primary);font-weight:600}
    .nav svg{width:16px;height:16px}

    .content{flex:1;display:flex;flex-direction:column;min-width:0}
    .topbar{height:64px;display:flex;align-items:center;justify-content:space-between;padding:0 20px;background:#fff;border-bottom:1px solid var(--border)}
    .top-left{font-size:18px;font-weight:700;color:var(--primary)}
    .top-right{display:flex;align-items:center;gap:16px}
    .icon{width:20px;height:20px;color:var(--muted)}
    .user{position:relative;display:flex;align-items:center;gap:8px}
    .user img{width:34px;height:34px;border-radius:50%;object-fit:cover}
    .user-info{display:flex;flex-direction:column;line-height:1}
    .user-info span:first-child{font-weight:700;font-size:14px}
    .user-info span:last-child{font-size:12px;color:var(--muted)}
    .menu{position:absolute;right:0;top:46px;background:#fff;border:1px solid var(--border);border-radius:10px;box-shadow:var(--shadow);min-width:160px;overflow:hidden;display:none;z-index:30}
    .menu a,.menu button{display:block;width:100%;text-align:left;padding:10px 12px;background:#fff;border:0;font:inherit;cursor:pointer}
    .menu a:hover,.menu button:hover{background:#f8fafc}

    .container{flex:1;max-width:1180px;margin:20px auto;padding:0 20px;width:100%}

    /* ====== Dashboard/Shared Utilities (FIX) ====== */
    /* page heading */
    .page-head{display:flex;align-items:flex-end;gap:16px;margin-bottom:16px}
    .page-head h1{margin:0;font-size:26px;font-weight:800}
    .page-head p{margin:0;color:var(--muted);font-size:14px}

    /* grid helpers */
    .grid{display:grid;gap:16px}
    .grid-4{grid-template-columns:repeat(4,1fr)}
    .grid-3{grid-template-columns:2fr 1fr}
    @media (max-width:1100px){ .grid-4{grid-template-columns:repeat(2,1fr)} .grid-3{grid-template-columns:1fr} }
    @media (max-width:640px){ .grid-4{grid-template-columns:1fr} }

    /* cards */
    .card{background:var(--card);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow)}
    .card.pad{padding:18px}
    .card-header{padding:16px 18px;border-bottom:1px solid var(--border)}
    .card-title{margin:0;font-weight:700}
    .card-sub{margin:4px 0 0;color:var(--muted);font-size:12px}

    /* KPI */
    .kpi{display:flex;justify-content:space-between;align-items:flex-start;width:100%}
    .kpi-title{color:var(--muted);font-size:12px;letter-spacing:.02em;text-transform:uppercase}
    .kpi-value{font-size:30px;font-weight:800;margin-top:6px;line-height:1.1}
    .kpi-hint{color:#059669;font-size:12px;margin-top:4px}
    .kpi-icon{width:40px;height:40px;border-radius:10px;display:grid;place-items:center}
    .bg-purple-50{background:#f3e8ff} .text-purple-600{color:#7c3aed}
    .bg-indigo-50{background:#eef2ff}  .text-indigo-600{color:#4f46e5}
    .bg-orange-50{background:#fff7ed}  .text-orange-500{color:#f97316}
    .bg-emerald-50{background:#ecfdf5} .text-emerald-600{color:#059669}

    /* activity */
    .activity{padding:16px}
    .activity-list{display:grid;gap:12px;margin:0;padding:0;list-style:none}
    .activity-item{display:flex;gap:12px;background:#f8fafc;border:1px solid #eef2f7;border-radius:12px;padding:12px 14px;align-items:flex-start}
    .bubble{width:36px;height:36px;border-radius:999px;display:grid;place-items:center}
    .b-indigo{background:#eef2ff;color:#4f46e5}
    .activity-text{font-size:14px}
    .activity-meta{color:var(--muted);font-size:12px;margin-top:2px}
    .link{color:var(--primary);font-size:13px;white-space:nowrap}

    /* quick actions */
    .qa{display:flex;flex-direction:column;gap:12px;padding:16px}
    .qa-btn{display:flex;align-items:center;justify-content:space-between;border-radius:10px;padding:12px 14px;color:#fff;font-weight:600;transition:transform .04s ease}
    .qa-btn:active{transform:translateY(1px)}
    .qa-btn .left{display:flex;align-items:center;gap:10px}
    .qa-icon{width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,.15);display:grid;place-items:center}
    .qa-icon svg{width:16px;height:16px;fill:#fff}
    .bg-purple{background:#522583}
    .bg-blue{background:#2563eb}
    .bg-orange{background:#f97316}
    .bg-green{background:#16a34a}

    /* small utils */
    .mt-24{margin-top:24px}
    .w-100{width:100%}

    /* responsive: hide sidebar */
    @media (max-width:860px){
      .sidebar{display:none}
      .container{padding:0 14px}
    }
  </style>
</head>
<body>
  <div class="shell">
    <aside class="sidebar">
      <div class="brand">
        <img src="{{ asset('assets/images/attento-logo.jpg') }}" alt="ATTENTO">
      </div>
      <nav class="nav">
        <a class="{{ request()->routeIs('dashboard')?'active':'' }}" href="{{ route('dashboard') }}">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 10 12 3l9 7v10H3V10Zm7 8h4v-6h-4v6Z"/></svg>
          <span>Dashboard</span>
        </a>
        <a class="{{ str_contains(request()->route()?->getName() ?? '','employees')?'active':'' }}" href="{{ route('employees.index') }}">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-3.53 0-8 1.77-8 4v2h16v-2c0-2.23-4.47-4-8-4Z"/></svg>
          <span>Employees</span>
        </a>
      </nav>
    </aside>

    <section class="content">
      <div class="topbar">
        <div class="top-left">Attento Portal</div>
        <div class="top-right" style="cursor: pointer;">
         <!--  <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg> -->

          @guest
            <a href="{{ route('login') }}" class="user-info" style="font-weight:700;color:var(--primary)">Login</a>
            @if (Route::has('register'))
              <a href="{{ route('register') }}" class="user-info" style="color:var(--muted)">Register</a>
            @endif
          @endguest

          @auth
          <div class="user" id="userBtn">
            <img src="{{ Auth::user()->avatar_url ?? 'https://i.pravatar.cc/100?img=3' }}" alt="User">
            <div class="user-info">
              <span>{{ Auth::user()->name }}</span>
              <span>{{ Auth::user()->role ?? 'User' }}</span>
            </div>
            <div class="menu" id="userMenu" >
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background:#f07167; font-weight:700 ">Logout</button>
              </form>
            </div>
          </div>
          @endauth
        </div>
      </div>

      <div class="container">
        {{ $slot ?? '' }}
        @yield('content')
      </div>
    </section>
  </div>

  <script>
    (function(){
      const btn = document.getElementById('userBtn');
      const menu = document.getElementById('userMenu');
      if(btn && menu){
        btn.addEventListener('click', () => {
          menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        });
        document.addEventListener('click', (e) => {
          if(!btn.contains(e.target)) menu.style.display = 'none';
        });
      }
    })();
  </script>
</body>
</html>
