<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EducationController extends Controller
{
    public function index(): JsonResponse
    {
        $education = Education::active()->ordered()->get();
        return response()->json($education);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'institution_name' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'start_year' => 'required|integer|min:1990|max:2030',
            'end_year' => 'nullable|integer|min:1990|max:2030|gte:start_year',
            'is_current' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $education = Education::create($validated);
        return response()->json($education, 201);
    }

    public function show(Education $education): JsonResponse
    {
        return response()->json($education);
    }

    public function update(Request $request, Education $education): JsonResponse
    {
        $validated = $request->validate([
            'institution_name' => 'string|max:255',
            'degree' => 'string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'start_year' => 'integer|min:1990|max:2030',
            'end_year' => 'nullable|integer|min:1990|max:2030|gte:start_year',
            'is_current' => 'boolean',
            'sort_order' => 'integer',
            'is_active' => 'boolean'
        ]);

        $education->update($validated);
        return response()->json($education);
    }

    public function destroy(Education $education): JsonResponse
    {
        $education->delete();
        return response()->json(['message' => 'Education deleted successfully']);
    }
}
