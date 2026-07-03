<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iClinicSys Handout — C{{ str_pad($consultation->id, 4, '0', STR_PAD_LEFT) }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Source Sans 3"', 'Segoe UI', 'Tahoma', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Source Sans 3', 'Segoe UI', Tahoma, sans-serif;
            background: #e8e4dc;
        }

        .iclinic-form {
            font-family: Arial, 'Source Sans 3', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        .iclinic-sheet {
            width: 8.5in;
            max-width: 100%;
            margin-left: auto;
            margin-right: auto;
        }

        @media print {
            @page {
                size: letter portrait;
                margin: 0.35in;
            }

            body {
                background: #fff !important;
                padding: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .iclinic-sheet {
                width: 100%;
                max-width: none;
                box-shadow: none !important;
                page-break-after: always;
            }

            .iclinic-sheet:last-child {
                page-break-after: auto;
            }

            .iclinic-form button {
                pointer-events: none;
            }
        }
    </style>
</head>
<body class="text-black antialiased" x-data="{ showEnrollment: true, showItr: true }">
    {{-- Screen toolbar (hidden when printing) --}}
    <div class="no-print sticky top-0 z-10 border-b border-gray-300 bg-white/95 backdrop-blur px-4 py-3">
        <div class="max-w-5xl mx-auto flex flex-wrap items-center gap-2 justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-800">iClinicSys Consultation Handout</p>
                <p class="text-xs text-gray-500">
                    C{{ str_pad($consultation->id, 4, '0', STR_PAD_LEFT) }} ·
                    {{ $patient->last_name ?? '' }}, {{ $patient->first_name ?? '' }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <label class="inline-flex items-center gap-1.5 text-xs text-gray-700 cursor-pointer">
                    <input type="checkbox" class="rounded border-gray-400" x-model="showEnrollment">
                    Form 1 — Patient Enrollment
                </label>
                <label class="inline-flex items-center gap-1.5 text-xs text-gray-700 cursor-pointer">
                    <input type="checkbox" class="rounded border-gray-400" x-model="showItr">
                    Form 2 — Individual Treatment Record
                </label>
                <button type="button"
                        onclick="window.print()"
                        class="rounded-lg bg-emerald-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-800 transition">
                    Print handout
                </button>
                <a href="{{ route('consultations.show', $consultation->id) }}"
                   class="rounded-lg border border-emerald-900 px-3 py-1.5 text-xs font-semibold text-emerald-900 hover:bg-emerald-50 transition">
                    Back to consultation
                </a>
            </div>
        </div>
    </div>

  <main class="py-4 px-3 space-y-6">
        {{--
            Section 1: Patient Enrollment (FORM 1)
            Separate partial — consultations/handout/partials/patient-enrollment.blade.php
        --}}
        <div class="iclinic-sheet shadow-md" x-show="showEnrollment" x-cloak>
            @include('consultations.handout.partials.patient-enrollment', [
                'patient' => $patient,
                'age' => $age,
            ])
        </div>

        {{--
            Section 2: Individual Treatment Record (FORM 2)
            Separate partial — consultations/handout/partials/itr.blade.php
        --}}
        <div class="iclinic-sheet shadow-md" x-show="showItr" x-cloak>
            @include('consultations.handout.partials.itr', [
                'patient' => $patient,
                'consultation' => $consultation,
                'diagnoses' => $diagnoses,
                'prescriptions' => $prescriptions,
                'vitals' => $vitals,
                'labRequests' => $labRequests,
                'age' => $age,
                'consultationAt' => $consultationAt,
                'attendingProvider' => $attendingProvider,
            ])
        </div>
    </main>
</body>
</html>
