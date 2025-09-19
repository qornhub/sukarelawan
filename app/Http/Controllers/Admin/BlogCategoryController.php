<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\BlogCategory;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::orderBy('categoryName')->get();
        return view('admin.blogcategory.category-list', compact('categories'));
    }

    public function create()
    {
        return view('admin.blogcategory.category-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoryName' => 'required|string|max:255|unique:blog_categories,categoryName',
        ]);

        BlogCategory::create([
            'blogCategory_id' => (string) Str::uuid(),
            'categoryName'    => $request->categoryName,
        ]);

        return redirect()->route('admin.blogcategory.category-list')
            ->with('success', 'Blog category created successfully.');
    }

    public function edit($id)
    {
        $category = BlogCategory::where('blogCategory_id', $id)->firstOrFail();
        return view('admin.blogcategory.category-edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = BlogCategory::where('blogCategory_id', $id)->firstOrFail();

        $request->validate([
            'categoryName' => 'required|string|max:255|unique:blog_categories,categoryName,' . $category->blogCategory_id . ',blogCategory_id',
        ]);

        $category->categoryName = $request->categoryName;
        $category->save();

        return redirect()->route('admin.blogcategory.category-list')
            ->with('success', 'Blog category updated successfully.');
    }

    public function destroy($id)
    {
        $category = BlogCategory::where('blogCategory_id', $id)->firstOrFail();
        $category->delete();

        return redirect()->route('admin.blogcategory.category-list')
            ->with('success', 'Blog category deleted successfully.');
    }
}
