<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BHCIS') - Sta. Ana</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,400&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --font-display: 'Poppins', Georgia, serif;
            --font-body: 'Source Sans 3', system-ui, sans-serif;
            --bg-page: #f5f0e8;
            --bg-surface: #fdfcfa;
            --bg-surface-elevated: #ffffff;
            --ink: #1a1f1c;
            --ink-muted: #000000;
            --ink-subtle: #8a928d;
            --border: rgba(26, 31, 28, 0.12);
            
            /* Primary Colors (Dark Green, Hue: 166) */
            --primary: #0d4a3c;
            --primary-hover: #0a3d32;
            --teal-soft: rgba(13, 74, 60, 0.08);

            /* Accent Colors (Lighter Green, Exact same Hue: 166) */
            --accent: #0d4a3c; 
            --accent-hover: #0a3d32;
        --accent-soft: rgba(31, 181, 146, 0.12);
            
            /* Shadows & UI Settings */
            --shadow-sm: 0 1px 2px rgba(26, 31, 28, 0.06);
            --shadow-md: 0 4px 12px rgba(26, 31, 28, 0.08);
            --shadow-lg: 0 12px 32px rgba(26, 31, 28, 0.1);
            --radius: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
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
        .animate-in {
            animation: fadeSlideUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.1s; }
        .delay-3 { animation-delay: 0.15s; }
        .delay-4 { animation-delay: 0.2s; }
        .delay-5 { animation-delay: 0.25s; }
        .delay-6 { animation-delay: 0.3s; }
        .opacity-0 { opacity: 0; }
        .disabled {
            opacity: 0.5;
            filter: grayscale(100%);
            cursor: not-allowed;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Fraunces', 'Georgia', 'serif'],
                        sans: ['Source Sans 3', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        ink: '#1a1f1c',
                        'ink-muted': '#5c6560',
                        primary: '#0d4a3c',
                        accent: '#c45c41',
                    },
                },
            },
        };
    </script>
