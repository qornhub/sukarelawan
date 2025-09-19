<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Sdg;

class SdgController extends Controller
{
   public function index(Request $request)
{
    $query = Sdg::query();

    // Optional search by name
    if ($request->has('q') && !empty($request->q)) {
        $query->where('sdgName', 'like', '%' . $request->q . '%');
    }

    // Use pagination instead of all()
    $sdgs = $query->orderBy('created_at', 'desc')->paginate(12);

    return view('admin.sdg.sdg-list', compact('sdgs'));
}


    public function create()
    {
        return view('admin.sdg.sdg-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'sdgName'  => 'required|string|max:255',
            'sdgImage' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'sdg_id'  => (string) Str::uuid(),
            'sdgName' => $request->sdgName,
            // DB requires sdgImage NOT NULL in your schema, so default to empty string
            'sdgImage'=> '',
        ];

        if ($request->hasFile('sdgImage')) {
            $fileName = time() . '_sdg_' . preg_replace('/\s+/', '_', $request->sdgImage->getClientOriginalName());
            $request->sdgImage->move(public_path('images/sdgs'), $fileName);
            $data['sdgImage'] = $fileName;
        }

        Sdg::create($data);

        return redirect()->route('admin.sdg.sdg-list')
            ->with('success', 'SDG created successfully.');
    }

    // <-- THIS is the edit() method you were missing
    public function edit($id)
    {
        // use where('sdg_id', $id) so this works even if model primaryKey isn't set
        $sdg = Sdg::where('sdg_id', $id)->firstOrFail();
        return view('admin.sdg.sdg-edit', compact('sdg'));
    }

    public function update(Request $request, $id)
    {
        // find by sdg_id (UUID)
        $sdg = Sdg::where('sdg_id', $id)->firstOrFail();

        $request->validate([
            'sdgName'  => 'required|string|max:255',
            'sdgImage' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('sdgImage')) {
            if ($sdg->sdgImage) {
                $oldImageBasename = basename($sdg->sdgImage);
                $oldImagePath = public_path('images/sdgs/' . $oldImageBasename);
                if (file_exists($oldImagePath)) {
                    @unlink($oldImagePath);
                }
            }

            $fileName = time() . '_sdg_' . preg_replace('/\s+/', '_', $request->sdgImage->getClientOriginalName());
            $request->sdgImage->move(public_path('images/sdgs'), $fileName);
            $sdg->sdgImage = $fileName;
        }

        $sdg->sdgName = $request->sdgName;
        $sdg->save();

        return redirect()->route('admin.sdg.sdg-list')
            ->with('success', 'SDG updated successfully.');
    }

    public function destroy($id)
    {
        $sdg = Sdg::where('sdg_id', $id)->firstOrFail();

        if ($sdg->sdgImage) {
            $oldImageBasename = basename($sdg->sdgImage);
            $oldImagePath = public_path('images/sdgs/' . $oldImageBasename);
            if (file_exists($oldImagePath)) {
                @unlink($oldImagePath);
            }
        }

        $sdg->delete();

        return redirect()->route('admin.sdg.sdg-list')
            ->with('success', 'SDG deleted successfully.');
    }
}
