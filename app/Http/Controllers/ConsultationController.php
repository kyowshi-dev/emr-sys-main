<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
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
                        ->join('diagnosis_lookup', 'diagnosis_records.diagnosis_id', '=', 'diagnosis_lookup.id')
                        ->whereColumn('diagnosis_records.consultation_id', 'consultations.id')
                        ->where('diagnosis_lookup.diagnosis_name', 'like', '%'.$q.'%');
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

        $consultations = $query->get();

        $consultationIds = $consultations->pluck('id')->toArray();

        $diagnosisByConsultation = [];
        $treatmentByConsultation = [];
        if (! empty($consultationIds)) {
            $diagnosisRows = DB::table('diagnosis_records')
                ->join('diagnosis_lookup', 'diagnosis_records.diagnosis_id', '=', 'diagnosis_lookup.id')
                ->whereIn('diagnosis_records.consultation_id', $consultationIds)
                ->select('diagnosis_records.consultation_id', 'diagnosis_lookup.diagnosis_name', 'diagnosis_records.remarks')
                ->orderBy('diagnosis_records.id')
                ->get();
            foreach ($diagnosisRows as $row) {
                $diagnosisByConsultation[$row->consultation_id][] = trim($row->diagnosis_name.($row->remarks ? ' - '.$row->remarks : ''));
            }

            $prescriptionRows = DB::table('prescriptions')
                ->join('medicines_lookup', 'prescriptions.medicine_id', '=', 'medicines_lookup.id')
                ->whereIn('prescriptions.consultation_id', $consultationIds)
                ->select('prescriptions.consultation_id', 'medicines_lookup.medicine_name', 'prescriptions.dosage', 'prescriptions.duration')
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

    // 1. Show the Admission Form (Triage)
    public function create($patientId)
    {
        $patient = DB::table('patients')->find($patientId);

        if (! $patient) {
            abort(404, 'Patient not found');
        }

        return view('consultations.create', compact('patient'));
    }

    // 2. Save the Data (Triage Save)
    public function store(Request $request, $patientId)
    {
        $validated = $request->validate([
            'nature_of_visit' => ['required', 'string', 'max:255'],
            'chief_complaint' => ['nullable', 'string', 'max:1000'],
            'bp_systolic' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'bp_diastolic' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'temperature' => ['required', 'numeric', 'min:30', 'max:45'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:300'],
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
                'chief_complaint_id' => null,
                'complaint_text' => $validated['chief_complaint'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('vitals')->insert([
                'consultation_id' => $consultationId,
                'bp_systolic' => $validated['bp_systolic'] ?? null,
                'bp_diastolic' => $validated['bp_diastolic'] ?? null,
                'weight_kg' => $validated['weight'] ?? null,
                'height_cm' => $validated['height'] ?? null,
                'temperature_c' => $validated['temperature'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
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

        $consultation = DB::table('consultations')->find($id);

        if (! $consultation) {
            abort(404, 'Resource not found');
        }

        $patient = DB::table('patients')->find($consultation->patient_id);

        $vitals = DB::table('vitals')->where('consultation_id', $id)->first();
        if (! $vitals) {
            $vitals = (object) [
                'bp_systolic' => null,
                'bp_diastolic' => null,
                'temperature_c' => null,
                'weight_kg' => null,
                'height_cm' => null,
            ];
        }

        // 2. Fetch Existing Records (History)
        $existingDiagnoses = DB::table('diagnosis_records')
            ->join('diagnosis_lookup', 'diagnosis_records.diagnosis_id', '=', 'diagnosis_lookup.id')
            ->where('consultation_id', $id)
            ->select('diagnosis_records.*', 'diagnosis_lookup.diagnosis_name', 'diagnosis_lookup.diagnosis_code')
            ->get();

        $existingPrescriptions = DB::table('prescriptions')
            ->join('medicines_lookup', 'prescriptions.medicine_id', '=', 'medicines_lookup.id')
            ->where('prescriptions.consultation_id', $id)
            ->select('prescriptions.*', 'medicines_lookup.medicine_name')
            ->get();

        // 3. NEW: Fetch Dropdown Options (The "Menu" for the Doctor)
        $diagnosisOptions = DB::table('diagnosis_lookup')->orderBy('diagnosis_name')->get();
        $medicineOptions = DB::table('medicines_lookup')->orderBy('medicine_name')->get();

        return view('consultations.show', [
            'consultation' => $consultation,
            'patient' => $patient,
            'vitals' => $vitals,
            'diagnoses' => $existingDiagnoses,
            'prescriptions' => $existingPrescriptions,
            'diagnosisOptions' => $diagnosisOptions,
            'medicineOptions' => $medicineOptions,
        ]);
    }

    // 4. Save a Diagnosis (Doctor's Action)
    public function addDiagnosis(Request $request, $id)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'diagnosis_id' => 'required|exists:diagnosis_lookup,id',
            'remarks' => 'nullable|string',
        ]);

        $workerId = DB::table('health_workers')
            ->where('user_id', Auth::id())
            ->value('id');

        if ($workerId === null) {
            abort(403, 'No health worker profile is linked to this user.');
        }

        DB::table('diagnosis_records')->insert([
            'consultation_id' => $id,
            'diagnosis_id' => $request->diagnosis_id,
            'remarks' => $request->remarks,
            'diagnosed_by' => $workerId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update status to completed
        DB::table('consultations')->where('id', $id)->update(['status' => 'completed']);

        return redirect()->back()->with('success', 'Diagnosis added successfully!');
    }

    // 5. Save a Prescription
    public function addPrescription(Request $request, $id)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'medicine_id' => ['required', 'exists:medicines_lookup,id'],
            'dosage' => ['required', 'string', 'max:255'],
            'frequency' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        DB::table('prescriptions')->insert([
            'consultation_id' => $id,
            'medicine_id' => $validated['medicine_id'],
            'dosage' => $validated['dosage'],
            'frequency' => $validated['frequency'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'quantity' => $validated['quantity'] ?? null,
            'created_at' => now(),
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

        $consultation = DB::table('consultations')->find($id);

        if (! $consultation) {
            abort(404, 'Consultation not found');
        }

        // Get patient info
        $patient = DB::table('patients')->find($consultation->patient_id);
        
        // Get diagnoses
        $diagnoses = DB::table('diagnosis_records')
            ->join('diagnosis_lookup', 'diagnosis_records.diagnosis_id', '=', 'diagnosis_lookup.id')
            ->where('diagnosis_records.consultation_id', $id)
            ->select('diagnosis_records.id', 'diagnosis_lookup.diagnosis_name', 'diagnosis_records.remarks')
            ->get();

        // Get prescriptions
        $prescriptions = DB::table('prescriptions')
            ->join('medicines_lookup', 'prescriptions.medicine_id', '=', 'medicines_lookup.id')
            ->where('prescriptions.consultation_id', $id)
            ->select('prescriptions.id', 'medicines_lookup.medicine_name', 'prescriptions.dosage', 'prescriptions.frequency', 'prescriptions.duration', 'prescriptions.quantity')
            ->get();

        return view('consultations.edit', [
            'consultation' => $consultation,
            'patient' => $patient,
            'diagnoses' => $diagnoses,
            'prescriptions' => $prescriptions,
        ]);
    }

    // Export Consultation to PDF
    public function export($id)
    {
        if (! auth()->user()->hasPermission('consultations')) {
            abort(403, 'Unauthorized');
        }

        $consultation = DB::table('consultations')
            ->join('patients', 'consultations.patient_id', '=', 'patients.id')
            ->join('health_workers', 'consultations.worker_id', '=', 'health_workers.id')
            ->where('consultations.id', $id)
            ->select(
                'consultations.*',
                'patients.first_name as patient_first_name',
                'patients.last_name as patient_last_name',
                'patients.date_of_birth as patient_dob',
                'patients.sex as patient_sex',
                'health_workers.first_name as worker_first_name',
                'health_workers.last_name as worker_last_name',
                'health_workers.position as worker_position'
            )
            ->first();

        if (! $consultation) {
            abort(404, 'Consultation not found');
        }

        // Get diagnoses
        $diagnoses = DB::table('diagnosis_records')
            ->join('diagnosis_lookup', 'diagnosis_records.diagnosis_id', '=', 'diagnosis_lookup.id')
            ->where('diagnosis_records.consultation_id', $id)
            ->select('diagnosis_lookup.diagnosis_name', 'diagnosis_records.remarks')
            ->get();

        // Get prescriptions
        $prescriptions = DB::table('prescriptions')
            ->join('medicines_lookup', 'prescriptions.medicine_id', '=', 'medicines_lookup.id')
            ->where('prescriptions.consultation_id', $id)
            ->select('medicines_lookup.medicine_name', 'prescriptions.dosage', 'prescriptions.frequency', 'prescriptions.duration', 'prescriptions.quantity')
            ->get();

        // Get vitals
        $vitals = DB::table('vitals')->where('consultation_id', $id)->first();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.consultation_summary', [
            'consultation' => $consultation,
            'diagnoses' => $diagnoses,
            'prescriptions' => $prescriptions,
            'vitals' => $vitals,
        ]);

        return $pdf->stream('consultation-' . $consultation->id . '.pdf');
    }
}
