<?php

namespace App\Exports;

use App\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class FiscalExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize {
    private $title;
    private $data;
    public function __construct($data, $title) {
        $this->title    = $title;
        $this->data     = $data;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return collect($this->data);
    }

    public function headings(): array {
        return ['ID', 'NOMBRE', 'CORREO DE FACTURACIÓN', 'RFC', 'DIRECCIÓN', 'COLONIA', 'ESTADO', 'CIUDAD', 'CÓDIGO POSTAL'];
    }

    public function title(): string {
        return $this->title;
    }
}
