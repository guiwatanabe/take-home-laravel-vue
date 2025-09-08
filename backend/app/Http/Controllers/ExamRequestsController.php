<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrintExamsRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class ExamRequestsController extends Controller
{
    public function print(PrintExamsRequest $request)
    {
        $packages = collect($request->validated());
        $allExams = $packages->flatMap(function ($package) {
            return collect($package['exams'])->map(function ($exam) use ($package) {
                $exam['package_id'] = $package['id'] ?? null;

                return $exam;
            });
        });

        $groupedExams = $allExams->groupBy('group')->map(function ($examsByGroup) use ($packages) {
            return $examsByGroup->groupBy('package_id')->map(function ($packageExams, $packageId) use ($packages) {
                $originalPackage = $packages->firstWhere('id', $packageId) ?? [];

                return [
                    'package_id' => $originalPackage['id'] ?? null,
                    'package_name' => $originalPackage['name'] ?? '',
                    'package_observations' => $originalPackage['observations'] ?? '',
                    'exams' => $packageExams->toArray(),
                ];
            })->values();
        })->mapWithKeys(function ($packages, $group) {
            return [$group => ['group' => $group, 'packages' => $packages->toArray()]];
        })->values()->toArray();

        $pdf = Pdf::loadView('pdf/examrequest', [
            'groupedExams' => $groupedExams,
            'patient' => ['document' => '11144477735', 'full_name' => 'Guilherme Watanabe'],
            'doctor' => ['full_name' => 'Doutor Doutor', 'crm' => '123456/SP'],
        ]);

        return response()->make($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',
        ]);
    }
}
