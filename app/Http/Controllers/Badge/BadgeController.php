<?php

namespace App\Http\Controllers\Badge;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Badge;
use App\Models\BadgeCategory;

class BadgeController extends Controller
{
    /**
     * Show all badges
     */


public function index(Request $request)
{
    $categoryId = $request->query('category');
    $q = $request->query('q');

    // get categories with counts
    $categories = BadgeCategory::withCount('badges')->orderBy('badgeCategoryName')->get();

    // base badge query
    $query = Badge::with('category')->orderBy('created_at', 'desc');

    if ($categoryId) {
        $query->where('badgeCategory_id', $categoryId);
    }

    if ($q) {
        $query->where(function($sub) use ($q) {
            $sub->where('badgeName', 'like', "%{$q}%")
                ->orWhere('badgeDescription', 'like', "%{$q}%");
        });
    }

    $badges = $query->paginate(12)->appends($request->query());

    return view('admin.badge.badge-list', compact('badges', 'categories'));
}


    /**
     * Show create form
     */
    public function create()
    {
       

         // Fetch all categories for the dropdown
    $badgeCategories = BadgeCategory::all();

    return view('admin.badge.badge-create', compact('badgeCategories'));
    }

    /**
     * Store a new badge
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'badgeCategory_id' => 'required|exists:badge_categories,badgeCategory_id',
        'badgeName'        => 'required|string|max:255',
        'badgeDescription' => 'nullable|string',
        'pointsRequired'   => 'required|integer|min:1',
        'badgeImage'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $imagePath = 'images/badges/default-badge.jpg';

    if ($request->hasFile('badgeImage')) {
        $destinationPath = public_path('images/badges');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $filename = time() . '.' . $request->badgeImage->extension();
        $request->badgeImage->move($destinationPath, $filename);

        $imagePath = 'images/badges/' . $filename;
    }

    Badge::create([
        'badgeCategory_id'=> $validated['badgeCategory_id'],
        'badgeName'       => $validated['badgeName'],
        'badgeDescription'=> $validated['badgeDescription'] ?? null,
        'pointsRequired'  => $validated['pointsRequired'],
        'badgeImage'      => $imagePath,
    ]);

    return redirect()->route('admin.badges.index')
                     ->with('success', 'Badge created successfully.');
}

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $badge = Badge::findOrFail($id);
        $categories = BadgeCategory::all();

        return view('admin.badge.badge-edit', compact('badge', 'categories'));
    }

    /**
     * Update badge
     */
    public function update(Request $request, $id)
{
    $badge = Badge::findOrFail($id);

    $validated = $request->validate([
        'badgeCategory_id' => 'required|exists:badge_categories,badgeCategory_id',
        'badgeName'        => 'required|string|max:255',
        'badgeDescription' => 'nullable|string',
        'pointsRequired'   => 'required|integer|min:1',
        'badgeImage'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $imagePath = $badge->badgeImage;

    if ($request->hasFile('badgeImage')) {
        // delete old image if not default
        if ($imagePath && $imagePath !== 'images/badges/default-badge.jpg' && file_exists(public_path($imagePath))) {
            unlink(public_path($imagePath));
        }

        $destinationPath = public_path('images/badges');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $filename = time() . '.' . $request->badgeImage->extension();
        $request->badgeImage->move($destinationPath, $filename);

        $imagePath = 'images/badges/' . $filename;
    }

    $badge->update([
        'badgeCategory_id'=> $validated['badgeCategory_id'],
        'badgeName'       => $validated['badgeName'],
        'badgeDescription'=> $validated['badgeDescription'] ?? null,
        'pointsRequired'  => $validated['pointsRequired'],
        'badgeImage'      => $imagePath,
    ]);

    return redirect()->route('admin.badges.index')
                     ->with('success', 'Badge updated successfully.');
}


    /**
     * Delete badge
     */
   public function destroy($id)
{
    $badge = Badge::findOrFail($id);

    if (
        $badge->badgeImage &&
        $badge->badgeImage !== 'images/badges/default-badge.jpg' &&
        file_exists(public_path($badge->badgeImage))
    ) {
        unlink(public_path($badge->badgeImage));
    }

    $badge->delete();

    return redirect()->route('admin.badges.index')->with('success', 'Badge deleted successfully.');
}

}
