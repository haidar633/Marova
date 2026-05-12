<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Financial Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .date-range {
            font-style: italic;
            margin-bottom: 15px;
        }
        .summary-container {
            margin-bottom: 20px;
        }
        .summary-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -5px;
            justify-content: space-between;
        }
        .summary-card {
            flex: 1;
            min-width: 23%;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            margin: 5px;
            text-align: center;
        }
        .card-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        .card-value {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-danger {
            color: #dc3545;
        }
        .text-success {
            color: #28a745;
        }
        footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #777;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
<div class="header">
    <h2>Financial Report</h2>
    <div class="date-range">Period: {{ $start_date }} to {{ $end_date }}</div>
</div>

<div class="summary-container">
    <div class="summary-row">
        <div class="summary-card">
            <div class="card-title">APPOINTMENTS</div>
            <div class="card-value">{{ $nb_of_appointments }}</div>
        </div>
        <div class="summary-card">
            <div class="card-title">PATIENTS</div>
            <div class="card-value">{{ $nb_of_patients }}</div>
        </div>
        <div class="summary-card">
            <div class="card-title">DOCTOR SHARE</div>
            <div class="card-value">${{ number_format($doctorShareTotal, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="card-title">CENTER SHARE</div>
            <div class="card-value">${{ number_format($centerShareTotal, 2) }}</div>
        </div>
    </div>

    <div class="summary-row" style="margin-top: 10px;">
        <div class="summary-card">
            <div class="card-title">TOTAL BILLED</div>
            <div class="card-value">${{ number_format($totalBilledAmount, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="card-title">TOTAL PAID</div>
            <div class="card-value">${{ number_format($totalPaidAmount, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="card-title">REMAINING</div>
            <div class="card-value" style="color: {{ $totalRemainingAmount > 0 ? '#dc3545' : '#28a745' }}">
                ${{ number_format($totalRemainingAmount, 2) }}
            </div>
        </div>
    </div>
</div>

<h3>Doctor Details</h3>
<table>
    <thead>
    <tr>
        <th>Doctor</th>
        <th class="text-right">Appts</th>
        <th class="text-right">Patients</th>
        <th class="text-right">Total Billed</th>
        <th class="text-right">Total Paid</th>
        <th class="text-right">Remaining</th>
        <th class="text-right">Dr Share</th>
        <th class="text-right">Center Share</th>
    </tr>
    </thead>
    <tbody>
    @foreach($reportData as $report)
        <tr>
            <td>{{ $report['doctor_name'] }}</td>
            <td class="text-right">{{ $report['appointment_count'] }}</td>
            <td class="text-right">{{ $report['patient_count'] }}</td>
            <td class="text-right">${{ number_format($report['total'], 2) }}</td>
            <td class="text-right">${{ number_format($report['paid'], 2) }}</td>
            <td class="text-right" style="color: {{ $report['remaining'] > 0 ? '#dc3545' : '#28a745' }}">
                ${{ number_format($report['remaining'], 2) }}
            </td>
            <td class="text-right">${{ number_format($report['doctor_price'], 2) }} ({{ $report['doctor_percentage'] }}%)</td>
            <td class="text-right">${{ number_format($report['center'], 2) }} ({{ $report['center_percentage'] }}%)</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr class="total-row">
        <td><strong>TOTALS</strong></td>
        <td class="text-right"><strong>{{ $nb_of_appointments }}</strong></td>
        <td class="text-right"><strong>{{ $nb_of_patients }}</strong></td>
        <td class="text-right"><strong>${{ number_format($totalBilledAmount, 2) }}</strong></td>
        <td class="text-right"><strong>${{ number_format($totalPaidAmount, 2) }}</strong></td>
        <td class="text-right" style="color: {{ $totalRemainingAmount > 0 ? '#dc3545' : '#28a745' }}">
            <strong>${{ number_format($totalRemainingAmount, 2) }}</strong>
        </td>
        <td class="text-right"><strong>${{ number_format($doctorShareTotal, 2) }}</strong></td>
        <td class="text-right"><strong>${{ number_format($centerShareTotal, 2) }}</strong></td>
    </tr>
    </tfoot>
</table>

<footer>
    <p>Report generated on {{ date('Y-m-d H:i:s') }}</p>
</footer>
</body>
</html>
