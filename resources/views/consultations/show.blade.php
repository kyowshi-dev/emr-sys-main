@extends('layouts.app')

@section('content')
<div class="space-y-3 lg:space-y-4">
    @if (session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-3 lg:px-4 py-2 text-xs lg:text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 px-3 lg:px-4 py-2 text-xs lg:text-sm text-red-800">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-2">
        <div class="flex flex-wrap items-center gap-2 lg:gap-3">
            <a href="{{ route('patients.show', $patient->id) }}" class="text-xs lg:text-sm font-medium text-sky-600 hover:text-sky-800">← Back to patient</a>
            <span class="text-gray-300 hidden sm:inline">|</span>
            <a href="{{ route('consultations.index') }}" class="text-xs lg:text-sm font-medium text-sky-600 hover:text-sky-800">History</a>
        </div>
        <span class="inline-flex items-center px-2 lg:px-3 py-0.5 lg:py-1 rounded-full text-xs font-semibold
            @if ($consultation->status === 'completed') bg-emerald-100 text-emerald-700
            @elseif ($consultation->status === 'referred') bg-amber-100 text-amber-700
            @else bg-sky-100 text-sky-700
            @endif">
            {{ ucfirst(str_replace('_', ' ', $consultation->status)) }}
        </span>
    </div>
</div>

<div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 mt-4 lg:mt-6">
    <div class="md:col-span-1 space-y-4 lg:space-y-6">
        <div class="bg-white p-4 lg:p-6 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200">
            <h2 class="text-lg lg:text-xl font-bold text-gray-800">{{ $patient->last_name }}, {{ $patient->first_name }}</h2>
            <p class="text-xs lg:text-sm text-gray-500 mb-3 lg:mb-4">Patient ID: PT{{ str_pad($patient->id, 3, '0', STR_PAD_LEFT) }}</p>
            <div class="text-xs lg:text-sm space-y-1.5 lg:space-y-2">
                <div class="flex justify-between"><span class="text-gray-500">Sex</span> <span class="font-medium">{{ $patient->sex }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Age</span> <span class="font-medium">{{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} y/o</span></div>
            </div>
        </div>

        <div class="bg-sky-50 p-4 lg:p-6 rounded-xl lg:rounded-2xl border border-sky-100">
            <h3 class="font-bold text-sky-800 mb-2 lg:mb-3 flex items-center gap-2 text-sm lg:text-base">📊 Vital Signs</h3>
            <div class="grid grid-cols-2 gap-3 lg:gap-4 text-xs lg:text-sm">
                <div>
                    <span class="text-sky-600 block text-xs uppercase font-medium">BP <span class="text-red-500">*</span></span>
                    <span class="font-bold text-gray-700">{{ $vitals->bp_systolic ?? '—' }}/{{ $vitals->bp_diastolic ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-sky-600 block text-xs uppercase font-medium">Temp <span class="text-red-500">*</span></span>
                    <span class="font-bold text-gray-700">{{ $vitals->temperature_c !== null && $vitals->temperature_c !== '' ? $vitals->temperature_c.'°C' : '—' }}</span>
                </div>
                <div>
                    <span class="text-sky-600 block text-xs uppercase font-medium">Weight</span>
                    <span class="font-bold text-gray-700">{{ $vitals->weight_kg ?? '—' }} kg</span>
                </div>
                <div>
                    <span class="text-sky-600 block text-xs uppercase font-medium">Height</span>
                    <span class="font-bold text-gray-700">{{ $vitals->height_cm ?? '—' }} cm</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 lg:p-5 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200">
            <h3 class="font-bold text-gray-700 mb-2 text-sm lg:text-base">Chief Complaint</h3>
            <p class="text-xs lg:text-sm text-gray-600 bg-gray-50 p-2 lg:p-3 rounded-xl italic">{{ $consultation->complaint_text ?? 'No complaint recorded' }}</p>
        </div>

        <div class="bg-amber-50 p-4 lg:p-6 rounded-xl lg:rounded-2xl border border-amber-100">
            <h3 class="font-bold text-amber-800 mb-2 lg:mb-3 flex items-center gap-2 text-sm lg:text-base">⚡ Common Cases</h3>
            <div class="space-y-2">
                <button @click="$dispatch('set-diagnosis-query', { query: 'URI' })" class="w-full text-left px-3 py-2 rounded-lg bg-white hover:bg-amber-100 text-sm font-medium text-gray-700">URI</button>
                <button @click="$dispatch('set-diagnosis-query', { query: 'Hypertension' })" class="w-full text-left px-3 py-2 rounded-lg bg-white hover:bg-amber-100 text-sm font-medium text-gray-700">Hypertension</button>
                <button @click="$dispatch('set-diagnosis-query', { query: 'Diarrhea' })" class="w-full text-left px-3 py-2 rounded-lg bg-white hover:bg-amber-100 text-sm font-medium text-gray-700">Diarrhea</button>
                <button @click="$dispatch('set-diagnosis-query', { query: 'Prenatal Checkup' })" class="w-full text-left px-3 py-2 rounded-lg bg-white hover:bg-amber-100 text-sm font-medium text-gray-700">Prenatal Checkup</button>
            </div>
        </div>
    </div>

    <div class="md:col-span-2 space-y-4 lg:space-y-6">
        <div class="bg-white p-4 lg:p-6 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200">
            <h3 class="font-bold text-base lg:text-lg mb-3 lg:mb-4 text-gray-800 border-b border-gray-100 pb-2 lg:pb-3">🩺 Medical Diagnosis</h3>

            @if(isset($diagnoses) && $diagnoses->count() > 0)
                <div class="mb-4 lg:mb-6 space-y-2">
                    @foreach($diagnoses as $d)
                        <div class="flex justify-between items-center bg-emerald-50 p-2 lg:p-3 rounded-xl border border-emerald-100">
                            <div>
                                <span class="font-bold text-emerald-800 text-xs lg:text-sm">{{ $d->diagnosis_code }}</span>
                                <span class="text-gray-700 ml-2 text-xs lg:text-sm">{{ $d->diagnosis_name }}</span>
                            </div>
                            <span class="text-xs text-gray-500">Saved</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('consultations.diagnosis', $consultation->id) }}" method="POST" x-data="diagnosisSearch()" @set-diagnosis-query.window="setQuery($event.detail.query)" class="space-y-3 lg:space-y-4">
                @csrf
                <div class="relative">
                    <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Search ICD-10 / Disease name <span class="text-red-500">*</span></label>
                    <input type="text" x-model="query" @input.debounce.300ms="search()"
                           placeholder="e.g. Dengue, Hypertension..."
                           class="w-full px-3 lg:px-4 py-2 lg:py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none text-sm" autocomplete="off">
                    <input type="hidden" name="diagnosis_id" x-model="selectedId">
                    <div x-show="results.length > 0" class="absolute z-10 w-full bg-white mt-1 border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto" style="display: none;">
                        <ul>
                            <template x-for="item in results" :key="item.id">
                                <li @click="select(item)" class="px-3 lg:px-4 py-2 lg:py-2.5 hover:bg-sky-50 cursor-pointer border-b last:border-0 text-xs lg:text-sm">
                                    <span class="font-medium text-gray-800" x-text="item.text"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <div>
                    <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Remarks (optional)</label>
                    <textarea name="remarks" rows="2" class="w-full px-3 lg:px-4 py-2 rounded-xl border border-gray-300 focus:ring-sky-500 focus:border-sky-500 text-sm" placeholder="Additional notes..."></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" :disabled="!selectedId" class="px-4 lg:px-5 py-2 lg:py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs lg:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Add Diagnosis
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white p-4 lg:p-6 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200">
            <h3 class="font-bold text-base lg:text-lg mb-3 lg:mb-4 text-gray-800 border-b border-gray-100 pb-2 lg:pb-3">💊 Prescription (Rx)</h3>

            @if(isset($prescriptions) && $prescriptions->count() > 0)
                <div class="mb-4 lg:mb-6 overflow-hidden rounded-xl border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs lg:text-sm">
                            <thead class="bg-gray-50 text-gray-600 text-left">
                                <tr>
                                    <th class="px-2 lg:px-4 py-2 lg:py-3 font-semibold whitespace-nowrap">Medicine</th>
                                    <th class="px-2 lg:px-4 py-2 lg:py-3 font-semibold whitespace-nowrap">Dosage</th>
                                    <th class="px-2 lg:px-4 py-2 lg:py-3 font-semibold whitespace-nowrap hidden sm:table-cell">Duration</th>
                                    <th class="px-2 lg:px-4 py-2 lg:py-3 font-semibold text-center w-16 lg:w-20 whitespace-nowrap">Qty</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($prescriptions as $rx)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-2 lg:px-4 py-2 lg:py-3 font-medium text-gray-800">{{ $rx->medicine_name }}</td>
                                        <td class="px-2 lg:px-4 py-2 lg:py-3 text-gray-700">
                                            <div>{{ $rx->dosage }}</div>
                                            @if($rx->frequency)
                                                <div class="text-xs text-gray-500 sm:hidden">{{ $rx->frequency }}</div>
                                            @endif
                                        </td>
                                        <td class="px-2 lg:px-4 py-2 lg:py-3 text-gray-700 hidden sm:table-cell">{{ $rx->duration ?? '—' }}</td>
                                        <td class="px-2 lg:px-4 py-2 lg:py-3 text-center">{{ $rx->quantity ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <form action="{{ route('consultations.prescription', $consultation->id) }}" method="POST" x-data="medicineSearch()" class="space-y-3 lg:space-y-4">
                @csrf
                <div class="relative">
                    <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Search medicine</label>
                    <input type="text" x-model="query" @input.debounce.300ms="search()"
                           placeholder="e.g. Paracetamol, Amoxicillin..."
                           class="w-full px-3 lg:px-4 py-2 lg:py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none text-sm" autocomplete="off">
                    <input type="hidden" name="medicine_id" x-model="selectedId">
                    <div x-show="results.length > 0" class="absolute z-10 w-full bg-white mt-1 border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto" style="display: none;">
                        <ul>
                            <template x-for="item in results" :key="item.id">
                                <li @click="select(item)" class="px-3 lg:px-4 py-2 lg:py-2.5 hover:bg-sky-50 cursor-pointer border-b last:border-0 text-xs lg:text-sm">
                                    <span class="font-medium text-gray-800" x-text="item.text"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4">
                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Sig. / Dosage <span class="text-red-500">*</span></label>
                        <input type="text" name="dosage" value="{{ old('dosage') }}" placeholder="e.g. 1 tab 3x a day" class="w-full px-3 lg:px-4 py-2 rounded-xl border border-gray-300 focus:ring-sky-500 focus:border-sky-500 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Frequency (optional)</label>
                        <input type="text" name="frequency" value="{{ old('frequency') }}" placeholder="e.g. After meals" class="w-full px-3 lg:px-4 py-2 rounded-xl border border-gray-300 focus:ring-sky-500 focus:border-sky-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Duration (optional)</label>
                        <input type="text" name="duration" value="{{ old('duration') }}" placeholder="e.g. 7 days" class="w-full px-3 lg:px-4 py-2 rounded-xl border border-gray-300 focus:ring-sky-500 focus:border-sky-500 text-sm">
                    </div>
                </div>
                <div class="max-w-xs">
                    <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Quantity (optional)</label>
                    <input type="number" name="quantity" value="{{ old('quantity') }}" min="1" placeholder="e.g. 30" class="w-full px-3 lg:px-4 py-2 rounded-xl border border-gray-300 focus:ring-sky-500 focus:border-sky-500 text-sm">
                </div>
                <div class="flex justify-end">
                    <button type="submit" :disabled="!selectedId" class="px-4 lg:px-5 py-2 lg:py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs lg:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        + Add prescription
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function diagnosisSearch() {
        return {
            query: '',
            results: [],
            selectedId: null,
            async search() {
                if (this.query.length < 2) { this.results = []; return; }
                const response = await fetch('/search/diagnoses?query=' + encodeURIComponent(this.query));
                this.results = await response.json();
            },
            select(item) {
                this.query = item.text;
                this.selectedId = item.id;
                this.results = [];
            },
            setQuery(term) {
                this.query = term;
                this.search();
            }
        };
    }
    function medicineSearch() {
        return {
            query: '',
            results: [],
            selectedId: null,
            async search() {
                if (this.query.length < 2) { this.results = []; return; }
                const response = await fetch('/search/medicines?query=' + encodeURIComponent(this.query));
                this.results = await response.json();
            },
            select(item) {
                this.query = item.text;
                this.selectedId = item.id;
                this.results = [];
            }
        };
    }
</script>
@endsection
