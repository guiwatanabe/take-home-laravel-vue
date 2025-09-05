<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Http\Resources\ExamResource;
use App\Models\Exam;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Exam::all()->toResourceCollection();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExamRequest $request)
    {
        $data = $request->validated();
        $exam = Exam::create($data);

        return new ExamResource($exam);
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        return new ExamResource($exam);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExamRequest $request, Exam $exam)
    {
        $data = $request->validated();
        $exam->update($data);

        return new ExamResource($exam);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        $exam->delete();

        return response()->json(['message' => 'Exam deleted successfully.']);
    }
}
