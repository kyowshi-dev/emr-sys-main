<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImmunizationController extends Controller
{
    public function index()
    {
        // Check authorization
        if (! auth()->user()->hasRole('Admin', 'Nurse', 'BHW')) {
            abort(403, 'Unauthorized');
        }

        $recentRecords = DB::table('immunization_records')
            ->join('patients', 'immunization_records.patient_id', '=', 'patients.id')
            ->join('vaccines_lookup', 'immunization_records.vaccine_id', '=', 'vaccines_lookup.id')
            ->leftJoin('health_workers', 'immunization_records.administered_by', '=', 'health_workers.id')
            ->select(
                'immunization_records.id',
                'immunization_records.patient_id',
                'immunization_records.date_given',
                'immunization_records.dose_number',
                'patients.first_name',
                'patients.last_name',
                'vaccines_lookup.vaccine_name',
                DB::raw("CONCAT(health_workers.first_name, ' ', health_workers.last_name) as worker_name")
            )
            ->orderByDesc('immunization_records.date_given')
            ->limit(20)
            ->get();

        $totalGiven = DB::table('immunization_records')->count();
        $patientsWithRecords = DB::table('immunization_records')->distinct('patient_id')->count('patient_id');

        return view('immunizations.index', [
            'recentRecords' => $recentRecords,
            'totalGiven' => $totalGiven,
            'patientsWithRecords' => $patientsWithRecords,
        ]);
    }

    public function forPatient($id)
    {
        // Check authorization
        if (! auth()->user()->hasRole('Admin', 'Nurse', 'BHW')) {
            abort(403, 'Unauthorized');
        }

        $patient = DB::table('patients')
            ->join('households', 'patients.household_id', '=', 'households.id')
            ->where('patients.id', $id)
            ->select('patients.*', 'households.family_name_head', 'households.zone_id')
            ->first();

        if (! $patient) {
            abort(404, 'Patient not found');
        }

        $patient->age = \Carbon\Carbon::parse($patient->date_of_birth)->age;

        $isChild = $patient->age < 18;
        $allowedCategories = $isChild ? ['Child', 'Both'] : ['Adult', 'Both'];

        $records = DB::table('immunization_records')
            ->join('vaccines_lookup', 'immunization_records.vaccine_id', '=', 'vaccines_lookup.id')
            ->leftJoin('health_workers', 'immunization_records.administered_by', '=', 'health_workers.id')
            ->where('immunization_records.patient_id', $id)
            ->select(
                'immunization_records.*',
                'vaccines_lookup.vaccine_name',
                'vaccines_lookup.vaccine_code',
                DB::raw("CONCAT(health_workers.first_name, ' ', health_workers.last_name) as administered_by_name")
            )
            ->orderByDesc('immunization_records.date_given')
            ->get();

        $vaccines = DB::table('vaccines_lookup')
            ->whereIn('category', $allowedCategories)
            ->orderBy('sort_order')
            ->get();
        $healthWorkers = DB::table('health_workers')
            ->orderBy('last_name')
            ->get();

        return view('immunizations.patient', [
            'patient' => $patient,
            'records' => $records,
            'vaccines' => $vaccines,
            'healthWorkers' => $healthWorkers,
        ]);
    }

    public function store(Request $request)
    {
        // Check authorization
        if (! auth()->user()->hasRole('Admin', 'Nurse', 'BHW')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'vaccine_id' => ['required', 'integer', 'exists:vaccines_lookup,id'],
            'dose_number' => ['required', 'integer', 'min:1', 'max:99'],
            'date_given' => ['required', 'date', 'before_or_equal:today'],
            'administered_by' => ['nullable', 'integer', 'exists:health_workers,id'],
            'next_due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'date_given.before_or_equal' => 'Date given cannot be in the future.',
        ]);

        // Get patient and vaccine for age check
        $patient = DB::table('patients')->where('id', $validated['patient_id'])->first();
        $age = \Carbon\Carbon::parse($patient->date_of_birth)->age;
        $isChild = $age < 18;
        $allowedCategories = $isChild ? ['Child', 'Both'] : ['Adult', 'Both'];

        $vaccine = DB::table('vaccines_lookup')->where('id', $validated['vaccine_id'])->first();
        if (! in_array($vaccine->category, $allowedCategories)) {
            return back()
                ->withErrors(['vaccine_id' => 'This vaccine is not appropriate for the patient\'s age group.'])
                ->withInput();
        }

        DB::table('immunization_records')->insert([
            'patient_id' => $validated['patient_id'],
            'vaccine_id' => $validated['vaccine_id'],
            'dose_number' => $validated['dose_number'],
            'date_given' => $validated['date_given'],
            'administered_by' => $validated['administered_by'] ?? null,
            'next_due_date' => $validated['next_due_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('immunizations.patient', $validated['patient_id'])
            ->with('success', 'Immunization record saved.');
    }
}
