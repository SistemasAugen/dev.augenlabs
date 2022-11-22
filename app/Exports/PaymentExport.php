<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Order;

class PaymentExport implements FromView, WithTitle, WithEvents, ShouldAutoSize {
    protected $year;
    protected $month;

    public function __construct($year, $month) {
        $this->year     = $year;
        $this->month    = $month;
    }

    public function view(): View {
        $startDate = sprintf('%s-%s-01', $this->year, $this->month);
        $endDate   = date('Y-m-t', strtotime($startDate));

        $orders = Order::with('client', 'laboratory')
                        ->whereBetween('payment_date', [ $startDate, $endDate ])
                        ->orderBy('client_id', 'ASC')
                        ->orderBy('id', 'ASC')
                        ->get();

        return view('exports.payments', compact('orders'));
    }

    public function registerEvents(): array {
        return [
          // Handle by a closure.
          AfterSheet::class => function(AfterSheet $event) {
              // Sheet header styles
              $event->sheet->getColumnDimension('A')->setAutoSize(true);
              $event->sheet->getColumnDimension('B')->setAutoSize(true);
              $event->sheet->getColumnDimension('C')->setAutoSize(true);
              $event->sheet->getColumnDimension('D')->setAutoSize(true);
          },
        ];
    }

    public function title(): string {
        return 'REPORTE DE PAGO';
    }
}
