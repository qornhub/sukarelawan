<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\EventCategory;

class EventCategoryController extends Controller
{
    public function index()
    {
        $categories = EventCategory::orderBy('eventCategoryName')->get();
        return view('admin.eventCategory.eventCategory-list', compact('categories'));
    }

    public function create()
    {
        return view('admin.eventCategory.eventCategory-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'eventCategoryName' => 'required|string|max:255|unique:event_categories,eventCategoryName',
            'basePoints'        => 'nullable|integer|min:0',
        ]);

        EventCategory::create([
            'eventCategory_id'  => (string) Str::uuid(),
            'eventCategoryName' => $request->eventCategoryName,
            'basePoints'        => $request->input('basePoints', 10), // default to 10 if not provided
        ]);

        return redirect()->route('admin.eventCategory.eventCategory-list')
            ->with('success', 'Event category created successfully.');
    }

    public function edit($id)
    {
        $category = EventCategory::where('eventCategory_id', $id)->firstOrFail();
        return view('admin.eventCategory.eventCategory-edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = EventCategory::where('eventCategory_id', $id)->firstOrFail();

        $request->validate([
            'eventCategoryName' => 'required|string|max:255|unique:event_categories,eventCategoryName,' . $category->eventCategory_id . ',eventCategory_id',
            'basePoints'        => 'required|integer|min:0',
        ]);

        $category->update([
            'eventCategoryName' => $request->eventCategoryName,
            'basePoints'        => $request->basePoints,
        ]);

        return redirect()->route('admin.eventCategory.eventCategory-list')
            ->with('success', 'Event category updated successfully.');
    }

    public function destroy($id)
    {
        $category = EventCategory::where('eventCategory_id', $id)->firstOrFail();
        $category->delete();

        return redirect()->route('admin.eventCategory.eventCategory-list')
            ->with('success', 'Event category deleted successfully.');
    }
}
