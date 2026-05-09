<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithCustomChunkSize
{
    public function chunkSize(): int
    {
        return 1000;
    }
    protected $year;
    protected $search;

    public function __construct($year = null, $search = null)
    {
        $this->year = $year;
        $this->search = $search;
    }

    public function query()
    {
        return Transaction::select('id', 'product_id', 'date_sale', 'total_buy', 'total_payment')
            ->with(['product' => function($query) {
                $query->select('id', 'product_name');
            }])
            ->when($this->search, function ($query, $search) {
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('product_name', 'like', "%{$search}%");
                })
                ->orWhere('date_sale', 'like', "%{$search}%")
                ->orWhere('total_payment', 'like', "%{$search}%");
            })
            ->when($this->year, function ($query) {
                $query->whereYear('date_sale', $this->year);
            })
            ->orderBy('date_sale', 'asc');
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Produk',
            'Tanggal Penjualan',
            'Jumlah (Qty)',
            'Total Pembayaran (Rp)',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->product?->product_name ?? 'N/A',
            $transaction->date_sale,
            $transaction->total_buy,
            $transaction->total_payment,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
