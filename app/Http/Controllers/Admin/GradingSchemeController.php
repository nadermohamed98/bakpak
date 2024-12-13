<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradingScheme;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Brian2694\Toastr\Facades\Toastr;

class GradingSchemeController extends Controller
{
	public function index()
	{
		// Paginate and display grading schemes
		$gradingSchemes = GradingScheme::paginate(10);
		return view('backend.admin.course.grading-schemes.index', compact('gradingSchemes'));
	}
	
	public function create()
	{
		return view('backend.admin.course.grading-schemes.create');
	}
	
	public function store(Request $request): JsonResponse
	{
		$validated = $request->validate([
			'name' => 'required|string|max:255|unique:grading_schemes,name',
			'min_percentage' => 'required|numeric|min:0|max:100',
			'max_percentage' => 'required|numeric|min:0|max:100',
		]);
		
		try {
			GradingScheme::create($validated);
			Toastr::success(__('create_successful'));
			
			
			return response()->json([
				'success' => __('create_successful'),
				'route' => route('admin.grading-schemes.index'),
			]);
			
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()]);
		}
	}
	
	public function edit($id)
	{
		// Fetch grading scheme with any related models (mappings) if needed
		$gradingScheme = GradingScheme::findOrFail($id);
		return view('backend.admin.course.grading-schemes.edit', compact('gradingScheme'));
	}
	
	public function update(Request $request, $id): JsonResponse
	{
		$request->validate([
			'name' => 'required|string|max:255|unique:grading_schemes,name,' . $id,
			'min_percentage' => 'required|numeric|min:0|max:100',
			'max_percentage' => 'required|numeric|min:0|max:100',
		]);
		
		try {
			$gradingScheme = GradingScheme::findOrFail($id);
			$gradingScheme->update($request->only(['name', 'min_percentage', 'max_percentage']));
			Toastr::success(__('updated_successfully'));
			
			return response()->json([
				'success' => __('updated_successfully'),
				'route' => route('admin.grading-schemes.index'),
			]);
			
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()]);
		}
	}
	
	public function destroy($id): JsonResponse
	{
		try {
			$gradingScheme = GradingScheme::findOrFail($id);
			$gradingScheme->delete();
			
			return response()->json(['message' => 'Grading Scheme deleted successfully.']);
		} catch (\Exception $e) {
			// Return error response if deletion fails
			return response()->json(['message' => 'Failed to delete Grading Scheme.'], 500);
		}
	}
}