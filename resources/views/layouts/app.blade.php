<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BHCIS') - Sta. Ana</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Source+Sans+3:wght@400;500;600&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            /* Base Palette */
            --bg-page: #ffffff;
            --bg-surface: #ffffff;
            --bg-surface-elevated: #ffffff;
            --bg-sidebar: #0d4a3c;
            --bg-header: #0a3d32;
            
            /* Text / Ink Colors */
            --ink: #0f172a; 
            --ink-muted: #475569; 
            --ink-subtle: #94a3b8;
            --border: rgba(15, 23, 42, 0.08); 

            /* Primary Colors */
            --primary: #0d4a3c;
            --primary-hover: #0a3d32;
            --teal-soft: rgba(13, 74, 60, 0.08);

            /* Accent Colors */
            --accent: #1fb592; 
            --accent-hover: #189a7a;
            --accent-soft: rgba(31, 181, 146, 0.12);
            
            /* Shadows */
            --shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.04);
            --shadow-md: 0 4px 12px rgba(15, 23, 42, 0.06);
            --shadow-lg: 0 12px 32px rgba(15, 23, 42, 0.08);
        }

        .grain::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in { animation: fadeSlideUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.1s; }
        
        .disabled {
            opacity: 0.5;
            filter: grayscale(100%);
            cursor: not-allowed;
        }

        .logo-mark { 
            width: 37px; 
            height: 37px; 
            border-radius: 14px; 
            overflow: hidden; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            background: var(--bg-surface); 
            zoom: 200%;
        }
        .logo-mark img { width: 100%; height: 100%; object-fit: cover; }

        .app-sidebar,
        .app-header {
            color: #ffffff;
        }

        .app-sidebar .text-ink,
        .app-sidebar .text-ink-muted,
        .app-sidebar .text-primary,
        .app-sidebar .nav-link,
        .app-sidebar button {
            color: rgba(255, 255, 255, 0.92) !important;
        }

        .app-sidebar .border-border {
            border-color: rgba(255, 255, 255, 0.18) !important;
        }

        .app-sidebar .nav-link:hover,
        .app-sidebar button:hover {
            background: rgba(255, 255, 255, 0.14) !important;
            color: #ffffff !important;
        }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Poppins', 'system-ui', 'sans-serif'],
                        sans: ['Poppins', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        page: 'var(--bg-page)',
                        surface: 'var(--bg-surface)',
                        'surface-elevated': 'var(--bg-surface-elevated)',
                        ink: 'var(--ink)',
                        'ink-muted': 'var(--ink-muted)',
                        'ink-subtle': 'var(--ink-subtle)',
                        border: 'var(--border)',
                        primary: 'var(--primary)',
                        'teal-soft': 'var(--teal-soft)',
                        accent: 'var(--accent)',
                    },
                    boxShadow: {
                        sm: 'var(--shadow-sm)',
                        md: 'var(--shadow-md)',
                        lg: 'var(--shadow-lg)',
                    }
                },
            },
        };
    </script>
</head>

