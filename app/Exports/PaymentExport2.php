<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\PaymentExport;
use App\Laboratory;

class PaymentExport2 implements WithMultipleSheets {
    use Exportable;

    /**
     * @return array
     */
    public function sheets(): array {
        $sheets = [];

        $laboratories = Laboratory::all();
        foreach ($laboratories as $laboratory) {
            $sheets[] = new PaymentExport(2022, 02, $laboratory->id);
        }

        return $sheets;
    }
}
