<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPatients = DB::table('patients')->count();

        $pendingAppointments = DB::table('consultations')
            ->whereIn('status', ['triage', 'pending_doctor'])
            ->count();

        $today = Carbon::today();

        // FIXED: Using distinct() chained with count() for proper Laravel Query Builder syntax
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

        return view('dashboard', [
            'totalPatients' => $totalPatients,
            'pendingAppointments' => $pendingAppointments,
            'overdueImmunizations' => $overdueImmunizations,
            'followUpConsultationsToday' => $followUpConsultationsToday,
            'doctorsOnDuty' => $doctorsOnDuty,
            'pendingPasswordResets' => $pendingPasswordResets,
            'onDutyStaff' => $onDutyStaff,
            'recentActivity' => $recentActivity,
        ]);
    }
}