</head>
<body x-data="{ sidebarOpen: false }" :class="{ 'overflow-hidden': sidebarOpen }" class="min-h-screen overflow-x-hidden font-sans text-[var(--ink)] antialiased" style="background: var(--bg-page);">
    <div class="grain fixed inset-0 z-0"></div>
    <div class="absolute inset-0 z-0 opacity-40" style="background: linear-gradient(135deg, var(--teal-soft) 0%, transparent 50%, rgba(196,92,65,0.06) 100%);"></div>

    <div class="relative z-10 flex min-h-screen">
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-black/40 lg:hidden" style="display: none;"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" class="transform fixed lg:static top-0 h-screen lg:h-screen overflow-y-auto w-64 shrink-0 flex flex-col z-50 transition-transform duration-300 ease-out border-r border-[var(--border)]" style="background: var(--bg-surface-elevated); box-shadow: var(--shadow-md);">
            <div class="flex items-center justify-between p-4 lg:p-5 border-b border-[var(--border)]">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg text-white font-display font-semibold text-sm" style="background: var(--primary);">B</span>
                    <span class="font-display font-semibold text-lg" style="color: var(--ink);">BHCIS</span>
                    <span class="text-[10px] font-medium uppercase tracking-wider px-2 py-0.5 rounded" style="background: var(--teal-soft); color: var(--primary);">Sta. Ana</span>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden p-2 rounded-lg hover:bg-black/5 transition-[background]" style="color: var(--ink-muted);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="flex-1 p-3 space-y-1 overflow-y-auto" x-data="{ 
                patientCareOpen: false, 
                managementOpen: false, 
                adminOpen: false,
                initDropdowns() {
                    const current = window.location.pathname;
                    if (current.includes('household') || current.includes('patient') || current.includes('consultation') || current.includes('immunization')) {
                        this.patientCareOpen = true;
                    }
                    if (current.includes('medicine') || current.includes('report')) {
                        this.managementOpen = true;
                    }
                    if (current.includes('user')) {
                        this.adminOpen = true;
                    }
                },
                isActive(routes) {
                    const current = window.location.pathname;
                    return routes.some(route => current.includes(route));
                }
            }" @load="initDropdowns()">
                @php
                    /** @var \App\Models\User|null $authUser */
                    $authUser = auth()->user();
                @endphp

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-[background,color] duration-200" style="color: var(--ink-muted);">
                    <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="9" y="3" width="6" height="4" rx="1"></rect>
                        <path d="M5 7h14v14H5z"></path>
                        <path d="M9 11h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Patient Care Dropdown -->
                <div>
                    <button @click="patientCareOpen = !patientCareOpen" :class="{ 'bg-opacity-100': patientCareOpen }" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-[background,color] duration-200 hover:opacity-100" style="color: var(--ink-muted);">
                        <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span class="flex-1 text-left">Patient Care</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': patientCareOpen }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 9l6 6 6-6"></path>
                        </svg>
                    </button>
                    <div x-show="patientCareOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-1 ml-2 pl-3 border-l space-y-0.5" style="border-color: var(--border);">
                        <a href="{{ route('households.index') }}" class="nav-link nav-submenu flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-[background,color] duration-200 @if(!$authUser->hasPermission('household')) disabled @endif" @if(!$authUser->hasPermission('household')) onclick="Swal.fire({title: 'Unauthorized', text: 'Please contact the administrator if you believe this is a mistake.', icon: 'error'}); return false;" @endif>
                            <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 10.5 12 3l9 7.5"></path>
                                <path d="M5 10v11h14V10"></path>
                                <path d="M10 21v-7h4v7"></path>
                            </svg>
                            <span>Households</span>
                        </a>
                        <a href="{{ url('/patients') }}" class="nav-link nav-submenu flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-[background,color] duration-200 @if(!$authUser->hasPermission('patients')) disabled @endif" @if(!$authUser->hasPermission('patients')) onclick="Swal.fire({title: 'Unauthorized', text: 'Please contact the administrator if you believe this is a mistake.', icon: 'error'}); return false;" @endif>
                            <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                            </svg>
                            <span>Patients</span>
                        </a>
                        <a href="{{ route('consultations.index') }}" class="nav-link nav-submenu flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-[background,color] duration-200 @if(!$authUser->hasPermission('consultations')) disabled @endif" @if(!$authUser->hasPermission('consultations')) onclick="Swal.fire({title: 'Unauthorized', text: 'Please contact the administrator if you believe this is a mistake.', icon: 'error'}); return false;" @endif>
                            <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="M21 21l-4.35-4.35"></path>
                            </svg>
                            <span>Consultations</span>
                        </a>
                        <a href="{{ route('immunizations.index') }}" class="nav-link nav-submenu flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-[background,color] duration-200 @if(!$authUser->hasPermission('immunizations')) disabled @endif" @if(!$authUser->hasPermission('immunizations')) onclick="Swal.fire({title: 'Unauthorized', text: 'Please contact the administrator if you believe this is a mistake.', icon: 'error'}); return false;" @endif>
                            <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M10 3h4v4h4v4h-4v4h-4v-4H6V7h4z"></path>
                            </svg>
                            <span>Immunization</span>
                        </a>
                    </div>
                </div>

                <!-- Management & Reports Dropdown -->
                @if ($authUser && ($authUser->hasPermission('medicines') || $authUser->hasPermission('reports')))
                    <div>
                        <button @click="managementOpen = !managementOpen" :class="{ 'bg-opacity-100': managementOpen }" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-[background,color] duration-200 hover:opacity-100" style="color: var(--ink-muted);">
                            <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"></path>
                                <rect x="10" y="6" width="4" height="8" rx="1"></rect>
                            </svg>
                            <span class="flex-1 text-left">Management</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': managementOpen }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 9l6 6 6-6"></path>
                            </svg>
                        </button>
                        <div x-show="managementOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-1 ml-2 pl-3 border-l space-y-0.5" style="border-color: var(--border);">
                            @if ($authUser && $authUser->hasPermission('medicines'))
                                <a href="{{ route('medicines.index') }}" class="nav-link nav-submenu flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-[background,color] duration-200 @if(!$authUser->hasPermission('medicines')) disabled @endif" @if(!$authUser->hasPermission('medicines')) onclick="Swal.fire({title: 'Unauthorized', text: 'Please contact the administrator if you believe this is a mistake.', icon: 'error'}); return false;" @endif>
                                    <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M10.5 6h2.25v.75h-2.25V6zm0 3h2.25v.75h-2.25V9zm0 3h2.25v.75h-2.25v-.75z"></path>
                                        <path d="M17 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z"></path>
                                    </svg>
                                    <span>Medicines</span>
                                </a>
                            @endif
                            @if ($authUser && $authUser->hasPermission('reports'))
                                <a href="{{ route('reports.index') }}" class="nav-link nav-submenu flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-[background,color] duration-200 @if(!$authUser->hasPermission('reports')) disabled @endif" @if(!$authUser->hasPermission('reports')) onclick="Swal.fire({title: 'Unauthorized', text: 'Please contact the administrator if you believe this is a mistake.', icon: 'error'}); return false;" @endif>
                                    <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M3 3v18h18"></path>
                                        <path d="M7 14l2-2 3 3 5-5"></path>
                                    </svg>
                                    <span>Reports</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Administration Dropdown (Admin only) -->
                @if ($authUser && $authUser->hasPermission('users'))
                    <div>
                        <button @click="adminOpen = !adminOpen" :class="{ 'bg-opacity-100': adminOpen }" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-[background,color] duration-200 hover:opacity-100" style="color: var(--ink-muted);">
                            <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"></path>
                            </svg>
                            <span class="flex-1 text-left">Administration</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': adminOpen }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 9l6 6 6-6"></path>
                            </svg>
                        </button>
                        <div x-show="adminOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="mt-1 ml-2 pl-3 border-l space-y-0.5" style="border-color: var(--border);">
                            <a href="{{ route('users.index') }}" class="nav-link nav-submenu flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-[background,color] duration-200 @if(!$authUser->hasPermission('users')) disabled @endif" @if(!$authUser->hasPermission('users')) onclick="Swal.fire({title: 'Unauthorized', text: 'Please contact the administrator if you believe this is a mistake.', icon: 'error'}); return false;" @endif>
                                <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span>User Management</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Settings -->
                <a href="{{ route('settings.index') }}" class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-[background,color] duration-200" style="color: var(--ink-muted);">
                    <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 0 0 2.572-1.065z"></path>
                        <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"></path>
                    </svg>
                    <span>Settings</span>
                </a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="shrink-0 flex justify-between items-center px-4 lg:px-6 py-3 border-b border-[var(--border)]" style="background: var(--bg-surface);">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-black/5 transition-[background]" style="color: var(--ink-muted);">
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
                                    class="flex items-center gap-3 rounded-xl border px-3 py-2 hover:shadow-sm transition-[background,transform,box-shadow] duration-200"
                                    style="background: var(--bg-surface); border-color: var(--border); color: var(--ink-muted);">
                                <span class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-semibold"
                                      style="background: var(--teal-soft); color: var(--primary);">
                                    {{ $initials }}
                                </span>
                                <span class="hidden sm:block text-left leading-tight">
                                    <span class="block text-sm font-semibold" style="color: var(--ink);">
                                        {{ $username }}
                                    </span>
                                    <span class="inline-flex mt-1 px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                          style="background: var(--teal-soft); color: var(--primary);">
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
                                 class="absolute right-0 mt-3 w-52 rounded-xl border shadow-lg z-50"
                                 style="background: var(--bg-surface-elevated); border-color: var(--border); box-shadow: var(--shadow-md);">
                                <div class="px-4 py-3 text-xs" style="color: var(--ink-muted); border-bottom: 1px solid var(--border);">
                                    <div class="font-semibold" style="color: var(--ink);">{{ $username }}</div>
                                    <div>{{ $roleName }}</div>
                                </div>

                                <div class="p-3">
                                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center rounded-lg text-sm font-semibold transition-[background,transform] duration-200 hover:bg-black/5 active:scale-[0.98]"
                                                style="background: transparent; color: var(--ink); border: 1px solid var(--border);">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </header>

            <main class="flex-1 px-4 lg:px-6 py-6 lg:py-8 overflow-auto">
                <div class="max-w-5xl mx-auto">
                    @php
                        $breadcrumbs = \App\Helpers\BreadcrumbHelper::getBreadcrumbs();
                    @endphp
                    @if(count($breadcrumbs) > 1)
                        <nav class="flex items-center gap-2 mb-6 text-sm animate-in opacity-0 delay-1" aria-label="Breadcrumb">
                            @foreach($breadcrumbs as $index => $crumb)
                                @if($index > 0)
                                    <svg class="w-4 h-4 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M9 18l6-6-6-6"></path>
                                    </svg>
                                @endif
                                @if($crumb['url'])
                                    <a href="{{ $crumb['url'] }}" class="font-medium transition-colors duration-200 hover:opacity-75" style="color: var(--primary);">{{ $crumb['name'] }}</a>
                                @else
                                    <span class="font-semibold" style="color: var(--ink);">{{ $crumb['name'] }}</span>
                                @endif
                            @endforeach
                        </nav>
                    @endif
                    <div class="rounded-2xl p-5 lg:p-8 border border-[var(--border)] animate-in opacity-0 delay-2" style="background: var(--bg-surface-elevated); box-shadow: var(--shadow-sm);">
                        @yield('content')
                    </div>
                </div>
            </main>

            <footer class="shrink-0 text-center py-3 text-xs border-t border-[var(--border)]" style="background: var(--bg-surface); color: var(--ink-subtle);">
                &copy; {{ date('Y') }} Barangay Sta. Ana Health Center
                <span class="font-medium hidden sm:inline" style="color: var(--primary);"> — Community care</span>
            </footer>
        </div>
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
    </script>
</body>
</html>