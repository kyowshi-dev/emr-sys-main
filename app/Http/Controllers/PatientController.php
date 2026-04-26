<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    // 1. List all patients
    public function index()
    {
        $this->authorize('viewAny', Patient::class);

        $patients = DB::table('patients')
            ->join('households', 'patients.household_id', '=', 'households.id')
            ->leftJoinSub(
                DB::table('consultations')
                    ->select('patient_id', DB::raw('MAX(created_at) as last_visit'))
                    ->groupBy('patient_id'),
                'latest_consultations',
                function ($join) {
                    $join->on('patients.id', '=', 'latest_consultations.patient_id');
                }
            )
            ->select(
                'patients.*',
                'households.family_name_head',
                'households.zone_id',
                'households.contact_number',
                'latest_consultations.last_visit'
            )
            ->orderByDesc('patients.created_at')
            ->paginate(20)
            ->withQueryString();

        return view('patients.index', compact('patients'));
    }

    // 2. Show the Registration Form
    public function create(Request $request)
    {
        $this->authorize('create', Patient::class);

        $selectedHouseholdId = $request->old('household_id') ?? $request->input('household_id');

        $transientHousehold = DB::table('households')
            ->where(function ($qb) {
                $qb->whereRaw('LOWER(family_name_head) LIKE ?', ['%transient%'])
                    ->orWhereRaw('LOWER(family_name_head) LIKE ?', ['%unmapped%']);
            })
            ->select(['id', 'family_name_head'])
            ->first();

        $selectedHousehold = null;
        if (! empty($selectedHouseholdId)) {
            $selectedHousehold = DB::table('households')
                ->join('zones', 'households.zone_id', '=', 'zones.id')
                ->select('households.id', 'households.family_name_head', 'zones.zone_number', 'households.contact_number')
                ->where('households.id', $selectedHouseholdId)
                ->first();
        }

        return view('patients.create', [
            'selectedHouseholdId' => $selectedHouseholdId,
            'transientHouseholdId' => $transientHousehold?->id,
            'transientHouseholdLabel' => $transientHousehold?->family_name_head,
            'selectedHousehold' => $selectedHousehold,
        ]);
    }

    // 3. Save the New Patient
    public function store(Request $request)
    {
        $this->authorize('create', Patient::class);

        // --- 1. ENHANCED VALIDATION ---
        $validated = $request->validate([
            'household_id' => 'required|exists:households,id', // Still required for now

            // Name: Only letters, spaces, dots, and dashes. No numbers.
            'first_name' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[a-zA-Z\s\-\.]+$/'],
            'last_name' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[a-zA-Z\s\-\.]+$/'],
            'middle_name' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z\s\-\.]+$/'],

            'sex' => 'required|in:Male,Female',

            // Birthdate: Must be a valid date and NOT in the future
            'date_of_birth' => 'required|date|before:today',

            'civil_status' => 'required|in:Single,Married,Widowed,Separated,Common Law',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'educational_attainment' => 'nullable|string',
            'employment_status' => 'nullable|string|max:100',
        ], [
            // Custom Error Messages
            'first_name.regex' => 'First name cannot contain numbers or special symbols.',
            'date_of_birth.before' => 'Birth date cannot be in the future.',
            'household_id.required' => 'You must assign a household. If none exists, register the household first.',
        ]);

        // --- 2. DUPLICATE CHECK ---
        // Prevents double-entry of the same person
        $exists = DB::table('patients')
            ->where('first_name', $request->first_name)
            ->where('last_name', $request->last_name)
            ->where('date_of_birth', $request->date_of_birth)
            ->exists();

        if ($exists) {
            // Returns the user to the form with their input intact
            return back()->withInput()->withErrors(['first_name' => 'This patient is already registered in the system!']);
        }

        // --- 3. INSERT DATA (Sanitized) ---
        DB::table('patients')->insert([
            'household_id' => $request->household_id,
            // Auto-Capitalize Names
            'first_name' => ucwords(strtolower($request->first_name)),
            'last_name' => ucwords(strtolower($request->last_name)),
            'middle_name' => $request->middle_name ? ucwords(strtolower($request->middle_name)) : null,

            'suffix' => $request->suffix,
            'sex' => $request->sex,
            'date_of_birth' => $request->date_of_birth,
            'birth_place' => $request->birth_place,
            'blood_type' => $request->blood_type,
            'civil_status' => $request->civil_status,
            'educational_attainment' => $request->educational_attainment,
            'employment_status' => $request->employment_status,

            'has_4ps' => $request->has('has_4ps') ? 1 : 0,
            'has_nhts' => $request->has('has_nhts') ? 1 : 0,

            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('patients.index')->with('success', 'Patient registered successfully!');
    }

    // 4. View Single Patient Profile
    public function show($id)
    {
        $patient = Patient::findOrFail($id);

        $this->authorize('view', $patient);

        $patient = DB::table('patients')
            ->join('households', 'patients.household_id', '=', 'households.id')
            ->where('patients.id', $id)
            ->select('patients.*', 'households.family_name_head', 'households.zone_id')
            ->first();

        // 2. Calculate Age
        $patient->age = Carbon::parse($patient->date_of_birth)->age;

        // 3. Load Consultations (History) – worker_id is health_workers.id
        $history = DB::table('consultations')
            ->leftJoin('health_workers', 'consultations.worker_id', '=', 'health_workers.id')
            ->where('patient_id', $id)
            ->select(
                'consultations.*',
                DB::raw($this->dbConcat(['health_workers.first_name', 'health_workers.last_name']).' as worker_name'),
                'consultations.nature_of_visit as complaint_name'
            )
            ->orderByDesc('consultations.created_at')
            ->get();

        $immunizationCount = DB::table('immunization_records')->where('patient_id', $id)->count();

        return view('patients.show', compact('patient', 'history', 'immunizationCount'));
    }
}
