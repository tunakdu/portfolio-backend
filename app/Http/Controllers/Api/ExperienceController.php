<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExperienceController extends Controller
{
    public function index(): JsonResponse
    {
        $experiences = Experience::active()->ordered()->get();
        return response()->json($experiences);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_current' => 'boolean',
            'location' => 'nullable|string|max:255',
            'company_website' => 'nullable|url',
            'technologies' => 'nullable|array',
            'sort_order' => 'integer'
        ]);

        $experience = Experience::create($validated);
        return response()->json($experience, 201);
    }

    public function show(Experience $experience): JsonResponse
    {
        return response()->json($experience);
    }

    public function update(Request $request, Experience $experience): JsonResponse
    {
        $validated = $request->validate([
            'company_name' => 'string|max:255',
            'position' => 'string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'date',
            'end_date' => 'nullable|date|after:start_date',
            'is_current' => 'boolean',
            'location' => 'nullable|string|max:255',
            'company_website' => 'nullable|url',
            'technologies' => 'nullable|array',
            'sort_order' => 'integer',
            'is_active' => 'boolean'
        ]);

        $experience->update($validated);
        return response()->json($experience);
    }

    public function destroy(Experience $experience): JsonResponse
    {
        $experience->delete();
        return response()->json(['message' => 'Experience deleted successfully']);
    }
}
