<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignmentGroup;
use App\Models\GradingScheme;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class AssignmentGroupController extends Controller
{
	/**
	 * Display a listing of the assignment groups.
	 *
	 * @return View
	 */
	public function index(): View
	{
		$assignmentGroups = AssignmentGroup::paginate(10);
		return view('backend.admin.course.assignment-groups.index', compact('assignmentGroups'));
	}
	
	/**
	 * Show the form for creating a new assignment group.
	 *
	 * @return View
	 */
	public function create(): View
	{
		return view('backend.admin.course.assignment-groups.create');
	}
	
	/**
	 * Store a newly created assignment group in storage.
	 *
	 * @param  Request  $request
	 * @return JsonResponse
	 */
	public function store(Request $request): JsonResponse
	{
		$validated = $request->validate([
			'name' => 'required|string|max:255',
			'weight' => 'required|numeric|min:0|max:100',
			'lowest_degree' => 'required|numeric|min:0|max:100',
			'highest_degree' => 'required|numeric|min:0|max:100',
			'status' => 'required|boolean',
		]);
		
		try {
			AssignmentGroup::create($validated);
			Toastr::success(__('create_successful'));
			
			
			return response()->json([
				'success' => __('create_successful'),
				'route' => route('admin.assignment-groups.index'),
			]);
			
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()]);
		}
		
	}
	
	/**
	 * Show the form for editing the specified assignment group.
	 *
	 * @param $id
	 * @return View
	 */
	public function edit($id): View
	{
		$assignmentGroup = AssignmentGroup::findOrFail($id);
		return view('backend.admin.course.assignment-groups.edit', compact('assignmentGroup'));
	}
	
	/**
	 * Update the specified assignment group in storage.
	 *
	 * @param  Request  $request
	 * @param $id
	 * @return JsonResponse
	 */
	public function update(Request $request, $id): JsonResponse
	{
		$request->validate([
			'name' => 'required|string|max:255' . $id,
			'weight' => 'required|numeric|min:0|max:100',
			'lowest_degree' => 'required|numeric|min:0|max:100',
			'highest_degree' => 'required|numeric|min:0|max:100',
			'status' => 'required|boolean',
		]);
		
		try {
			$assignmentGroup = AssignmentGroup::findOrFail($id);
			$assignmentGroup->update($request->only(['name', 'weight', 'lowest_degree', 'highest_degree', 'status']));
			Toastr::success(__('updated_successfully'));
			
			return response()->json([
				'success' => __('updated_successfully'),
				'route' => route('admin.assignment-groups.index'),
			]);
			
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()]);
		}
	}
	
	/**
	 * Remove the specified assignment group from storage.
	 *
	 * @param $id
	 * @return JsonResponse
	 */
	public function destroy($id): JsonResponse
	{
		
		try {
			$assignmentGroup = AssignmentGroup::findOrFail($id);
			$assignmentGroup->delete();
			
			return response()->json(['message' => 'Assignment Group deleted successfully.']);
		} catch (\Exception $e) {
			// Return error response if deletion fails
			return response()->json(['message' => 'Failed to delete Assignment Group.'], 500);
		}
	}
}