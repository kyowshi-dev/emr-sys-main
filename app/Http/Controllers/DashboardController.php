<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $healthWorker = DB::table('health_workers')->where('user_id', $user->id)->first();
        $today = Carbon::today();

        if ($healthWorker && strtolower($healthWorker->role) === 'bhw') {
            $totalPatients = DB::table('patients')->count();

            $consultationsToday = DB::table('consultations')
                ->whereDate('created_at', $today)
                ->count();

            $pendingConsultations = DB::table('consultations')
                ->whereIn('status', ['triage', 'pending_validation', 'pending_doctor', 'in_progress'])
                ->count();

            $pendingQueue = DB::table('consultations')
                ->join('patients', 'consultations.patient_id', '=', 'patients.id')
                ->whereIn('consultations.status', ['triage', 'pending_validation', 'pending_doctor', 'in_progress'])
                ->orderBy('consultations.created_at')
                ->limit(5)
                ->select(
                    'patients.first_name',
                    'patients.last_name',
                    'patients.id as patient_id',
                    'consultations.status'
                )
                ->get()
                ->map(function ($row) {
                    return (object) [
                        'name' => trim($row->first_name.' '.$row->last_name),
                        'identifier' => 'PT'.str_pad((string) $row->patient_id, 3, '0', STR_PAD_LEFT).' · '.str_replace('_', ' ', (string) $row->status),
                    ];
                });

            $handoutData = $user->canViewDashboardHandouts('bhw')
                ? $this->loadResultsReady($request)
                : ['resultsReady' => collect(), 'resultsReadyCount' => 0, 'resultsFilters' => $this->emptyResultsFilters()];

            return view('dashboard_bhw', [
                'totalPatients' => $totalPatients,
                'consultationsToday' => $consultationsToday,
                'pendingConsultations' => $pendingConsultations,
                'pendingQueue' => $pendingQueue,
                'queueUpdatedAt' => now()->format('M j, Y g:i A'),
                'showResultsReady' => $user->canViewDashboardHandouts('bhw'),
                ...$handoutData,
            ]);
        }

        if ($healthWorker && in_array(strtolower($healthWorker->role), ['doctor', 'nurse'], true)) {
            $consultationsToday = DB::table('consultations')
                ->whereDate('created_at', $today)
                ->count();

            $pendingConsultations = DB::table('consultations')
                ->whereIn('status', ['triage', 'pending_validation', 'pending_doctor', 'in_progress'])
                ->count();

            $pendingValidationCount = DB::table('consultations')
                ->where('status', 'pending_validation')
                ->count();

            $validationQueue = DB::table('consultations')
                ->join('patients', 'consultations.patient_id', '=', 'patients.id')
                ->where('consultations.status', 'pending_validation')
                ->orderBy('consultations.created_at')
                ->limit(8)
                ->select(
                    'consultations.id',
                    'consultations.created_at',
                    'consultations.complaint_text',
                    'patients.first_name',
                    'patients.last_name'
                )
                ->get();

            $completedConsultationsToday = DB::table('consultations')
                ->whereDate('updated_at', $today)
                ->where('status', 'completed')
                ->count();

            $followUpConsultationsToday = DB::table('consultations')
                ->whereDate('created_at', $today)
                ->where('nature_of_visit', 'Follow-up')
                ->count();

            $recentQueue = DB::table('consultations')
                ->leftJoin('patients', 'consultations.patient_id', '=', 'patients.id')
                ->select(
                    'consultations.id',
                    'consultations.status',
                    'consultations.created_at',
                    'patients.first_name',
                    'patients.last_name'
                )
                ->whereIn('consultations.status', ['triage', 'pending_validation', 'pending_doctor', 'in_progress'])
                ->orderByDesc('consultations.created_at')
                ->limit(5)
                ->get()
                ->map(function ($row) {
                    return [
                        'id' => $row->id,
                        'patient_name' => trim("{$row->first_name} {$row->last_name}"),
                        'status' => str_replace('_', ' ', (string) $row->status),
                        'time' => Carbon::parse($row->created_at)->diffForHumans(),
                    ];
                })
                ->all();

            $handoutData = $user->canViewDashboardHandouts('clinical')
                ? $this->loadResultsReady($request, limit: 8, defaultToToday: true)
                : ['resultsReady' => collect(), 'resultsReadyCount' => 0, 'resultsFilters' => $this->emptyResultsFilters()];

            return view('dashboard_clinical', [
                'consultationsToday' => $consultationsToday,
                'pendingConsultations' => $pendingConsultations,
                'pendingValidationCount' => $pendingValidationCount,
                'validationQueue' => $validationQueue,
                'completedConsultationsToday' => $completedConsultationsToday,
                'followUpConsultationsToday' => $followUpConsultationsToday,
                'recentQueue' => $recentQueue,
                'roleLabel' => ucfirst((string) $healthWorker->role),
                'role' => strtolower((string) $healthWorker->role),
                'showResultsReady' => $user->canViewDashboardHandouts('clinical'),
                ...$handoutData,
            ]);
        }

        $totalPatients = DB::table('patients')->count();

        $pendingAppointments = DB::table('consultations')
            ->whereIn('status', ['triage', 'pending_validation', 'pending_doctor', 'in_progress'])
            ->count();

        $overdueImmunizations = DB::table('immunization_records')
            ->whereNotNull('next_due_date')
            ->whereDate('next_due_date', '<', $today)
            ->distinct('patient_id')
            ->count('patient_id');

        $followUpConsultationsToday = DB::table('consultations')
            ->whereDate('created_at', $today)
            ->where('nature_of_visit', 'Follow-up')
            ->count();

        $doctorsOnDuty = DB::table('health_workers')->count();

        $pendingPasswordResets = DB::table('password_reset_requests')->where('status', 'pending')->count();

        $onDutyStaff = DB::table('health_workers')
            ->select('first_name', 'last_name', 'role')
            ->orderBy('last_name')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $initials = mb_strtoupper(mb_substr($row->first_name, 0, 1).mb_substr($row->last_name, 0, 1));

                return [
                    'name' => trim($row->first_name.' '.$row->last_name),
                    'role' => (string) $row->role,
                    'initials' => $initials,
                ];
            })
            ->all();

        $recentActivity = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select('audit_logs.*', 'users.username')
            ->orderByDesc('audit_logs.created_at')
            ->limit(5)
            ->get()
            ->map(function ($log) {
                $time = Carbon::parse($log->created_at)->format('M d, Y H:i');
                $user = $log->username ?: 'System';
                $action = ucfirst($log->action);
                $table = ucfirst(str_replace('_', ' ', $log->table_name));

                return "{$time} – {$user} {$action} {$table} #{$log->record_id}";
            })
            ->all();

        $handoutData = $user->canViewDashboardHandouts('admin')
            ? $this->loadResultsReady($request, limit: 10)
            : ['resultsReady' => collect(), 'resultsReadyCount' => 0, 'resultsFilters' => $this->emptyResultsFilters()];

        return view('dashboard', [
            'totalPatients' => $totalPatients,
            'pendingAppointments' => $pendingAppointments,
            'overdueImmunizations' => $overdueImmunizations,
            'followUpConsultationsToday' => $followUpConsultationsToday,
            'doctorsOnDuty' => $doctorsOnDuty,
            'pendingPasswordResets' => $pendingPasswordResets,
            'onDutyStaff' => $onDutyStaff,
            'recentActivity' => $recentActivity,
            'showResultsReady' => $user->canViewDashboardHandouts('admin'),
            ...$handoutData,
        ]);
    }

    /**
     * @return array{resultsReady: Collection, resultsReadyCount: int, resultsFilters: array{query: string, from: string, to: string}}
     */
    private function loadResultsReady(Request $request, int $limit = 15, bool $defaultToToday = false): array
    {
        $resultsQuery = DB::table('consultations')
            ->join('patients', 'consultations.patient_id', '=', 'patients.id')
            ->whereIn('consultations.status', ['completed', 'referred'])
            ->select(
                'consultations.id',
                'consultations.updated_at',
                'patients.first_name',
                'patients.last_name',
                'patients.id as patient_id'
            )
            ->orderByDesc('consultations.updated_at');

        if ($request->filled('results_query')) {
            $q = $request->input('results_query');
            $resultsQuery->where(function ($qb) use ($q) {
                $qb->where('patients.first_name', 'like', '%'.$q.'%')
                    ->orWhere('patients.last_name', 'like', '%'.$q.'%');
                if (is_numeric($q)) {
                    $qb->orWhere('patients.id', (int) $q);
                }
            });
        }

        $from = $request->input('results_from', $defaultToToday ? Carbon::today()->toDateString() : '');
        $to = $request->input('results_to', '');

        if ($from !== '') {
            $resultsQuery->whereDate('consultations.updated_at', '>=', $from);
        }
        if ($to !== '') {
            $resultsQuery->whereDate('consultations.updated_at', '<=', $to);
        }

        $resultsReady = $resultsQuery->limit($limit)->get();
        $resultIds = $resultsReady->pluck('id')->all();
        $diagnosisSummaryByConsultation = [];

        if (! empty($resultIds)) {
            $dxRows = DB::table('diagnosis_records')
                ->join('diagnosis_lookup', 'diagnosis_records.diagnosis_id', '=', 'diagnosis_lookup.id')
                ->whereIn('diagnosis_records.consultation_id', $resultIds)
                ->select('diagnosis_records.consultation_id', 'diagnosis_lookup.diagnosis_name')
                ->orderBy('diagnosis_records.id')
                ->get();

            foreach ($dxRows as $dxRow) {
                $diagnosisSummaryByConsultation[$dxRow->consultation_id][] = $dxRow->diagnosis_name;
            }
        }

        $resultsReady = $resultsReady->map(function ($row) use ($diagnosisSummaryByConsultation) {
            $names = $diagnosisSummaryByConsultation[$row->id] ?? [];
            $row->diagnosis_summary = $names ? implode(', ', $names) : null;

            return $row;
        });

        return [
            'resultsReady' => $resultsReady,
            'resultsReadyCount' => DB::table('consultations')->whereIn('status', ['completed', 'referred'])->count(),
            'resultsFilters' => [
                'query' => $request->input('results_query', ''),
                'from' => $from,
                'to' => $to,
            ],
        ];
    }

    /**
     * @return array{query: string, from: string, to: string}
     */
    private function emptyResultsFilters(): array
    {
        return ['query' => '', 'from' => '', 'to' => ''];
    }
}
