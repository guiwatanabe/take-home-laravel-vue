<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfOutputService
{
    public function __construct(public string $view, public array $data) {}

    public function output(array $options = []): string
    {
        $pdf = Pdf::loadView($this->view, $this->data);
        return $pdf->output();
    }
}
