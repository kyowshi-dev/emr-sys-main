<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Request Slip - LR{{ str_pad($labRequest->id, 3, '0', STR_PAD_LEFT) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #333;
            background: white;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }
        .header-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .header-subtitle {
            font-size: 10px;
            color: #555;
            margin-bottom: 1px;
        }
        .slip-title {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin: 10px 0;
            text-transform: uppercase;
        }
        .slip-id {
            text-align: right;
            font-size: 10px;
            margin-bottom: 10px;
        }
        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 4px 6px;
            margin-bottom: 8px;
            border-left: 3px solid #0066cc;
            text-transform: uppercase;
        }
        .form-group {
            display: inline-block;
            width: 48%;
            margin-right: 2%;
            margin-bottom: 8px;
            vertical-align: top;
        }
        .form-group.full {
            width: 100%;
            margin-right: 0;
        }
        .form-group.third {
            width: 31%;
            margin-right: 2%;
        }
        .label {
            font-size: 9px;
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .value {
            font-size: 11px;
            padding: 6px;
            border: 1px solid #ddd;
            background: #fafafa;
            min-height: 18px;
            display: flex;
            align-items: center;
        }
        .value.bold {
            font-weight: bold;
            color: #000;
        }
        .value.large {
            font-size: 12px;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        tr {
            page-break-inside: avoid;
        }
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 15px;
        }
        .three-column {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0 10px;
        }
        .results-box {
            border: 1px solid #ddd;
            padding: 8px;
            min-height: 60px;
            background: #fafafa;
            font-family: 'Courier New', monospace;
            font-size: 10px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .notes-box {
            border: 1px solid #ddd;
            padding: 8px;
            min-height: 40px;
            background: #fafafa;
            font-size: 10px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .signature-section {
            margin-top: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            padding-top: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .note-text {
            font-size: 9px;
            color: #666;
            font-style: italic;
            margin-top: 3px;
        }
        .divider {
            page-break-after: avoid;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
            font-size: 9px;
            color: #999;
        }
        .highlight {
            background-color: #fff9c4;
            padding: 2px 4px;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 10mm;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-title">BARANGAY HEALTH CENTER INFORMATION SYSTEM</div>
            <div class="header-subtitle">Sta. Ana Barangay Health Center</div>
            <div class="header-subtitle">Department of Health • Field Health Service Information System (FHSIS)</div>
        </div>

        <!-- Slip Title and ID -->
        <div class="slip-title">Laboratory Request Slip</div>
        <div class="slip-id">
            <strong>Request ID:</strong> LR{{ str_pad($labRequest->id, 3, '0', STR_PAD_LEFT) }}
            <br>
            <strong>Date Printed:</strong> {{ now()->format('M d, Y • H:i A') }}
        </div>

        <!-- Patient Information Section -->
        <div class="section">
            <div class="section-title">Patient Information</div>
            <div class="three-column">
                <div class="form-group">
                    <div class="label">Patient Name</div>
                    <div class="value large bold">{{ $labRequest->patient_last_name }}, {{ $labRequest->patient_first_name }}</div>
                </div>
                <div class="form-group">
                    <div class="label">Patient ID</div>
                    <div class="value bold">PT{{ str_pad($labRequest->patient_id, 3, '0', STR_PAD_LEFT) }}</div>
                </div>
                <div class="form-group">
                    <div class="label">Request Status</div>
                    <div class="value">
                        <span class="status-badge status-{{ $labRequest->status }}">{{ ucfirst($labRequest->status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Information Section -->
        <div class="section">
            <div class="section-title">Laboratory Test Details</div>
            <div class="form-group full">
                <div class="label">Test Name</div>
                <div class="value bold" style="font-size: 12px;">{{ $labRequest->lab_test_name }}</div>
            </div>
            @if($labRequest->lab_test_description)
            <div class="form-group full">
                <div class="label">Test Description</div>
                <div class="value" style="min-height: 30px; white-space: pre-wrap; align-items: flex-start; padding-top: 6px;">{{ $labRequest->lab_test_description }}</div>
            </div>
            @endif
        </div>

        <!-- Request Details Section -->
        <div class="section">
            <div class="section-title">Request Information</div>
            <div class="two-column">
                <div class="form-group">
                    <div class="label">Requested Date</div>
                    <div class="value">{{ $labRequest->requested_date->format('M d, Y') }}</div>
                </div>
                @if($labRequest->completed_date)
                <div class="form-group">
                    <div class="label">Completed Date</div>
                    <div class="value">{{ $labRequest->completed_date->format('M d, Y') }}</div>
                </div>
                @else
                <div class="form-group">
                    <div class="label">Completion Date</div>
                    <div class="value">—</div>
                </div>
                @endif
            </div>
            <div class="form-group full">
                <div class="label">Requested By</div>
                <div class="value">{{ $labRequest->requester_first_name }} {{ $labRequest->requester_last_name }}</div>
            </div>
            @if($labRequest->consultation_id)
            <div class="form-group full">
                <div class="label">Related Consultation</div>
                <div class="value">Consultation #{{ str_pad($labRequest->consultation_id, 3, '0', STR_PAD_LEFT) }}</div>
            </div>
            @endif
        </div>

        <!-- Results Section -->
        @if($labRequest->results || $labRequest->status === 'completed')
        <div class="section">
            <div class="section-title">Test Results</div>
            @if($labRequest->results)
            <div class="form-group full">
                <div class="label">Results</div>
                <div class="results-box">{{ $labRequest->results }}</div>
            </div>
            @else
            <div class="form-group full">
                <div class="label">Results</div>
                <div class="results-box" style="min-height: 40px; display: flex; align-items: center; justify-content: center; color: #999; font-style: italic;">
                    [Space for test results]
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Notes Section -->
        @if($labRequest->notes)
        <div class="section">
            <div class="section-title">Additional Notes</div>
            <div class="form-group full">
                <div class="notes-box">{{ $labRequest->notes }}</div>
            </div>
        </div>
        @endif

        <!-- Signature Section -->
        <div class="section">
            <div class="section-title">Signatures</div>
            <div class="signature-section">
                <div>
                    <div style="min-height: 50px; margin-bottom: 5px;"></div>
                    <div class="signature-line">Requested By / Health Worker</div>
                    <div class="note-text">Name & Signature</div>
                </div>
                <div>
                    <div style="min-height: 50px; margin-bottom: 5px;"></div>
                    <div class="signature-line">Laboratory Authorized Person</div>
                    <div class="note-text">Name & Signature</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            This is an official laboratory request slip from {{ config('app.name', 'BHCIS') }}.
            <br>For inquiries, please contact the Sta. Ana Barangay Health Center.
        </div>
    </div>
</body>
</html>
