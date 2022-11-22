<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class WarrantyExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize {
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        $timestamp = time();
        $dayOfTheWeek = date('N', $timestamp);

        $sundayTs = $timestamp + ( 7 - $dayOfTheWeek) * 24 * 60 * 60;
        $mondayTs = $timestamp - ( $dayOfTheWeek - 1) * 24 * 60 * 60;

        $startDate  = date('Y-m-d', $mondayTs - 1 * 7 * 24 * 60 * 60) . ' 00:00:00';
        $endDate    = date('Y-m-d', $sundayTs - 1 * 7 * 24 * 60 * 60) . ' 23:59:59';

        return DB::table('orders_log')
                 ->join('orders', 'orders.id', '=', 'orders_log.order_id')
                 ->join('clients', 'clients.id', '=', 'orders.client_id')
                 ->join('branches', 'branches.id', '=', 'orders.branch_id')
                 ->join('laboratories', 'laboratories.id', '=', 'orders.laboratory_id')
                 ->selectRaw(
                     'branches.name AS PDV,
                     laboratories.name AS LABORATORIO,
                     clients.name AS CLIENTE,
                     orders_log.reason AS MOTIVO,
                     orders.rx AS RX,
                     orders_log.logged_at AS FECHA'
                 )->whereBetween('logged_at', [ $startDate, $endDate ])
                 ->get();
    }

    public function headings(): array {
        return ['PDV', 'LABORATORIO', 'CLIENTE', 'MOTIVO', 'RX', 'FECHA'];
    }

    public function title(): string {
        return 'GARANTÍAS';
    }
}