<body x-data="{ sidebarOpen: false }" 
      :class="{ 'overflow-hidden': sidebarOpen }" 
      class="min-h-screen overflow-x-hidden font-sans text-ink antialiased bg-page">
    
    <div class="grain fixed inset-0 z-0"></div>
    <div class="absolute inset-0 z-0 opacity-40 bg-[linear-gradient(135deg,var(--teal-soft)_0%,transparent_50%,rgba(196,92,65,0.06)_100%)]"></div>

    <div class="relative z-10 flex min-h-screen">
        
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 z-40 bg-black/40 lg:hidden" 
             style="display: none;">
        </div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
               class="app-sidebar transform fixed lg:sticky top-0 h-screen overflow-y-auto w-64 shrink-0 flex flex-col z-50 transition-transform duration-300 ease-out border-r border-border shadow-md"
               style="background: var(--bg-sidebar);">
            
            <div class="flex items-center justify-between p-4 lg:p-5 border-b border-border">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                    <div class="logo-mark">
                        <img src="{{ asset('img/sta-ana-logo.png') }}" alt="Santa Ana logo">
                    </div>
                    <span class="font-display font-semibold text-lg text-ink">BHCIS</span>
                    <span class="text-[10px] font-medium uppercase tracking-wider px-2 py-0.5 rounded bg-teal-soft text-primary">Sta. Ana</span>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden p-2 rounded-lg hover:bg-black/5 transition-colors text-ink-muted">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="flex-1 p-3 space-y-1 overflow-y-auto" 
                 x-data="{ 
                     patientCareOpen: false, 
                     managementOpen: false, 
                     adminOpen: false,
                     initDropdowns() {
                         const current = window.location.pathname;
                         this.patientCareOpen = ['household', 'patient', 'consultation', 'immunization'].some(r => current.includes(r));
                         this.managementOpen = ['medicine', 'report'].some(r => current.includes(r));
                         this.adminOpen = current.includes('user');
                     }
                 }" 
                 x-init="initDropdowns()">
                
                @php
                    /** @var \App\Models\User|null $authUser */
                    $authUser = auth()->user();
                    $swalError = "Swal.fire({title: 'Unauthorized', text: 'Please contact the administrator if you believe this is a mistake.', icon: 'error'}); return false;";
                @endphp

                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5">
                    <i class="fa-solid fa-house texhovert-base opacity-70" aria-hidden="true"></i>
                    <span>Dashboard</span>
                </a>

                <div>
                    <button @click="patientCareOpen = !patientCareOpen" 
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5 hover:opacity-100">
                        <i class="fa-solid fa-user-doctor text-base opacity-70" aria-hidden="true"></i>
                        <span class="flex-1 text-left">Medical Services</span>
                        <i class="fa-solid fa-chevron-down text-sm transition-transform duration-200" :class="{ 'rotate-180': patientCareOpen }" aria-hidden="true"></i>
                    </button>
                    <div x-show="patientCareOpen" 
                         x-collapse
                         class="mt-1 ml-2 pl-3 border-l border-border space-y-0.5">
                        
                        <a href="{{ route('households.index') }}" 
                           class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5 {{ !$authUser->hasPermission('household') ? 'disabled' : '' }}" 
                           {!! !$authUser->hasPermission('household') ? 'onclick="'.$swalError.'"' : '' !!}>
                            <i class="fa-solid fa-house-chimney text-sm opacity-70" aria-hidden="true"></i>
                            <span>Household Registry</span>
                        </a>

                        <a href="{{ url('/patients') }}" 
                           class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5 {{ !$authUser->hasPermission('patients') ? 'disabled' : '' }}" 
                           {!! !$authUser->hasPermission('patients') ? 'onclick="'.$swalError.'"' : '' !!}>
                            <i class="fa-solid fa-user-injured text-sm opacity-70" aria-hidden="true"></i>
                            <span>Individual Health Records</span>
                        </a>

                        <a href="{{ route('consultations.index') }}" 
                           class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5 {{ !$authUser->hasPermission('consultations') ? 'disabled' : '' }}" 
                           {!! !$authUser->hasPermission('consultations') ? 'onclick="'.$swalError.'"' : '' !!}>
                            <i class="fa-solid fa-stethoscope text-sm opacity-70" aria-hidden="true"></i>
                            <span>Check-ups</span>
                        </a>

                        <a href="{{ route('immunizations.index') }}" 
                           class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5 {{ !$authUser->hasPermission('immunizations') ? 'disabled' : '' }}" 
                           {!! !$authUser->hasPermission('immunizations') ? 'onclick="'.$swalError.'"' : '' !!}>
                            <i class="fa-solid fa-syringe text-sm opacity-70" aria-hidden="true"></i>
                            <span>EPI / Vaccinations</span>
                        </a>

                        <a href="{{ route('lab_requests.index') }}" 
                           class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5 {{ !$authUser->hasPermission('lab_requests') ? 'disabled' : '' }}" 
                           {!! !$authUser->hasPermission('lab_requests') ? 'onclick="'.$swalError.'"' : '' !!}>
                            <i class="fa-solid fa-vials text-sm opacity-70" aria-hidden="true"></i>
                            <span>Diagnostics</span>
                        </a>
                    </div>
                </div>

                @if ($authUser && ($authUser->hasPermission('medicines') || $authUser->hasPermission('reports')))
                    <div>
                        <button @click="managementOpen = !managementOpen" 
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5 hover:opacity-100">
                            <i class="fa-solid fa-layer-group text-base opacity-70" aria-hidden="true"></i>
                            <span class="flex-1 text-left">Management</span>
                            <i class="fa-solid fa-chevron-down text-sm transition-transform duration-200" :class="{ 'rotate-180': managementOpen }" aria-hidden="true"></i>
                        </button>
                        <div x-show="managementOpen" 
                             x-collapse
                             class="mt-1 ml-2 pl-3 border-l border-border space-y-0.5">
                            
                            @if ($authUser->hasPermission('medicines'))
                                <a href="{{ route('medicines.index') }}" 
                                   class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5">
                                    <i class="fa-solid fa-pills text-sm opacity-70" aria-hidden="true"></i>
                                    <span>Medicines Lists</span>
                                </a>
                            @endif

                            @if ($authUser->hasPermission('reports'))
                                <a href="{{ route('reports.index') }}" 
                                   class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5">
                                    <i class="fa-solid fa-file-lines text-sm opacity-70" aria-hidden="true"></i>
                                    <span>Reports</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($authUser && $authUser->hasPermission('users'))
                    <div>
                        <button @click="adminOpen = !adminOpen" 
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5 hover:opacity-100">
                            <i class="fa-solid fa-user-gear text-base opacity-70" aria-hidden="true"></i>
                            <span class="flex-1 text-left">Administration</span>
                            <i class="fa-solid fa-chevron-down text-sm transition-transform duration-200" :class="{ 'rotate-180': adminOpen }" aria-hidden="true"></i>
                        </button>
                        <div x-show="adminOpen" 
                             x-collapse
                             class="mt-1 ml-2 pl-3 border-l border-border space-y-0.5">
                            <a href="{{ route('users.index') }}" 
                               class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5">
                                <i class="fa-solid fa-users text-sm opacity-70" aria-hidden="true"></i>
                                <span>User Management</span>
                            </a>
                        </div>
                    </div>
                @endif

                <a href="{{ route('settings.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5">
                    <i class="fa-solid fa-gear text-base opacity-70" aria-hidden="true"></i>
                    <span>Settings</span>
                </a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col min-w-0" x-data="{ headerSticky: false }" @scroll.window="headerSticky = window.scrollY > 275">
            
            <header :class="{ 'sticky top-0': headerSticky }" class="app-header z-40 shrink-0 flex justify-between items-center px-4 lg:px-6 py-1 border-b border-border transition-all duration-200"
                    style="background: var(--bg-header);">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-white/10 transition-colors text-white/90">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                
                <div class="ml-auto flex items-center gap-4">
                    @if ($authUser)
                        @php
                            $roleName = 'User';
                            $username = (string) $authUser->username;
                            $initials = mb_strtoupper(mb_substr($username, 0, 1));
                        @endphp
                        
                        <div x-data="{ profileOpen: false }" class="relative">
                            <button type="button"
                                    @click="profileOpen = !profileOpen"
                                    @click.away="profileOpen = false"
                                    class="flex items-center gap-3 rounded-xl px-3 py-2 hover:shadow-sm transition-all duration-200 bg-white/10 border border-white/20 hover:bg-white/15 text-white">
                                <span class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-semibold bg-white/20 text-white">
                                    {{ $initials }}
                                </span>
                                <span class="hidden sm:block text-left leading-tight">
                                    <span class="block text-sm font-semibold text-white">
                                        {{ $username }}
                                    </span>
                                    <span class="inline-flex mt-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-white/20 text-white">
                                        {{ $roleName }}
                                    </span>
                                </span>
                                <svg class="w-4 h-4 hidden sm:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M6 9l6 6 6-6"></path>
                                </svg>
                            </button>

                            <div x-show="profileOpen"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform translate-y-1"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform translate-y-1"
                                 class="absolute right-0 mt-3 w-52 rounded-xl border border-border shadow-md bg-surface-elevated z-50"
                                 style="display: none;">
                                
                                <div class="px-4 py-3 text-xs border-b border-border text-ink-muted">
                                    <div class="font-semibold text-ink">{{ $username }}</div>
                                    <div>{{ $roleName }}</div>
                                </div>

                                <div class="p-2 space-y-1">
                                    <a href="{{ route('profile.show') }}" class="block px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-black/5 text-ink">
                                        My Profile
                                    </a>
                                    @if($authUser->hasPermission('users'))
                                        <a href="{{ route('profile.settings') }}" class="block px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-black/5 text-ink">
                                            Session Settings
                                        </a>
                                    @endif
                                </div>

                                <div class="p-3 border-t border-border">
                                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex items-center justify-center rounded-lg text-sm font-semibold transition-all duration-200 hover:bg-black/5 active:scale-[0.98] border border-border text-ink py-1.5 bg-transparent">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </header>

            <main class="flex-1 px-2 lg:px-2 py-2 lg:py-2 overflow-auto">
                <div class="max-w-5xl mx-auto">
                    
                    @php
                        $breadcrumbs = \App\Helpers\BreadcrumbHelper::getBreadcrumbs();
                    @endphp
                    @if(count($breadcrumbs) > 1)
                        <nav class="flex items-center gap-2 mb-3 animate-in opacity-0 delay-1 ml-5 -mt-3" aria-label="Breadcrumb">
                            @foreach($breadcrumbs as $index => $crumb)
                                @if($index > 0)
                                    <svg class="w-4 h-4 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M9 18l6-6-6-6"></path>
                                    </svg>
                                @endif
                                @if($crumb['url'])
                                    <a href="{{ $crumb['url'] }}" class="font-medium transition-colors duration-200 hover:opacity-75 text-primary">{{ $crumb['name'] }}</a>
                                @else
                                    <span class="font-semibold text-ink">{{ $crumb['name'] }}</span>
                                @endif
                            @endforeach
                        </nav>
                    @endif
                    
                    <div class="rounded-2xl p-5 lg:p-8 animate-in opacity-0 delay-2 bg-surface-elevated shadow-sm">
                        @yield('content')
                    </div>

                </div>
            </main>

        </div>
    </div>
    <footer class="shrink-0 text-center py-3 text-xs border-t border-[var(--border)]" style="background: var(--bg-surface); color: var(--ink-subtle);">
        &copy; {{ date('Y') }} Barangay Sta. Ana Health Center
        <span class="font-medium hidden sm:inline" style="color: var(--primary);"> — Community care</span>

    <div id="pageDrawer" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closePageDrawer()"></div>
        <aside id="pageDrawerPanel" class="absolute inset-y-0 right-0 w-full max-w-md transform translate-x-full overflow-y-auto bg-white shadow-2xl transition-transform duration-300 ease-out">
            @stack('drawer-content')
        </aside>
    </div>

    <style>
        .nav-link:hover { background: var(--teal-soft); color: var(--primary) !important; }
        .nav-submenu:hover { background: var(--teal-soft); color: var(--primary) !important; }
        a[href="{{ request()->url() }}"].nav-link,
        .nav-link.router-link-active { background: var(--teal-soft); color: var(--primary) !important; }
        a[href="{{ request()->url() }}"].nav-submenu,
        .nav-submenu.router-link-active { background: var(--teal-soft); color: var(--primary) !important; }
    </style>
    <script>
        document.querySelectorAll('.nav-link, .nav-submenu').forEach(function(link) {
            var href = link.getAttribute('href') || '';
            var path = href.replace(/^https?:\/\/[^/]+/, '').replace(/\/$/, '') || '/';
            var current = window.location.pathname.replace(/\/$/, '') || '/';
            if (path === current) link.classList.add('router-link-active');
        });

        function openPageDrawer() {
            var drawer = document.getElementById('pageDrawer');
            var panel = document.getElementById('pageDrawerPanel');
            if (!drawer || !panel) return;
            drawer.classList.remove('hidden');
            panel.classList.remove('translate-x-full');
            panel.classList.add('translate-x-0');
        }

        function closePageDrawer() {
            var drawer = document.getElementById('pageDrawer');
            var panel = document.getElementById('pageDrawerPanel');
            if (!drawer || !panel) return;
            panel.classList.remove('translate-x-0');
            panel.classList.add('translate-x-full');
            panel.addEventListener('transitionend', function handleTransitionEnd() {
                drawer.classList.add('hidden');
                panel.removeEventListener('transitionend', handleTransitionEnd);
            }, { once: true });
        }

        window.openImmunizationDrawer = openPageDrawer;
        window.closeImmunizationDrawer = closePageDrawer;

        // Session timeout check
        @auth
        setInterval(function() {
            fetch('/session/status', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (!data.active) {
                    Swal.fire({
                        title: 'Session Expired',
                        text: 'Your session has expired due to inactivity. You will be redirected to the login page.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then(() => {
                        window.location.href = '/login';
                    });
                }
            })
            .catch(error => {
                console.error('Session check failed:', error);
            });
        }, 30000); // Check every 30 seconds
        @endauth
    </script>

    @stack('scripts')
</body>
</html>