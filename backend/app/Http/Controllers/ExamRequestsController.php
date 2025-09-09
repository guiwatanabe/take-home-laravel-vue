<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrintExamsRequest;
use App\Services\ExamRequestGroupingService;
use App\Services\PdfOutputService;

class ExamRequestsController extends Controller
{
    public function print(PrintExamsRequest $request)
    {
        $groupedExams = (new ExamRequestGroupingService)->group($request->validated());

        $pdfOutput = (new PdfOutputService('pdf/examrequest', [
            'groupedExams' => $groupedExams,
            'patient' => ['document' => '11144477735', 'full_name' => 'Guilherme Watanabe'],
            'doctor' => ['full_name' => 'Doutor Doutor', 'crm' => '123456/SP'],
        ]))->output();

        return response()->make($pdfOutput, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',
        ]);
    }
}
