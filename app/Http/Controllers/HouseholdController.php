<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HouseholdController extends Controller
{
    public function index(Request $request): View
    {
        if (! auth()->user()->hasPermission('household')) {
            abort(403, 'Unauthorized');
        }

        $households = DB::table('households')
            ->join('zones', 'households.zone_id', '=', 'zones.id')
            ->select('households.*', 'zones.zone_number')
            ->orderBy('zones.zone_number')
            ->orderBy('households.family_name_head')
            ->paginate(10)
            ->withQueryString();

        return view('households.index', [
            'households' => $households,
        ]);
    }

    public function create(): View
    {
        if (! auth()->user()->hasPermission('household')) {
            abort(403, 'Unauthorized');
        }

        $zones = DB::table('zones')
            ->select('id', 'zone_number')
            ->orderBy('zone_number')
            ->get();

        return view('households.create', [
            'zones' => $zones,
        ]);
    }

    public function store(Request $request)
    {
        if (! auth()->user()->hasPermission('household')) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'zone_id' => ['required', 'integer', 'exists:zones,id'],
            'family_name_head' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:32', 'regex:/^[0-9+\\-\\s()]*$/'],
        ]);

        DB::table('households')->insert([
            'zone_id' => $data['zone_id'],
            'family_name_head' => trim($data['family_name_head']),
            'contact_number' => $data['contact_number'] !== null ? trim($data['contact_number']) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('households.index')
            ->with('success', 'Household registered successfully.');
    }
}
