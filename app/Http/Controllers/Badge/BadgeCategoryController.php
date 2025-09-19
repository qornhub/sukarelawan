<?php

namespace App\Http\Controllers\Badge;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BadgeCategory;

class BadgeCategoryController extends Controller
{
    public function index()
    {
        $categories = BadgeCategory::all();
        return view('admin.badgeCategory.badgeCategory-list', compact('categories'));
    }

    public function create()
    {
        return view('admin.badgeCategory.badgeCategory-create');
    }

    public function store(Request $request)
    {
        $request->validate([
        'badgeCategoryName' => 'required|string|max:255',
    ]);

        BadgeCategory::create([
             'badgeCategoryName'  => $request->badgeCategoryName,
        ]);

        return redirect()->route('admin.badge_categories.badgeCategory-list')->with('success', 'Badge Category created successfully.');
    }

    public function edit($id)
    {
        $category = BadgeCategory::findOrFail($id);
        return view('admin.badgeCategory.badgeCategory-edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'badgeCategoryName' => 'required|string|max:255',
        ]);

        $category = BadgeCategory::findOrFail($id);
        $category->update([
            'badgeCategoryName'  => $request->badgeCategoryName,
        ]);

        return redirect()->route('admin.badge_categories.badgeCategory-list')->with('success', 'Badge Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = BadgeCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.badge_categories.badgeCategory-list')->with('success', 'Badge Category deleted successfully.');
    }
}
