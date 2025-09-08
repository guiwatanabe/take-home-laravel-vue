<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Http\Resources\PackageResource;
use App\Models\Package;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Package::all()->load('exams')->toResourceCollection();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePackageRequest $request)
    {
        $data = $request->validated();
        $package = Package::create($data);

        if (! empty($data['exams'])) {
            $package->exams()->sync($data['exams']);
        }

        return response()->json(new PackageResource($package->load('exams')), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {
        return new PackageResource($package->load('exams'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePackageRequest $request, Package $package)
    {
        $data = $request->validated();
        $package->update($request->validated());

        if (array_key_exists('exams', $data)) {
            $package->exams()->sync($data['exams']);
        }

        return response()->json(new PackageResource($package->load('exams')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        $package->delete();

        return response()->json(['message' => 'Package deleted successfully.']);
    }
}
