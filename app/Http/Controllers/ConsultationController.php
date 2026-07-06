<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConsultationController extends Controller
{
    private ?bool $supportsVersionedVitals = null;

    public function index(Request $request)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        $query = DB::table('consultations')
            ->join('patients', 'consultations.patient_id', '=', 'patients.id')
            ->join('health_workers', 'consultations.worker_id', '=', 'health_workers.id')
            ->select(
                'consultations.id',
                'consultations.patient_id',
                'consultations.status',
                'consultations.created_at',
                'patients.first_name as patient_first_name',
                'patients.last_name as patient_last_name',
                'health_workers.first_name as worker_first_name',
                'health_workers.last_name as worker_last_name'
            );

        // Apply sorting based on sort parameter
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('consultations.created_at');
                break;
            case 'patient_name':
                $query->orderBy('patients.last_name')
                    ->orderBy('patients.first_name');
                break;
            case 'status':
                $query->orderBy('consultations.status')
                    ->orderByDesc('consultations.created_at');
                break;
            case 'newest':
            default:
                $query->orderByDesc('consultations.created_at');
                break;
        }

        if ($request->filled('query')) {
            $q = $request->input('query');
            $query->where(function ($qb) use ($q) {
                $qb->where('patients.first_name', 'like', '%'.$q.'%')
                    ->orWhere('patients.last_name', 'like', '%'.$q.'%')
                    ->orWhereRaw($this->dbConcat(['patients.last_name', 'patients.first_name'], ', ').' LIKE ?', ['%'.$q.'%']);
                if (is_numeric($q)) {
                    $qb->orWhere('patients.id', (int) $q);
                }
                if (preg_match('/^PT\s*(\d+)$/i', trim($q), $m)) {
                    $qb->orWhere('patients.id', (int) $m[1]);
                }
                $qb->orWhereExists(function ($ex) use ($q) {
                    $ex->select(DB::raw(1))
                        ->from('diagnosis_records')
                        ->leftJoin('diagnosis_lookup', 'diagnosis_records.diagnosis_id', '=', 'diagnosis_lookup.id')
                        ->whereColumn('diagnosis_records.consultation_id', 'consultations.id')
                        ->where(function ($dx) use ($q) {
                            $dx->where('diagnosis_lookup.diagnosis_name', 'like', '%'.$q.'%')
                                ->orWhere('diagnosis_records.custom_diagnosis_name', 'like', '%'.$q.'%');
                        });
                });
            });
        }

        if ($request->filled('date_from')) {
            $parsed = Carbon::createFromFormat('d/m/Y', trim($request->input('date_from')));
            if ($parsed !== false) {
                $query->where('consultations.created_at', '>=', $parsed->copy()->startOfDay());
            }
        }
        if ($request->filled('date_to')) {
            $parsed = Carbon::createFromFormat('d/m/Y', trim($request->input('date_to')));
            if ($parsed !== false) {
                $query->where('consultations.created_at', '<=', $parsed->copy()->endOfDay());
            }
        }

        $consultations = $query->paginate(15)->withQueryString();

        $consultationIds = $consultations->pluck('id')->toArray();

        $diagnosisByConsultation = [];
        $treatmentByConsultation = [];
        if (! empty($consultationIds)) {
            $diagnosisRows = $this->diagnosisRecordsQuery()
                ->whereIn('diagnosis_records.consultation_id', $consultationIds)
                ->select(
                    'diagnosis_records.consultation_id',
                    DB::raw('COALESCE(diagnosis_lookup.diagnosis_name, diagnosis_records.custom_diagnosis_name) as diagnosis_name'),
                    'diagnosis_records.remarks'
                )
                ->orderBy('diagnosis_records.id')
                ->get();
            foreach ($diagnosisRows as $row) {
                $diagnosisByConsultation[$row->consultation_id][] = trim($row->diagnosis_name.($row->remarks ? ' - '.$row->remarks : ''));
            }

            $prescriptionRows = $this->prescriptionsQuery()
                ->whereIn('prescriptions.consultation_id', $consultationIds)
                ->select(
                    'prescriptions.consultation_id',
                    DB::raw('COALESCE(medicines_lookup.medicine_name, prescriptions.custom_medicine_name) as medicine_name'),
                    'prescriptions.dosage',
                    'prescriptions.duration'
                )
                ->get();
            foreach ($prescriptionRows as $row) {
                $treatmentByConsultation[$row->consultation_id][] = $row->medicine_name.($row->dosage ? ' '.$row->dosage : '').($row->duration ? ', '.$row->duration : '');
            }
        }

        $totalConsultations = DB::table('consultations')->count();
        $thisWeekCount = DB::table('consultations')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $completedCount = DB::table('consultations')->where('status', 'completed')->count();

        return view('consultations.index', [
            'consultations' => $consultations,
            'diagnosisByConsultation' => $diagnosisByConsultation,
            'treatmentByConsultation' => $treatmentByConsultation,
            'totalConsultations' => $totalConsultations,
            'thisWeekCount' => $thisWeekCount,
            'completedCount' => $completedCount,
            'currentSort' => $sort,
        ]);
    }

    public function liveRequests(Request $request)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $consultation = DB::table('consultations')
            ->where('status', 'pending_doctor')
            ->orderByDesc('created_at')
            ->first();

        if (! $consultation) {
            return response()->json(['hasRequest' => false]);
        }

        $patient = DB::table('patients')->where('id', $consultation->patient_id)->first();
        $worker = DB::table('health_workers')->where('id', $consultation->worker_id)->first();

        if (! $patient || ! $worker) {
            return response()->json(['hasRequest' => false]);
        }

        return response()->json([
            'hasRequest' => true,
            'request' => [
                'id' => $consultation->id,
                'open_url' => route('consultations.show', ['id' => $consultation->id]),
                'clinic_name' => 'Santa Ana Health Center',
                'worker_name' => trim(($worker->first_name ?? '') . ' ' . ($worker->last_name ?? '')),
                'patient_name' => trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? '')),
                'patient_age' => $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->age : null,
                'patient_gender' => $patient->gender ?? '',
                'chief_complaint' => $consultation->complaint_text ?? $consultation->chief_complaint ?? 'No reason provided',
            ],
        ]);
    }

    // 1. Show the Admission Form (Triage)
    public function create($patientId)
    {
        $patient = Patient::find($patientId);

        if (! $patient) {
            abort(404, 'Patient not found');
        }

        // Calculate age for display
        $patient->age = Carbon::parse($patient->date_of_birth)->age;

        return view('consultations.create', compact('patient'));
    }

    // 2. Save the Data (Triage Save)
    public function store(Request $request, $patientId)
    {
        $validated = $request->validate([
            'mode_of_transaction' => ['required', 'string', 'max:255'],
            'referred_from' => ['nullable', 'string', 'max:255'],
            'nature_of_visit' => ['required', 'string', 'max:255'],
            'chief_complaint' => ['nullable', 'string', 'max:1000'],
            'bp_systolic' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'bp_diastolic' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'temperature' => ['required', 'numeric', 'min:30', 'max:45'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'refer_to_higher_facility' => ['nullable', 'boolean'],
            'referred_to' => ['nullable', 'string', 'max:255'],
            'referral_reason' => ['nullable', 'string', 'max:1000'],
        ], [
            'temperature.required' => 'Temperature is required.',
            'temperature.min' => 'Temperature must be at least 30°C.',
            'temperature.max' => 'Temperature must not exceed 45°C.',
        ]);

        $workerId = DB::table('health_workers')
            ->where('user_id', Auth::id())
            ->value('id');

        if ($workerId === null) {
            abort(403, 'No health worker profile is linked to this user.');
        }

        DB::transaction(function () use ($validated, $patientId, $workerId) {
            $consultationId = DB::table('consultations')->insertGetId([
                'patient_id' => $patientId,
                'worker_id' => $workerId,
                'status' => 'pending_doctor',
                'nature_of_visit' => $validated['nature_of_visit'],
                'mode_of_transaction' => $validated['mode_of_transaction'],
                'referred_from' => $validated['referred_from'] ?? null,
                'refer_to_higher_facility' => $validated['refer_to_higher_facility'] ?? false,
                'referred_to' => $validated['referred_to'] ?? null,
                'referral_reason' => $validated['referral_reason'] ?? null,
                'chief_complaint_id' => null,
                'complaint_text' => $validated['chief_complaint'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $vitalsPayload = [
                'consultation_id' => $consultationId,
                'bp_systolic' => $validated['bp_systolic'] ?? null,
                'bp_diastolic' => $validated['bp_diastolic'] ?? null,
                'weight_kg' => $validated['weight'] ?? null,
                'height_cm' => $validated['height'] ?? null,
                'temperature_c' => $validated['temperature'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($this->vitalsSupportVersioning()) {
                $vitalsPayload['phase'] = 'triage';
                $vitalsPayload['captured_by'] = $workerId;
            }

            DB::table('vitals')->insert($vitalsPayload);
        });

        return redirect()->route('patients.show', $patientId)
            ->with('success', 'Consultation started. Patient is in the doctor queue.');
    }

    // 3. Show the Doctor's Workspace (View Consultation)
    public function show($id)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        $consultation = DB::table('consultations')
            ->leftJoin('health_workers', 'consultations.worker_id', '=', 'health_workers.id')
            ->where('consultations.id', $id)
            ->select(
                'consultations.*',
                'health_workers.first_name as worker_first_name',
                'health_workers.last_name as worker_last_name'
            )
            ->first();

        if (! $consultation) {
            abort(404, 'Resource not found');
        }

        $patient = DB::table('patients')->find($consultation->patient_id);

        $vitalsQuery = DB::table('vitals')
            ->where('vitals.consultation_id', $id)
            ->orderBy('vitals.created_at')
            ->orderBy('vitals.id');

        if ($this->vitalsSupportVersioning()) {
            $vitalsQuery
                ->leftJoin('health_workers', 'vitals.captured_by', '=', 'health_workers.id')
                ->select(
                    'vitals.*',
                    'health_workers.first_name as captured_by_first_name',
                    'health_workers.last_name as captured_by_last_name',
                    'health_workers.role as captured_by_role'
                );
        } else {
            $vitalsQuery->select('vitals.*');
        }

        $allVitals = $vitalsQuery->get();

        $triageVitals = $this->vitalsSupportVersioning()
            ? ($allVitals->firstWhere('phase', 'triage') ?? $allVitals->first())
            : $allVitals->first();
        $latestVitals = $allVitals->last();
        $vitals = $latestVitals;
        if (! $vitals) {
            $vitals = (object) [
                'bp_systolic' => null,
                'bp_diastolic' => null,
                'temperature_c' => null,
                'weight_kg' => null,
                'height_cm' => null,
                'phase' => 'triage',
            ];
        }

        $currentUserRole = DB::table('health_workers')
            ->where('user_id', Auth::id())
            ->value('role');

        $canReferExternally = in_array(strtolower((string) $currentUserRole), ['doctor', 'nurse'], true);
        $canAcknowledgeIntake = strtolower((string) $currentUserRole) === 'nurse';

        // 2. Fetch Existing Records (History)
        $existingDiagnoses = $this->diagnosisRecordsQuery()
            ->where('diagnosis_records.consultation_id', $id)
            ->select(
                'diagnosis_records.*',
                DB::raw('COALESCE(diagnosis_lookup.diagnosis_code, diagnosis_records.custom_diagnosis_code) as diagnosis_code'),
                DB::raw('COALESCE(diagnosis_lookup.diagnosis_name, diagnosis_records.custom_diagnosis_name) as diagnosis_name'),
                DB::raw('(diagnosis_records.diagnosis_id IS NULL) as is_custom')
            )
            ->get();

        $existingPrescriptions = $this->prescriptionsQuery()
            ->where('prescriptions.consultation_id', $id)
            ->select(
                'prescriptions.*',
                DB::raw('COALESCE(medicines_lookup.medicine_name, prescriptions.custom_medicine_name) as medicine_name'),
                DB::raw('(prescriptions.medicine_id IS NULL) as is_custom')
            )
            ->get();

        // 3. NEW: Fetch Dropdown Options (The "Menu" for the Doctor)
        $diagnosisOptions = DB::table('diagnosis_lookup')->orderBy('diagnosis_name')->get();
        $medicineOptions = DB::table('medicines_lookup')->orderBy('medicine_name')->get();

        return view('consultations.show', [
            'consultation' => $consultation,
            'patient' => $patient,
            'vitals' => $vitals,
            'triageVitals' => $triageVitals,
            'latestVitals' => $latestVitals,
            'allVitals' => $allVitals,
            'diagnoses' => $existingDiagnoses,
            'prescriptions' => $existingPrescriptions,
            'diagnosisOptions' => $diagnosisOptions,
            'medicineOptions' => $medicineOptions,
            'canReferExternally' => $canReferExternally,
            'canAcknowledgeIntake' => $canAcknowledgeIntake,
        ]);
    }

    public function acknowledgeIntake($id)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        $worker = DB::table('health_workers')->where('user_id', Auth::id())->first();
        if ($worker === null || strtolower((string) $worker->role) !== 'nurse') {
            abort(403, 'Only nurses can acknowledge intake.');
        }

        $consultation = DB::table('consultations')->where('id', $id)->first();
        if (! $consultation) {
            abort(404, 'Consultation not found');
        }

        if ($consultation->status !== 'pending_validation') {
            return redirect()->back()->withErrors([
                'intake' => 'This consultation is not awaiting nurse validation.',
            ]);
        }

        $updates = [
            'status' => 'pending_doctor',
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('consultations', 'nurse_validated_at')) {
            $updates['nurse_validated_at'] = now();
            $updates['nurse_validated_by'] = $worker->id;
        }

        DB::table('consultations')->where('id', $id)->update($updates);

        return redirect()->route('consultations.show', $id)
            ->with('success', 'Intake acknowledged. Patient is now in the doctor queue.');
    }

    public function printHandout($id)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        if (! auth()->user()->canPrintHandout()) {
            abort(403, 'You do not have permission to print consultation handouts.');
        }

        $consultation = DB::table('consultations')
            ->leftJoin('health_workers', 'consultations.worker_id', '=', 'health_workers.id')
            ->where('consultations.id', $id)
            ->select(
                'consultations.*',
                'health_workers.first_name as worker_first_name',
                'health_workers.last_name as worker_last_name'
            )
            ->first();

        if (! $consultation) {
            abort(404, 'Consultation not found');
        }

        if (! in_array($consultation->status, ['completed', 'referred'], true)) {
            abort(403, 'Print handout is available only for completed consultations.');
        }

        $patient = DB::table('patients')
            ->join('households', 'patients.household_id', '=', 'households.id')
            ->leftJoin('zones', 'households.zone_id', '=', 'zones.id')
            ->where('patients.id', $consultation->patient_id)
            ->select(
                'patients.*',
                'households.contact_number as household_contact_number',
                'households.id as household_record_id',
                'zones.zone_number'
            )
            ->first();

        $vitals = DB::table('vitals')
            ->where('consultation_id', $id)
            ->orderByDesc('id')
            ->first();

        $labRequests = DB::table('lab_requests')
            ->where('consultation_id', $id)
            ->orderBy('id')
            ->get();

        $diagnoses = $this->diagnosisRecordsQuery()
            ->where('diagnosis_records.consultation_id', $id)
            ->select(
                DB::raw('COALESCE(diagnosis_lookup.diagnosis_name, diagnosis_records.custom_diagnosis_name) as diagnosis_name'),
                DB::raw('COALESCE(diagnosis_lookup.diagnosis_code, diagnosis_records.custom_diagnosis_code) as diagnosis_code'),
                'diagnosis_records.remarks'
            )
            ->orderBy('diagnosis_records.id')
            ->get();

        $prescriptions = $this->prescriptionsQuery()
            ->where('prescriptions.consultation_id', $id)
            ->select(
                DB::raw('COALESCE(medicines_lookup.medicine_name, prescriptions.custom_medicine_name) as medicine_name'),
                'prescriptions.dosage',
                'prescriptions.frequency',
                'prescriptions.duration',
                'prescriptions.quantity'
            )
            ->orderBy('prescriptions.id')
            ->get();

        $age = $patient ? Carbon::parse($patient->date_of_birth)->age : null;
        $zoneLabel = $patient?->zone_number ? 'Zone '.$patient->zone_number : null;

        $consultationAt = Carbon::parse($consultation->updated_at ?? $consultation->created_at);
        $attendingProvider = trim(($consultation->worker_first_name ?? '').' '.($consultation->worker_last_name ?? '')) ?: null;

        return view('consultations.handout', [
            'consultation' => $consultation,
            'patient' => $patient,
            'diagnoses' => $diagnoses,
            'prescriptions' => $prescriptions,
            'vitals' => $vitals,
            'labRequests' => $labRequests,
            'age' => $age,
            'zoneLabel' => $zoneLabel,
            'consultationAt' => $consultationAt,
            'attendingProvider' => $attendingProvider,
        ]);
    }

    public function retakeVitals(Request $request, $id)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'bp_systolic' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'bp_diastolic' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'temperature' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $consultation = DB::table('consultations')->where('id', $id)->first();
        if (! $consultation) {
            abort(404, 'Consultation not found');
        }

        $workerId = DB::table('health_workers')
            ->where('user_id', Auth::id())
            ->value('id');

        if ($workerId === null) {
            abort(403, 'No health worker profile is linked to this user.');
        }

        $vitalsPayload = [
            'consultation_id' => $id,
            'bp_systolic' => $validated['bp_systolic'] ?? null,
            'bp_diastolic' => $validated['bp_diastolic'] ?? null,
            'weight_kg' => $validated['weight'] ?? null,
            'height_cm' => $validated['height'] ?? null,
            'temperature_c' => $validated['temperature'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($this->vitalsSupportVersioning()) {
            $vitalsPayload['phase'] = 'clinical';
            $vitalsPayload['captured_by'] = $workerId;
            $vitalsPayload['notes'] = $validated['notes'] ?? null;
        }

        DB::table('vitals')->insert($vitalsPayload);

        DB::table('consultations')
            ->where('id', $id)
            ->update(['status' => 'in_progress', 'updated_at' => now()]);

        return redirect()->route('consultations.show', $id)
            ->with('success', 'Clinical vitals saved as a new version.');
    }

    public function updateVitalVersion(Request $request, $consultationId, $vitalId)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'bp_systolic' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'bp_diastolic' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'temperature' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $vital = DB::table('vitals')
            ->where('id', $vitalId)
            ->where('consultation_id', $consultationId)
            ->first();

        if (! $vital) {
            abort(404, 'Vitals version not found for this consultation.');
        }

        $updatePayload = [
            'bp_systolic' => $validated['bp_systolic'] ?? null,
            'bp_diastolic' => $validated['bp_diastolic'] ?? null,
            'weight_kg' => $validated['weight'] ?? null,
            'height_cm' => $validated['height'] ?? null,
            'temperature_c' => $validated['temperature'] ?? null,
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('vitals', 'notes')) {
            $updatePayload['notes'] = $validated['notes'] ?? null;
        }

        DB::table('vitals')
            ->where('id', $vitalId)
            ->where('consultation_id', $consultationId)
            ->update($updatePayload);

        return redirect()->route('consultations.show', $consultationId)
            ->with('success', 'Vitals version updated successfully.');
    }

    public function deleteVitalVersion($consultationId, $vitalId)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        $versions = DB::table('vitals')
            ->where('consultation_id', $consultationId)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        if ($versions->count() <= 1) {
            return redirect()->route('consultations.show', $consultationId)
                ->withErrors(['vitals' => 'Cannot delete the only vitals version.']);
        }

        $vital = $versions->firstWhere('id', (int) $vitalId);
        if (! $vital) {
            abort(404, 'Vitals version not found for this consultation.');
        }

        if ($this->vitalsSupportVersioning() && ($vital->phase ?? null) === 'triage') {
            return redirect()->route('consultations.show', $consultationId)
                ->withErrors(['vitals' => 'Triage baseline vitals cannot be deleted.']);
        }

        DB::table('vitals')
            ->where('id', $vitalId)
            ->where('consultation_id', $consultationId)
            ->delete();

        return redirect()->route('consultations.show', $consultationId)
            ->with('success', 'Vitals version deleted successfully.');
    }

    // 4. Save a Diagnosis (Doctor's Action)
    public function addDiagnosis(Request $request, $id)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'diagnosis_id' => ['nullable', 'integer', 'exists:diagnosis_lookup,id'],
            'custom_diagnosis_code' => ['nullable', 'string', 'max:20'],
            'custom_diagnosis_name' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ]);

        $customName = trim((string) ($validated['custom_diagnosis_name'] ?? ''));
        $hasLookup = ! empty($validated['diagnosis_id']);
        $hasCustom = $customName !== '';

        if ($hasLookup === $hasCustom) {
            return redirect()->back()
                ->withErrors(['diagnosis' => $hasLookup
                    ? 'Choose either a master-list diagnosis or a custom entry, not both.'
                    : 'Select a diagnosis from the list or enter a custom diagnosis name.'])
                ->withInput();
        }

        if ($hasCustom && mb_strlen($customName) < 2) {
            return redirect()->back()
                ->withErrors(['custom_diagnosis_name' => 'Custom diagnosis name must be at least 2 characters.'])
                ->withInput();
        }

        $workerId = DB::table('health_workers')
            ->where('user_id', Auth::id())
            ->value('id');

        if ($workerId === null) {
            abort(403, 'No health worker profile is linked to this user.');
        }

        DB::table('diagnosis_records')->insert([
            'consultation_id' => $id,
            'diagnosis_id' => $hasLookup ? $validated['diagnosis_id'] : null,
            'custom_diagnosis_code' => $hasCustom ? ($validated['custom_diagnosis_code'] ?? null) : null,
            'custom_diagnosis_name' => $hasCustom ? $customName : null,
            'remarks' => $validated['remarks'] ?? null,
            'diagnosed_by' => $workerId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($this->maybeAutoCompleteConsultation((int) $id)) {
            return redirect()->back()->with('success', 'Diagnosis added. Consultation marked as completed.');
        }

        DB::table('consultations')->where('id', $id)->update([
            'status' => 'in_progress',
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Diagnosis added successfully!');
    }

    public function finalizeConsultation(Request $request, $id)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'refer_to_higher_facility' => ['nullable', 'boolean'],
            'referred_to' => ['nullable', 'string', 'max:255'],
            'referral_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $consultation = DB::table('consultations')->where('id', $id)->first();
        if (! $consultation) {
            abort(404, 'Consultation not found');
        }

        $diagnosisCount = DB::table('diagnosis_records')
            ->where('consultation_id', $id)
            ->count();

        if ($diagnosisCount < 1) {
            return redirect()->route('consultations.show', $id)
                ->withErrors(['diagnosis' => 'Add at least one diagnosis before finalizing consultation.']);
        }

        $workerId = DB::table('health_workers')
            ->where('user_id', Auth::id())
            ->value('id');

        if ($workerId === null) {
            abort(403, 'No health worker profile is linked to this user.');
        }

        $updates = [
            'status' => 'completed',
            'updated_at' => now(),
        ];

        $requestedReferral = (bool) ($validated['refer_to_higher_facility'] ?? false);
        if ($requestedReferral) {
            $currentWorkerRole = strtolower((string) DB::table('health_workers')
                ->where('id', $workerId)
                ->value('role'));

            if (! in_array($currentWorkerRole, ['doctor', 'nurse'], true)) {
                return redirect()->back()->withErrors([
                    'refer_to_higher_facility' => 'Only Doctor or Nurse roles can trigger external referral.',
                ])->withInput();
            }

            $updates['status'] = 'referred';
            $updates['refer_to_higher_facility'] = true;
            $updates['referred_to'] = $validated['referred_to'] ?? null;
            $updates['referral_reason'] = $validated['referral_reason'] ?? null;
        }

        DB::table('consultations')
            ->where('id', $id)
            ->update($updates);

        return redirect()->route('consultations.show', $id)
            ->with('success', $requestedReferral
                ? 'Consultation finalized and marked as referred.'
                : 'Consultation finalized successfully.');
    }

    // 5. Save a Prescription
    public function addPrescription(Request $request, $id)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'medicine_id' => ['nullable', 'integer', 'exists:medicines_lookup,id'],
            'custom_medicine_name' => ['nullable', 'string', 'max:255'],
            'dosage' => ['required', 'string', 'max:255'],
            'frequency' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $customMedicine = trim((string) ($validated['custom_medicine_name'] ?? ''));
        $hasLookup = ! empty($validated['medicine_id']);
        $hasCustom = $customMedicine !== '';

        if ($hasLookup === $hasCustom) {
            return redirect()->back()
                ->withErrors(['prescription' => $hasLookup
                    ? 'Choose either a master-list medicine or a custom entry, not both.'
                    : 'Select a medicine from the list or enter a custom medicine name.'])
                ->withInput();
        }

        if ($hasCustom && mb_strlen($customMedicine) < 2) {
            return redirect()->back()
                ->withErrors(['custom_medicine_name' => 'Custom medicine name must be at least 2 characters.'])
                ->withInput();
        }

        DB::table('prescriptions')->insert([
            'consultation_id' => $id,
            'medicine_id' => $hasLookup ? $validated['medicine_id'] : null,
            'custom_medicine_name' => $hasCustom ? $customMedicine : null,
            'dosage' => $validated['dosage'],
            'frequency' => $validated['frequency'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'quantity' => $validated['quantity'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($this->maybeAutoCompleteConsultation((int) $id)) {
            return redirect()->back()->with('success', 'Prescription added. Consultation marked as completed.');
        }

        DB::table('consultations')->where('id', $id)->update([
            'status' => 'in_progress',
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Prescription added successfully.');
    }

    // Edit Consultation (Quick edit for notes/treatments)
    public function edit($id)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        $consultation = DB::table('consultations')
            ->leftJoin('health_workers', 'consultations.worker_id', '=', 'health_workers.id')
            ->where('consultations.id', $id)
            ->select(
                'consultations.*',
                'health_workers.first_name as worker_first_name',
                'health_workers.last_name as worker_last_name'
            )
            ->first();

        if (! $consultation) {
            abort(404, 'Consultation not found');
        }

        // Get patient info
        $patient = DB::table('patients')->find($consultation->patient_id);

        // Get diagnoses
        $diagnoses = $this->diagnosisRecordsQuery()
            ->where('diagnosis_records.consultation_id', $id)
            ->select(
                'diagnosis_records.id',
                DB::raw('COALESCE(diagnosis_lookup.diagnosis_name, diagnosis_records.custom_diagnosis_name) as diagnosis_name'),
                'diagnosis_records.remarks'
            )
            ->get();

        // Get prescriptions
        $prescriptions = $this->prescriptionsQuery()
            ->where('prescriptions.consultation_id', $id)
            ->select(
                'prescriptions.id',
                DB::raw('COALESCE(medicines_lookup.medicine_name, prescriptions.custom_medicine_name) as medicine_name'),
                'prescriptions.dosage',
                'prescriptions.frequency',
                'prescriptions.duration',
                'prescriptions.quantity'
            )
            ->get();

        return view('consultations.edit', [
            'consultation' => $consultation,
            'patient' => $patient,
            'diagnoses' => $diagnoses,
            'prescriptions' => $prescriptions,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        $consultation = DB::table('consultations')->find($id);
        if (! $consultation) {
            abort(404, 'Consultation not found');
        }

        // Update notes if provided
        if ($request->has('notes')) {
            DB::table('consultations')
                ->where('id', $id)
                ->update(['notes' => $request->input('notes'), 'updated_at' => now()]);
        }

        return redirect()->route('consultations.show', $id)->with('success', 'Consultation updated successfully.');
    }

    public function deleteDiagnosis(Request $request, $consultationId, $diagnosisId)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        $diagnosis = DB::table('diagnosis_records')
            ->where('id', $diagnosisId)
            ->where('consultation_id', $consultationId)
            ->first();

        if (! $diagnosis) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Diagnosis not found'], 404);
            }
            abort(404, 'Diagnosis not found');
        }

        DB::table('diagnosis_records')->where('id', $diagnosisId)->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Diagnosis deleted successfully']);
        }

        return redirect()->route('consultations.edit', $consultationId)->with('success', 'Diagnosis deleted successfully.');
    }

    public function deletePrescription(Request $request, $consultationId, $prescriptionId)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        $prescription = DB::table('prescriptions')
            ->where('id', $prescriptionId)
            ->where('consultation_id', $consultationId)
            ->first();

        if (! $prescription) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Prescription not found'], 404);
            }
            abort(404, 'Prescription not found');
        }

        DB::table('prescriptions')->where('id', $prescriptionId)->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Prescription deleted successfully']);
        }

        return redirect()->route('consultations.edit', $consultationId)->with('success', 'Prescription deleted successfully.');
    }

    private function maybeAutoCompleteConsultation(int $consultationId): bool
    {
        $consultation = DB::table('consultations')->where('id', $consultationId)->first();
        if (! $consultation || in_array($consultation->status, ['completed', 'referred'], true)) {
            return false;
        }

        $hasDiagnosis = DB::table('diagnosis_records')->where('consultation_id', $consultationId)->exists();
        $hasPrescription = DB::table('prescriptions')->where('consultation_id', $consultationId)->exists();

        if (! $hasDiagnosis || ! $hasPrescription) {
            return false;
        }

        DB::table('consultations')->where('id', $consultationId)->update([
            'status' => 'completed',
            'updated_at' => now(),
        ]);

        return true;
    }

    private function diagnosisRecordsQuery()
    {
        return DB::table('diagnosis_records')
            ->leftJoin('diagnosis_lookup', 'diagnosis_records.diagnosis_id', '=', 'diagnosis_lookup.id');
    }

    private function prescriptionsQuery()
    {
        return DB::table('prescriptions')
            ->leftJoin('medicines_lookup', 'prescriptions.medicine_id', '=', 'medicines_lookup.id');
    }

    private function vitalsSupportVersioning(): bool
    {
        if ($this->supportsVersionedVitals !== null) {
            return $this->supportsVersionedVitals;
        }

        $this->supportsVersionedVitals = Schema::hasColumn('vitals', 'phase')
            && Schema::hasColumn('vitals', 'captured_by');

        return $this->supportsVersionedVitals;
    }
}
