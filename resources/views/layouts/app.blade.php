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
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
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

        /* Normalize checkbox appearance & size across the app */
        input[type="checkbox"],
        input[type="checkbox"].checkbox,
        input[type="checkbox"].household-checkbox,
        input[type="checkbox"]#selectAllCheckbox,
        input[type="checkbox"].rounded {
            width: 1rem !important; /* 16px */
            height: 1rem !important; /* 16px */
            min-width: 1rem !important;
            min-height: 1rem !important;
            padding: 0 !important;
            margin: 0 !important;
            box-sizing: border-box !important;
            vertical-align: middle !important;
            -webkit-appearance: checkbox !important;
            appearance: checkbox !important;
            transform: scale(1) !important;
        }

        /* Reduce visual differences from border-radius utilities */
        input[type="checkbox"].rounded { border-radius: 4px !important; }

        /* Ensure accent color consistent */
        input[type="checkbox"] { accent-color: var(--primary); }

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

<body x-data="{ sidebarOpen: false, desktopSidebarOpen: localStorage.getItem('desktop-sidebar-open') !== '0' }" 
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

        <aside :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0', desktopSidebarOpen ? 'lg:w-64 lg:border-r lg:shadow-md' : 'lg:w-0 lg:border-r-0 lg:shadow-none']" 
               class="app-sidebar transform fixed lg:sticky top-0 h-screen overflow-y-auto w-64 shrink-0 flex flex-col z-50 transition-all duration-300 ease-out border-r border-border shadow-md"
               style="background: var(--bg-sidebar);">
            
            <div class="flex items-center justify-between p-4 lg:p-5 border-b border-border">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                    <div class="logo-mark" style="background: transparent;">
                        <img src="{{ asset('img/logo.svg') }}" alt="Santa Ana logo">
                    </div>
                    <span class="font-display font-semibold text-lg text-ink">iHEALTHSYS</span>
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
                        <span class="flex-1 text-left">Services</span>
                        <i class="fa-solid fa-chevron-down text-sm transition-transform duration-200" :class="{ 'rotate-180': patientCareOpen }" aria-hidden="true"></i>
                    </button>
                    <div x-show="patientCareOpen" 
                         x-collapse
                         class="mt-1 ml-2 pl-3 border-l border-border space-y-0.5">
                        
                        <a href="{{ route('households.index') }}" 
                           class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5 {{ !$authUser->hasPermission('household') ? 'disabled' : '' }}" 
                           {!! !$authUser->hasPermission('household') ? 'onclick="'.$swalError.'"' : '' !!}>
                            <i class="fa-solid fa-house-chimney text-sm opacity-70" aria-hidden="true"></i>
                            <span>Household</span>
                        </a>

                        <a href="{{ url('/patients') }}" 
                           class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5 {{ !$authUser->hasPermission('patients') ? 'disabled' : '' }}" 
                           {!! !$authUser->hasPermission('patients') ? 'onclick="'.$swalError.'"' : '' !!}>
                            <i class="fa-solid fa-user-injured text-sm opacity-70" aria-hidden="true"></i>
                            <span>Patients</span>
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
                            <span>Immunizations</span>
                        </a>

                        <a href="{{ route('lab_requests.index') }}" 
                           class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5 {{ !$authUser->hasPermission('lab_requests') ? 'disabled' : '' }}" 
                           {!! !$authUser->hasPermission('lab_requests') ? 'onclick="'.$swalError.'"' : '' !!}>
                            <i class="fa-solid fa-vials text-sm opacity-70" aria-hidden="true"></i>
                            <span>Labs / Tests</span>
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
                            <a href="{{ route('zones.index') }}" 
                           class="nav-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 text-ink-muted hover:bg-black/5 {{ !$authUser->hasPermission('zones') ? 'disabled' : '' }}" 
                           {!! !$authUser->hasPermission('zones') ? 'onclick="'.$swalError.'"' : '' !!}>
                            <i class="fa-solid fa-map-marker-alt text-sm opacity-70" aria-hidden="true"></i>
                            <span>Manage Purok</span>
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

                <button @click="desktopSidebarOpen = !desktopSidebarOpen; localStorage.setItem('desktop-sidebar-open', desktopSidebarOpen ? '1' : '0')"
                        class="hidden lg:inline-flex p-2 rounded-lg hover:bg-white/10 transition-colors text-white/90"
                        :title="desktopSidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'">
                    <i class="fa-solid text-sm" :class="desktopSidebarOpen ? 'fa-angles-left' : 'fa-angles-right'" aria-hidden="true"></i>
                </button>

                <div class="ml-auto flex items-center gap-4">
                    @if ($authUser)
                        @php
                            $roleName = 'User';
                            $username = (string) $authUser->username;
                            $initials = mb_strtoupper(mb_substr($username, 0, 1));
                            $notifications = auth()->user()->notifications()->latest()->take(5)->get();
                            $unreadCount = auth()->user()->unreadNotifications->count();
                        @endphp
                        
                        <!-- Notifications Dropdown -->
                        <div x-data="{ notificationsOpen: false }" class="relative">
                            <button type="button"
                                    @click="notificationsOpen = !notificationsOpen"
                                    @click.away="notificationsOpen = false"
                                    class="relative p-2 rounded-lg hover:bg-white/10 transition-colors text-white/90 hover:text-white">
                                <i class="fa-solid fa-bell text-lg" aria-hidden="true"></i>
                                @if ($unreadCount > 0)
                                    <span class="absolute top-1 right-1 inline-flex items-center justify-center h-5 w-5 text-xs font-bold rounded-full bg-red-500 text-white">
                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                    </span>
                                @endif
                            </button>

                            <div x-show="notificationsOpen"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform translate-y-1"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform translate-y-1"
                                 class="absolute right-0 mt-3 w-80 rounded-xl border border-border shadow-md bg-surface-elevated z-50"
                                 style="display: none;">
                                
                                <div class="px-4 py-3 border-b border-border">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-ink">Notifications</h3>
                                        @if ($unreadCount > 0)
                                            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-xs font-medium text-primary hover:opacity-70 transition-opacity">
                                                    Mark all as read
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <div class="max-h-96 overflow-y-auto">
                                    @forelse ($notifications as $notification)
                                        <div class="px-4 py-3 border-b border-border hover:bg-black/3 transition-colors {{ is_null($notification->read_at) ? 'bg-teal-soft' : '' }}">
                                            <div class="flex gap-3">
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-ink">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                                    <p class="text-xs text-ink-muted mt-1">{{ $notification->data['message'] ?? '' }}</p>
                                                    <p class="text-xs text-ink-subtle mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                                                </div>
                                                @if (is_null($notification->read_at))
                                                    <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="p-1 rounded hover:bg-black/10 transition-colors" title="Mark as read">
                                                            <i class="fa-solid fa-check text-xs text-primary" aria-hidden="true"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="px-4 py-8 text-center">
                                            <i class="fa-solid fa-bell-slash text-2xl text-ink-subtle opacity-50 mb-2" aria-hidden="true"></i>
                                            <p class="text-sm text-ink-muted">No notifications yet</p>
                                        </div>
                                    @endforelse
                                </div>

                                <div class="px-4 py-3 border-t border-border">
                                    <a href="{{ route('notifications.index') }}" class="block text-center text-sm font-medium text-primary hover:opacity-75 transition-opacity">
                                        View all notifications
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div x-data="{ profileOpen: false }" class="relative">
                            <button type="button"
                                    @click="profileOpen = !profileOpen"
                                    @click.away="profileOpen = false"
                                    class="flex items-center gap-3 rounded-xl px-3 py-2 hover:shadow-sm transition-all duration-200  border border-white/20 hover:bg-white/15 text-white">
                                <span class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-semibold bg-white/20 text-white">
                                    {{ $initials }}
                                </span>
                                <span class="hidden sm:block text-left leading-tight">
                                    <span class="block text-sm font-semibold text-white">
                                    {{ ucwords($username) }}
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

            <main class="flex-1 px-2 lg:px-2 pt-3 pb-2 lg:pt-3 lg:pb-2 overflow-auto">
                <div class="max-w-5xl mx-auto">
                    
                    @php
                        $breadcrumbs = \App\Helpers\BreadcrumbHelper::getBreadcrumbs();
                    @endphp
                    @if(count($breadcrumbs) > 1)
                        <nav class="flex items-center gap-2 mb-2 py-1 animate-in opacity-0 delay-1 ml-5" aria-label="Breadcrumb">
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
            
            <footer class="shrink-0 text-center py-3 text-xs border-t border-[var(--border)]" style="background: var(--bg-surface); color: var(--ink-subtle);">
                &copy; {{ date('Y') }} Barangay Sta. Ana Health Center. All rights reserved.
            </footer>
        </div>
    </div>

    <div id="liveConsultationToast" class="fixed bottom-5 right-5 z-[60] hidden max-w-[380px] rounded-3xl border border-slate-200 bg-white shadow-[0_24px_80px_rgba(14,30,37,0.15)] ring-1 ring-slate-900/5 overflow-hidden" aria-live="assertive" aria-atomic="true">
        <div class="p-5">
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 text-lg">!</span>
                <div class="min-w-0">
                    <p id="liveToastTitle" class="text-sm font-semibold text-slate-900">New Consultation Request</p>
                    <p id="liveToastSubtitle" class="text-xs text-slate-500 mt-1">Santa Ana Health Center • BHW</p>
                </div>
            </div>

            <div class="mt-4 rounded-3xl bg-slate-50 p-4 text-slate-700">
                <p id="liveToastPatient" class="text-sm font-semibold"></p>
                <p id="liveToastDetails" class="text-xs text-slate-500 mt-1"></p>
                <p id="liveToastReason" class="mt-3 text-sm text-slate-700"></p>
            </div>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <button id="liveToastDecline" type="button" class="w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 sm:w-auto">Cancel</button>
                <button id="liveToastAccept" type="button" class="w-full rounded-2xl bg-emerald-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800 sm:w-auto">Accept & Open Case</button>
            </div>
        </div>
    </div>

    <div id="pageModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closePageDrawer()"></div>
        <div id="pageModalPanel" class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            @stack('modal-content')
        </div>
    </div>

    <style>
        .nav-link:hover { background: var(--teal-soft); color: var(--primary) !important; }
        .nav-submenu:hover { background: var(--teal-soft); color: var(--primary) !important; }
        a[href="{{ request()->url() }}"].nav-link,
        .nav-link.router-link-active { background: var(--teal-soft); color: var(--primary) !important; }
        a[href="{{ request()->url() }}"].nav-submenu,
        .nav-submenu.router-link-active { background: var(--teal-soft); color: var(--primary) !important; }

        .app-sidebar .nav-link:hover,
        .app-sidebar .nav-submenu:hover {
            background: rgba(255, 255, 255, 0.16) !important;
            color: #ffffff !important;
        }

        .app-sidebar a[href="{{ request()->url() }}"].nav-link,
        .app-sidebar .nav-link.router-link-active,
        .app-sidebar a[href="{{ request()->url() }}"].nav-submenu,
        .app-sidebar .nav-submenu.router-link-active {
            background: rgba(255, 255, 255, 0.24) !important;
            color: #ffffff !important;
            font-weight: 600;
        }

        #liveConsultationToast {
            transform: translateX(16px);
            opacity: 0;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        #liveConsultationToast.active {
            display: block;
            transform: translateX(0);
            opacity: 1;
        }
    </style>
    <script>
        document.querySelectorAll('.nav-link, .nav-submenu').forEach(function(link) {
            var href = link.getAttribute('href') || '';
            var path = href.replace(/^https?:\/\/[^/]+/, '').replace(/\/$/, '') || '/';
            var current = window.location.pathname.replace(/\/$/, '') || '/';
            if (path === current) link.classList.add('router-link-active');
        });

        function openPageDrawer() {
            var modal = document.getElementById('pageModal');
            var panel = document.getElementById('pageModalPanel');
            if (!modal || !panel) return;
            modal.classList.remove('hidden');
            requestAnimationFrame(() => {
                panel.classList.remove('scale-95', 'opacity-0');
                panel.classList.add('scale-100', 'opacity-100');
            });
        }

        function closePageDrawer() {
            var modal = document.getElementById('pageModal');
            var panel = document.getElementById('pageModalPanel');
            if (!modal || !panel) return;
            panel.classList.remove('scale-100', 'opacity-100');
            panel.classList.add('scale-95', 'opacity-0');
            panel.addEventListener('transitionend', function handleTransitionEnd() {
                modal.classList.add('hidden');
                panel.removeEventListener('transitionend', handleTransitionEnd);
            }, { once: true });
        }

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

        @if(auth()->check() && auth()->user()->hasPermission('consultations'))
        (function() {
            var liveToast = document.getElementById('liveConsultationToast');
            var liveToastAccept = document.getElementById('liveToastAccept');
            var liveToastDecline = document.getElementById('liveToastDecline');
            var liveToastLastId = localStorage.getItem('lastLiveConsultationId');
            var liveToastCloseTimer = null;

            function playLiveToastChime() {
                if (!window.AudioContext && !window.webkitAudioContext) {
                    return;
                }
                var AudioContext = window.AudioContext || window.webkitAudioContext;
                var ctx = new AudioContext();
                var osc = ctx.createOscillator();
                var gain = ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(880, ctx.currentTime);
                gain.gain.setValueAtTime(0, ctx.currentTime);
                gain.gain.linearRampToValueAtTime(0.18, ctx.currentTime + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.7);
            }

            function notifyBrowser(payload) {
                if (!('Notification' in window)) {
                    return;
                }

                if (Notification.permission === 'default') {
                    Notification.requestPermission();
                }

                if (Notification.permission === 'granted') {
                    var notification = new Notification(payload.title, {
                        body: payload.message,
                        tag: 'live-consultation-alert',
                    });
                    notification.onclick = function() {
                        window.focus();
                        if (payload.openUrl) {
                            window.location.href = payload.openUrl;
                        }
                    };
                }
            }

            function dismissConsultationToast() {
                if (!liveToast) {
                    return;
                }
                liveToast.classList.remove('active');
                if (liveToastCloseTimer) {
                    clearTimeout(liveToastCloseTimer);
                    liveToastCloseTimer = null;
                }
            }

            function showConsultationToast(request) {
                if (!liveToast || !request || !request.id) {
                    return;
                }

                if (liveToastLastId === String(request.id)) {
                    return;
                }

                document.getElementById('liveToastTitle').textContent = 'New Consultation Request';
                document.getElementById('liveToastSubtitle').textContent = request.clinic_name + ' · BHW: ' + request.worker_name;
                document.getElementById('liveToastPatient').textContent = request.patient_name + ' • ' + (request.patient_age ? request.patient_age + ' y/o' : 'Age unknown') + (request.patient_gender ? ' / ' + request.patient_gender : '');
                document.getElementById('liveToastDetails').textContent = 'Reason: ' + (request.chief_complaint || 'No complaint provided');
                document.getElementById('liveToastReason').textContent = request.chief_complaint || 'No complaint provided';

                liveToastAccept.onclick = function() {
                    localStorage.setItem('lastLiveConsultationId', request.id);
                    window.location.href = request.open_url;
                };
                liveToastDecline.onclick = function() {
                    localStorage.setItem('lastLiveConsultationId', request.id);
                    dismissConsultationToast();
                };

                liveToast.classList.add('active');
                liveToast.classList.remove('hidden');
                if (liveToastCloseTimer) {
                    clearTimeout(liveToastCloseTimer);
                }
                liveToastCloseTimer = setTimeout(dismissConsultationToast, 18000);
                liveToastLastId = request.id;

                playLiveToastChime();
                notifyBrowser({
                    title: 'New Consultation Request',
                    message: request.patient_name + ' • ' + (request.patient_age ? request.patient_age + ' y/o' : 'Age unknown') + '\n' + request.chief_complaint,
                    openUrl: request.open_url,
                });
            }

            /**
             * Polls the server for live consultation requests
             * 
             * Sends a GET request to the '/consultations/live-requests' endpoint
             * to fetch active consultation requests in real-time.
             * 
             * Uses the Fetch API with credentials set to 'same-origin' to ensure
             * cookies and authentication tokens are included in the request.
             * 
             * Sets appropriate headers:
             * - 'X-Requested-With': Identifies the request as XMLHttpRequest (AJAX)
             * - 'Accept': Specifies the response format as JSON
             * 
             * @returns {Promise} A promise that resolves with the server response
             */
            function pollLiveConsultationRequests() {
                fetch('/consultations/live-requests', {
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Live request fetch failed');
                    }
                    return response.json();
                })
                .then(function(data) {
                    if (data.hasRequest && data.request) {
                        showConsultationToast(data.request);
                    }
                })
                .catch(function(error) {
                    console.error('Live consultation poll failed:', error);
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                pollLiveConsultationRequests();
                setInterval(pollLiveConsultationRequests, 12000);
            });
        })();
        @endif
        @endauth
    </script>

    @stack('scripts')
</body>
</html>