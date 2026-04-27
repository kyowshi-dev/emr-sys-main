<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabRequestController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', LabRequest::class);

        $labRequests = DB::table('lab_requests')
            ->join('patients', 'lab_requests.patient_id', '=', 'patients.id')
            ->leftJoin('health_workers', 'lab_requests.requested_by', '=', 'health_workers.id')
            ->select(
                'lab_requests.*',
                'patients.first_name',
                'patients.last_name',
                'patients.id as patient_id',
                'health_workers.first_name as requester_first_name',
                'health_workers.last_name as requester_last_name'
            )
            ->orderByDesc('lab_requests.created_at')
            ->paginate(20)
            ->withQueryString();

        return view('lab_requests.index', compact('labRequests'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', LabRequest::class);

        $patientId = $request->input('patient_id');
        $consultationId = $request->input('consultation_id');

        // Get patient info if provided
        $patient = null;
        if ($patientId) {
            $patient = DB::table('patients')->where('id', $patientId)->first();
        }

        return view('lab_requests.create', compact('patient', 'patientId', 'consultationId'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', LabRequest::class);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'lab_test_name' => 'required|string|max:255',
            'lab_test_description' => 'nullable|string',
            'requested_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Get current user's health worker ID
        $user = auth()->user();
        $healthWorker = DB::table('health_workers')->where('user_id', $user->id)->first();

        if (! $healthWorker) {
            return back()->withErrors(['error' => 'Health worker profile not found. Please contact administrator.']);
        }

        LabRequest::create([
            'patient_id' => $validated['patient_id'],
            'consultation_id' => $validated['consultation_id'],
            'requested_by' => $healthWorker->id,
            'lab_test_name' => $validated['lab_test_name'],
            'lab_test_description' => $validated['lab_test_description'],
            'requested_date' => $validated['requested_date'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('lab_requests.index')->with('success', 'Lab request created successfully!');
    }

    public function show(LabRequest $labRequest)
    {
        $this->authorize('view', $labRequest);

        $labRequest = DB::table('lab_requests')
            ->join('patients', 'lab_requests.patient_id', '=', 'patients.id')
            ->leftJoin('consultations', 'lab_requests.consultation_id', '=', 'consultations.id')
            ->leftJoin('health_workers', 'lab_requests.requested_by', '=', 'health_workers.id')
            ->where('lab_requests.id', $labRequest->id)
            ->select(
                'lab_requests.*',
                'patients.first_name as patient_first_name',
                'patients.last_name as patient_last_name',
                'patients.id as patient_id',
                'consultations.id as consultation_id',
                'health_workers.first_name as requester_first_name',
                'health_workers.last_name as requester_last_name'
            )
            ->first();

        return view('lab_requests.show', compact('labRequest'));
    }

    public function edit(LabRequest $labRequest)
    {
        $this->authorize('update', $labRequest);

        return view('lab_requests.edit', compact('labRequest'));
    }

    public function update(Request $request, LabRequest $labRequest)
    {
        $this->authorize('update', $labRequest);

        $validated = $request->validate([
            'lab_test_name' => 'required|string|max:255',
            'lab_test_description' => 'nullable|string',
            'status' => 'required|in:pending,completed,cancelled',
            'completed_date' => 'nullable|date|required_if:status,completed',
            'results' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $labRequest->update($validated);

        return redirect()->route('lab_requests.show', $labRequest)->with('success', 'Lab request updated successfully!');
    }
}
