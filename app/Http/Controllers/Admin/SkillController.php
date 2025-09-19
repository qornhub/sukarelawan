<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Skill;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::all();
        return view('admin.skill.skill-list', compact('skills'));
    }

    public function create()
    {
        return view('admin.skill.skill-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'skillName' => 'required|string|max:255|unique:skills,skillName',
        ]);

        Skill::create([
            'skill_id'   => (string) Str::uuid(),
            'skillName'  => $request->skillName,
        ]);

        return redirect()->route('admin.skill.skill-list')
            ->with('success', 'Skill created successfully.');
    }

    public function edit($id)
    {
        $skill = Skill::where('skill_id', $id)->firstOrFail();
        return view('admin.skill.skill-edit', compact('skill'));
    }

    public function update(Request $request, $id)
    {
        $skill = Skill::where('skill_id', $id)->firstOrFail();

        $request->validate([
            'skillName' => 'required|string|max:255|unique:skills,skillName,' . $skill->skill_id . ',skill_id',
        ]);

        $skill->skillName = $request->skillName;
        $skill->save();

        return redirect()->route('admin.skill.skill-list')
            ->with('success', 'Skill updated successfully.');
    }

    public function destroy($id)
    {
        $skill = Skill::where('skill_id', $id)->firstOrFail();
        $skill->delete();

        return redirect()->route('admin.skill.skill-list')
            ->with('success', 'Skill deleted successfully.');
    }
}
