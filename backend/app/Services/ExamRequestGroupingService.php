<?php

namespace App\Services;

class ExamRequestGroupingService
{
    public function group(array $packages): array
    {
        $packages = collect($packages);
        $allExams = $packages->flatMap(function ($package) {
            return collect($package['exams'])->map(function ($exam) use ($package) {
                $exam['package_id'] = $package['id'] ?? null;

                return $exam;
            });
        });

        return $allExams->groupBy('group')->map(function ($examsByGroup) use ($packages) {
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
    }
}
