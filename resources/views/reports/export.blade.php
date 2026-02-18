<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kehadiran - {{ $alert->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #1e293b;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #64748b;
            font-size: 14px;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .alert-info h2 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        
        .alert-info .details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            font-size: 14px;
        }
        
        .summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: #f8fafc;
            border-left: 4px solid;
            padding: 15px;
            border-radius: 5px;
        }
        
        .summary-card.total {
            border-color: #3b82f6;
        }
        
        .summary-card.hadir {
            border-color: #10b981;
        }
        
        .summary-card.tidak-hadir {
            border-color: #ef4444;
        }
        
        .summary-card.percentage {
            border-color: #f59e0b;
        }
        
        .summary-card h3 {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .summary-card .value {
            font-size: 28px;
            font-weight: bold;
            color: #1e293b;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        table thead {
            background: #1e293b;
            color: white;
        }
        
        table th {
            padding: 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        
        table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        
        table tbody tr:hover {
            background: #e0f2fe;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge.hadir {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge.tidak-hadir {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            color: #64748b;
            font-size: 12px;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 LAPORAN KEHADIRAN ALARM SIAGA</h1>
            <p>Sistem Informasi Alarm Siaga - Politeknik</p>
        </div>
        
        <div class="alert-info">
            <h2>{{ $alert->title }}</h2>
            <div class="details">
                <div><strong>Tingkat:</strong> {{ strtoupper($alert->level) }}</div>
                <div><strong>Dipicu oleh:</strong> {{ $alert->triggered_by }}</div>
                <div><strong>Waktu Mulai:</strong> {{ $alert->started_at?->format('d/m/Y H:i:s') }}</div>
                <div><strong>Waktu Selesai:</strong> {{ $alert->ended_at?->format('d/m/Y H:i:s') ?? 'Masih Aktif' }}</div>
            </div>
        </div>
        
        <div class="summary">
            <div class="summary-card total">
                <h3>Total Personel</h3>
                <div class="value">{{ $logs->count() }}</div>
            </div>
            <div class="summary-card hadir">
                <h3>Hadir</h3>
                <div class="value">{{ $logs->where('status', 'hadir')->count() }}</div>
            </div>
            <div class="summary-card tidak-hadir">
                <h3>Tidak Hadir</h3>
                <div class="value">{{ $logs->where('status', 'tidak_hadir')->count() }}</div>
            </div>
            <div class="summary-card percentage">
                <h3>Persentase</h3>
                <div class="value">
                    {{ $logs->count() > 0 ? round(($logs->where('status', 'hadir')->count() / $logs->count()) * 100, 2) : 0 }}%
                </div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NRP</th>
                    <th>Jabatan</th>
                    <th>Status</th>
                    <th>Waktu Hadir</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $log->personel->name }}</strong></td>
                    <td>{{ $log->personel->nrp }}</td>
                    <td>{{ ucfirst($log->role) }}</td>
                    <td>
                        <span class="badge {{ $log->status }}">
                            {{ $log->status === 'hadir' ? '✓ Hadir' : '✗ Tidak Hadir' }}
                        </span>
                    </td>
                    <td>{{ $log->attended_at?->format('d/m/Y H:i:s') ?? '-' }}</td>
                    <td>{{ $log->keterangan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="footer">
            <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>Sistem Informasi Alarm Siaga &copy; {{ date('Y') }}</p>
        </div>
    </div>
    
    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
