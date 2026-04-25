@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-4 lg:space-y-6">
    @if ($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-red-800">
            <p class="font-semibold mb-1">Please fix the following:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 class="text-xl lg:text-2xl font-bold text-gray-800">Register New Patient</h1>
    </div>

    <form action="{{ route('patients.store') }}" method="POST" class="bg-white p-4 lg:p-6 xl:p-8 rounded-xl lg:rounded-lg shadow-sm border border-gray-200 space-y-6 lg:space-y-8" x-data="patientCreateTabs()" x-init="init()">
        @csrf

        {{-- Tabbed layout to reduce vertical scrolling --}}
        <div class="flex flex-wrap gap-2 border-b border-gray-100 pb-4">
            <button type="button"
                    class="px-3 lg:px-4 py-1.5 rounded-xl text-xs lg:text-sm font-semibold border transition"
                    :class="tab === 'household' ? 'bg-sky-50 border-sky-300 text-sky-700' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'"
                    @click="tab = 'household'">
                Household
            </button>
            <button type="button"
                    class="px-3 lg:px-4 py-1.5 rounded-xl text-xs lg:text-sm font-semibold border transition"
                    :class="tab === 'personal' ? 'bg-sky-50 border-sky-300 text-sky-700' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'"
                    @click="tab = 'personal'">
                Personal Info
            </button>
            <button type="button"
                    class="px-3 lg:px-4 py-1.5 rounded-xl text-xs lg:text-sm font-semibold border transition"
                    :class="tab === 'social' ? 'bg-sky-50 border-sky-300 text-sky-700' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'"
                    @click="tab = 'social'">
                Social Status
            </button>
        </div>

        <div class="pb-4 lg:pb-6 border-b border-gray-100" x-show="tab === 'household'" x-cloak>
            <h3 class="text-sm lg:text-base font-semibold text-sky-700 mb-3 lg:mb-4 flex items-center">
                <span class="mr-2">🏠</span>
                Household Information
            </h3>
            
            <div x-data='householdAutocomplete({
                    initialId: @json($selectedHouseholdId ? (int) $selectedHouseholdId : null),
                    initialText: @json($selectedHousehold?->family_name_head ?? ""),
                    transientId: @json($transientHouseholdId ?? null),
                    transientLabel: @json($transientHouseholdLabel ?? "Transient/Unmapped")
                })'
                 x-init="init()">
                <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">
                    Select Household <span class="text-red-500">*</span>
                </label>

                <div class="flex items-center gap-3 flex-wrap mb-3">
                    <input type="checkbox"
                           id="transient_unmapped"
                           x-model="isTransient"
                           @change="onTransientToggle()"
                           class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-gray-300 rounded"
                           :disabled="transientHouseholdId === null">
                    <label for="transient_unmapped" class="text-xs lg:text-sm text-gray-800 font-medium">
                        Transient/Unmapped
                    </label>

                    <template x-if="transientHouseholdId === null">
                        <span class="text-xs text-red-600">Transient household not found.</span>
                    </template>
                </div>

                <input type="hidden" name="household_id" x-model="householdId">

                <div class="relative">
                    <input type="text"
                           x-ref="householdSearch"
                           x-model="query"
                           @input.debounce.250ms="search()"
                           @focus="dropdownOpen = true"
                           @keydown.escape="dropdownOpen = false"
                           @click="dropdownOpen = true"
                           :disabled="isTransient"
                           placeholder="Search household (family name / zone / contact)..."
                           class="w-full px-3 lg:px-4 py-2 lg:py-2.5 rounded-xl border @error('household_id') border-red-500 bg-red-50 @else border-gray-300 @enderror focus:ring-sky-500 focus:border-sky-500 text-sm"
                           autocomplete="off">

                    <div x-show="dropdownOpen && !isTransient && results.length > 0" class="absolute z-20 w-full bg-white mt-1 border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                        <ul>
                            <template x-for="item in results" :key="item.id">
                                <li class="px-3 lg:px-4 py-2 lg:py-2.5 hover:bg-sky-50 cursor-pointer border-b last:border-0 text-xs lg:text-sm"
                                    @click.prevent="select(item)">
                                    <div class="font-medium text-gray-800" x-text="item.text"></div>
                                    <div class="text-xs text-gray-500" x-text="item.subtext"></div>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <div class="text-xs text-gray-500" x-show="!isTransient && query.length > 0 && results.length === 0" x-cloak>
                    No household found.
                </div>

                @error('household_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @else
                    <p class="text-xs text-gray-500 mt-1">Use search to avoid scrolling through 500+ households.</p>
                @enderror
            </div>
        </div>

        <div class="pb-4 lg:pb-6 border-b border-gray-100" x-show="tab === 'personal'" x-cloak>
            <h3 class="text-sm lg:text-base font-semibold text-sky-700 mb-3 lg:mb-4 flex items-center">
                <span class="mr-2">👤</span>
                Personal Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 lg:gap-4 mb-3 lg:mb-4">
                <div>
                    <label class="block text-xs uppercase text-gray-500 font-bold mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" 
                           class="w-full px-3 lg:px-4 py-2 rounded-xl border @error('first_name') border-red-500 bg-red-50 @else border-gray-300 @enderror focus:ring-sky-500 focus:border-sky-500 text-sm">
                    @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs uppercase text-gray-500 font-bold mb-1">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                           class="w-full px-3 lg:px-4 py-2 rounded-xl border @error('middle_name') border-red-500 bg-red-50 @else border-gray-300 @enderror focus:ring-sky-500 focus:border-sky-500 text-sm">
                    @error('middle_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs uppercase text-gray-500 font-bold mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                           class="w-full px-3 lg:px-4 py-2 rounded-xl border @error('last_name') border-red-500 bg-red-50 @else border-gray-300 @enderror focus:ring-sky-500 focus:border-sky-500 text-sm">
                    @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-3 lg:mb-4">
                <div>
                    <label class="block text-xs uppercase text-gray-500 font-bold mb-1">Suffix</label>
                    <input type="text" name="suffix" placeholder="Jr, III" value="{{ old('suffix') }}" class="w-full px-3 lg:px-4 py-2 rounded-xl border border-gray-300 focus:ring-sky-500 focus:border-sky-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs uppercase text-gray-500 font-bold mb-1">Sex <span class="text-red-500">*</span></label>
                    <select name="sex" class="w-full px-3 lg:px-4 py-2 rounded-xl border @error('sex') border-red-500 @else border-gray-300 @enderror focus:ring-sky-500 focus:border-sky-500 text-sm">
                        <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs uppercase text-gray-500 font-bold mb-1">Birthdate <span class="text-red-500">*</span></label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                           class="w-full px-3 lg:px-4 py-2 rounded-xl border @error('date_of_birth') border-red-500 bg-red-50 @else border-gray-300 @enderror focus:ring-sky-500 focus:border-sky-500 text-sm">
                    @error('date_of_birth') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs uppercase text-gray-500 font-bold mb-1">Blood Type</label>
                    <select name="blood_type" class="w-full px-3 lg:px-4 py-2 rounded-xl border border-gray-300 focus:ring-sky-500 focus:border-sky-500 text-sm">
                        <option value="">Unknown</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $type)
                            <option value="{{ $type }}" {{ old('blood_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs uppercase text-gray-500 font-bold mb-1">Birth Place</label>
                <input type="text" name="birth_place" value="{{ old('birth_place') }}" 
                       placeholder="City/Municipality, Province" class="w-full px-3 lg:px-4 py-2 rounded-xl border border-gray-300 focus:ring-sky-500 focus:border-sky-500 text-sm">
            </div>
        </div>

        <div class="pb-4 lg:pb-6 border-b border-gray-100" x-show="tab === 'social'" x-cloak>
            <h3 class="text-sm lg:text-base font-semibold text-sky-700 mb-3 lg:mb-4 flex items-center">
                <span class="mr-2">💼</span>
                Socio-Economic Status
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 lg:gap-4 mb-3 lg:mb-4">
                <div>
                    <label class="block text-xs uppercase text-gray-500 font-bold mb-1">Civil Status <span class="text-red-500">*</span></label>
                    <select name="civil_status" class="w-full px-3 lg:px-4 py-2 rounded-xl border @error('civil_status') border-red-500 @else border-gray-300 @enderror focus:ring-sky-500 focus:border-sky-500 text-sm">
                        @foreach(['Single', 'Married', 'Widowed', 'Separated', 'Common Law'] as $status)
                            <option value="{{ $status }}" {{ old('civil_status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs uppercase text-gray-500 font-bold mb-1">Education</label>
                    <select name="educational_attainment" class="w-full px-3 lg:px-4 py-2 rounded-xl border border-gray-300 focus:ring-sky-500 focus:border-sky-500 text-sm">
                        <option value="">Select Level</option>
                        @foreach(['None', 'Elementary', 'High School', 'College', 'Vocational'] as $edu)
                            <option value="{{ $edu }}" {{ old('educational_attainment') == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs uppercase text-gray-500 font-bold mb-1">Employment</label>
                    <input type="text" name="employment_status" value="{{ old('employment_status') }}" 
                           placeholder="None If Unemployed" class="w-full px-3 lg:px-4 py-2 rounded-xl border border-gray-300 focus:ring-sky-500 focus:border-sky-500 text-sm">
                </div>
            </div>

            <div class="flex flex-wrap gap-4 lg:gap-6 p-3 lg:p-4 bg-gray-50 rounded-xl border border-gray-100">
                <div class="flex items-center">
                    <input type="checkbox" name="has_4ps" id="4ps" value="1" 
                           class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-gray-300 rounded"
                           {{ old('has_4ps') ? 'checked' : '' }}>
                    <label for="4ps" class="ml-2 block text-xs lg:text-sm text-gray-900 font-medium">4Ps Member</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="has_nhts" id="nhts" value="1"
                           class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-gray-300 rounded"
                           {{ old('has_nhts') ? 'checked' : '' }}>
                    <label for="nhts" class="ml-2 block text-xs lg:text-sm text-gray-900 font-medium">NHTS / Indigent</label>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap justify-end gap-2 lg:gap-3">
            <a href="{{ route('patients.index') }}" class="px-4 lg:px-6 py-2 rounded-xl border border-gray-300 text-gray-600 hover:bg-[var(--primary-light)] text-xs lg:text-sm font-medium">Cancel</a>
            <button type="submit" class="px-5 lg:px-6 py-2 lg:py-2.5 rounded-xl bg-[var(--primary)] text-white font-semibold text-xs lg:text-sm shadow-md hover:bg-[var(--primary-light)] transition">
                Save Patient Record
            </button>
        </div>
    </form>

    <script>
        function patientCreateTabs() {
            return {
                tab: 'household',
                init() {
                    this.tab = 'household';
                },
            };
        }

        function householdAutocomplete({ initialId, initialText, transientId, transientLabel }) {
            return {
                query: initialText || '',
                householdId: initialId || null,
                transientHouseholdId: transientId ?? null,
                transientHouseholdLabel: transientLabel || 'Transient/Unmapped',
                isTransient: transientId !== null && initialId !== null && String(initialId) === String(transientId),
                previousHouseholdId: null,
                previousQuery: null,
                dropdownOpen: false,
                results: [],
                loading: false,

                init() {
                    this.$nextTick(() => {
                        if (this.$refs.householdSearch) {
                            this.$refs.householdSearch.focus();
                        }
                    });

                    // If the old household is the transient household, lock it in.
                    if (this.isTransient && this.transientHouseholdId !== null) {
                        this.householdId = this.transientHouseholdId;
                        this.query = this.transientHouseholdLabel;
                    }
                },

                async search() {
                    if (this.isTransient) return;
                    const q = (this.query || '').trim();
                    if (q.length < 2) {
                        this.results = [];
                        this.dropdownOpen = false;
                        return;
                    }

                    this.loading = true;
                    try {
                        const response = await fetch(`/search/households?query=${encodeURIComponent(q)}`);
                        this.results = await response.json();
                        this.dropdownOpen = this.results.length > 0;
                    } catch (e) {
                        console.error('Household search failed:', e);
                        this.results = [];
                    } finally {
                        this.loading = false;
                    }
                },

                select(item) {
                    this.householdId = item.id;
                    this.query = item.text;
                    this.results = [];
                    this.dropdownOpen = false;
                },

                onTransientToggle() {
                    this.results = [];
                    this.dropdownOpen = false;

                    if (this.isTransient) {
                        this.previousHouseholdId = this.householdId;
                        this.previousQuery = this.query;

                        if (this.transientHouseholdId === null) {
                            this.isTransient = false;
                            return;
                        }

                        this.householdId = this.transientHouseholdId;
                        this.query = this.transientHouseholdLabel;
                        return;
                    }

                    if (this.previousHouseholdId !== null) {
                        this.householdId = this.previousHouseholdId;
                        this.query = this.previousQuery ?? this.query;
                    }
                },
            };
        }
    </script>
</div>
@endsection
