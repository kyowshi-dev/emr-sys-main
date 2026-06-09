<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation Handout — C{{ str_pad($consultation->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Source Sans 3', 'Segoe UI', Tahoma, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #1a1a1a;
            background: #f5f0e8;
            padding: 1.5rem;
        }
        .sheet {
            max-width: 720px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #d4cfc4;
            border-radius: 8px;
            padding: 1.75rem 2rem;
        }
        .toolbar {
            max-width: 720px;
            margin: 0 auto 1rem;
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .toolbar button, .toolbar a {
            font-size: 13px;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .btn-print { background: #064e3b; color: #fff; }
        .btn-back { background: #fff; color: #064e3b; border: 1px solid #064e3b !important; }
        .header {
            text-align: center;
            border-bottom: 2px solid #064e3b;
            padding-bottom: 0.75rem;
            margin-bottom: 1.25rem;
        }
        .header h1 { font-size: 15px; font-weight: 700; color: #064e3b; }
        .header p { font-size: 11px; color: #555; margin-top: 2px; }
        .doc-title {
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin: 0.75rem 0 1rem;
            color: #333;
        }
        .meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem 1.5rem;
            margin-bottom: 1.25rem;
            font-size: 11px;
        }
        .meta dt { font-weight: 600; color: #555; }
        .meta dd { margin-bottom: 0.35rem; }
        .section { margin-bottom: 1.1rem; page-break-inside: avoid; }
        .section h2 {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #064e3b;
            border-bottom: 1px solid #d4cfc4;
            padding-bottom: 0.25rem;
            margin-bottom: 0.5rem;
        }
        .section ul { list-style: none; padding: 0; }
        .section li { padding: 0.35rem 0; border-bottom: 1px dotted #e8e4dc; }
        .section li:last-child { border-bottom: none; }
        .rx-item { margin-bottom: 0.5rem; }
        .rx-name { font-weight: 700; }
        .rx-sig { color: #444; font-size: 11px; }
        .notes { font-size: 11px; color: #333; white-space: pre-wrap; }
        .footer {
            margin-top: 1.5rem;
            padding-top: 0.75rem;
            border-top: 1px solid #d4cfc4;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .signature {
            margin-top: 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            font-size: 11px;
        }
        .signature .line {
            border-top: 1px solid #333;
            padding-top: 0.25rem;
            margin-top: 2.5rem;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none !important; }
            .sheet { border: none; border-radius: 0; max-width: none; padding: 0.5in; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" class="btn-print" onclick="window.print()">Print handout</button>
        <a href="{{ route('consultations.show', $consultation->id) }}" class="btn-back">Back to consultation</a>
    </div>

    <article class="sheet">
        <header class="header">
            <h1>Barangay Health Center Information System</h1>
            <p>Sta. Ana · Patient consultation summary</p>
        </header>

        <p class="doc-title">Consultation results &amp; prescription handout</p>

        <dl class="meta">
            <div>
                <dt>Patient</dt>
                <dd>{{ $patient->last_name }}, {{ $patient->first_name }} (PT{{ str_pad($patient->id, 3, '0', STR_PAD_LEFT) }})</dd>
                <dt>Age / Sex</dt>
                <dd>{{ $age }} y/o · {{ $patient->sex }}</dd>
                <dt>Zone</dt>
                <dd>{{ $zoneLabel ?? '—' }}</dd>
            </div>
            <div>
                <dt>Consultation</dt>
                <dd>#{{ $consultation->id }} · {{ \Carbon\Carbon::parse($consultation->updated_at)->format('M j, Y g:i A') }}</dd>
                <dt>Visit type</dt>
                <dd>{{ $consultation->nature_of_visit ?? '—' }}</dd>
                <dt>Attending</dt>
                <dd>{{ trim(($consultation->worker_first_name ?? '').' '.($consultation->worker_last_name ?? '')) ?: '—' }}</dd>
            </div>
        </dl>

        @if ($consultation->complaint_text)
            <section class="section">
                <h2>Chief complaint</h2>
                <p class="notes">{{ $consultation->complaint_text }}</p>
            </section>
        @endif

        <section class="section">
            <h2>Diagnosis summary</h2>
            @if ($diagnoses->isNotEmpty())
                <ul>
                    @foreach ($diagnoses as $dx)
                        <li>
                            <strong>{{ $dx->diagnosis_name }}</strong>
                            @if ($dx->diagnosis_code)
                                <span style="color:#666;">({{ $dx->diagnosis_code }})</span>
                            @endif
                            @if ($dx->remarks)
                                — {{ $dx->remarks }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p style="color:#666;">No diagnosis recorded.</p>
            @endif
        </section>

        <section class="section">
            <h2>Prescription (Rx)</h2>
            @if ($prescriptions->isNotEmpty())
                <ul>
                    @foreach ($prescriptions as $rx)
                        <li class="rx-item">
                            <div class="rx-name">{{ $rx->medicine_name }}</div>
                            <div class="rx-sig">
                                {{ $rx->dosage }}
                                @if ($rx->frequency) · {{ $rx->frequency }} @endif
                                @if ($rx->duration) · {{ $rx->duration }} @endif
                                @if ($rx->quantity) · Qty {{ $rx->quantity }} @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p style="color:#666;">No medicines prescribed.</p>
            @endif
        </section>

        @if ($consultation->notes)
            <section class="section">
                <h2>Clinical notes</h2>
                <p class="notes">{{ $consultation->notes }}</p>
            </section>
        @endif

        @if ($consultation->refer_to_higher_facility)
            <section class="section">
                <h2>Referral</h2>
                <p class="notes">
                    Referred to: {{ $consultation->referred_to ?? 'Higher facility' }}
                    @if ($consultation->referral_reason)
                        <br>Reason: {{ $consultation->referral_reason }}
                    @endif
                </p>
            </section>
        @endif

        <div class="signature">
            <div>
                <div class="line">Health worker signature</div>
            </div>
            <div>
                <div class="line">Patient / guardian signature</div>
            </div>
        </div>

        <footer class="footer">
            Printed {{ now()->format('M j, Y g:i A') }} · For barangay health center use only. Bring this handout when picking up medicines or for follow-up visits.
        </footer>
    </article>
</body>
</html>
