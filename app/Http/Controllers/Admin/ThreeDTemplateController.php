<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThreeDTemplate;
use Illuminate\Http\Request;

class ThreeDTemplateController extends Controller
{
    public function index()
    {
        $templates = ThreeDTemplate::withCount('products')->get();
        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:three_d_templates,name',
            'wood_type' => 'required|string|max:255',
            'width' => 'required|numeric|min:1',
            'height' => 'required|numeric|min:1',
            'depth' => 'required|numeric|min:0.1',
            'thickness' => 'required|numeric|min:0.1',
            'has_top' => 'nullable|boolean',
            'has_bottom' => 'nullable|boolean',
            'has_left' => 'nullable|boolean',
            'has_right' => 'nullable|boolean',
            'inner_width' => 'required|numeric|min:1',
            'inner_height' => 'required|numeric|min:1',
            'inner_depth' => 'required|numeric|min:0.1',
            'inner_border' => 'required|numeric|min:0.1',
            'pos_x' => 'required|numeric',
            'pos_y' => 'required|numeric',
            'bump_scale' => 'required|numeric|min:0',
        ]);

        ThreeDTemplate::create([
            'name' => $request->name,
            'wood_type' => $request->wood_type,
            'width' => $request->width,
            'height' => $request->height,
            'depth' => $request->depth,
            'thickness' => $request->thickness,
            'has_top' => $request->has('has_top') ? 1 : 0,
            'has_bottom' => $request->has('has_bottom') ? 1 : 0,
            'has_left' => $request->has('has_left') ? 1 : 0,
            'has_right' => $request->has('has_right') ? 1 : 0,
            'inner_width' => $request->inner_width,
            'inner_height' => $request->inner_height,
            'inner_depth' => $request->inner_depth,
            'inner_border' => $request->inner_border,
            'pos_x' => $request->pos_x,
            'pos_y' => $request->pos_y,
            'bump_scale' => $request->bump_scale,
        ]);

        return redirect()->route('admin.templates.index')->with('success', '3D Şablon başarıyla oluşturuldu.');
    }

    public function edit($id)
    {
        $template = ThreeDTemplate::findOrFail($id);
        return view('admin.templates.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = ThreeDTemplate::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:three_d_templates,name,' . $id,
            'wood_type' => 'required|string|max:255',
            'width' => 'required|numeric|min:1',
            'height' => 'required|numeric|min:1',
            'depth' => 'required|numeric|min:0.1',
            'thickness' => 'required|numeric|min:0.1',
            'has_top' => 'nullable|boolean',
            'has_bottom' => 'nullable|boolean',
            'has_left' => 'nullable|boolean',
            'has_right' => 'nullable|boolean',
            'inner_width' => 'required|numeric|min:1',
            'inner_height' => 'required|numeric|min:1',
            'inner_depth' => 'required|numeric|min:0.1',
            'inner_border' => 'required|numeric|min:0.1',
            'pos_x' => 'required|numeric',
            'pos_y' => 'required|numeric',
            'bump_scale' => 'required|numeric|min:0',
        ]);

        $template->update([
            'name' => $request->name,
            'wood_type' => $request->wood_type,
            'width' => $request->width,
            'height' => $request->height,
            'depth' => $request->depth,
            'thickness' => $request->thickness,
            'has_top' => $request->has('has_top') ? 1 : 0,
            'has_bottom' => $request->has('has_bottom') ? 1 : 0,
            'has_left' => $request->has('has_left') ? 1 : 0,
            'has_right' => $request->has('has_right') ? 1 : 0,
            'inner_width' => $request->inner_width,
            'inner_height' => $request->inner_height,
            'inner_depth' => $request->inner_depth,
            'inner_border' => $request->inner_border,
            'pos_x' => $request->pos_x,
            'pos_y' => $request->pos_y,
            'bump_scale' => $request->bump_scale,
        ]);

        return redirect()->route('admin.templates.index')->with('success', '3D Şablon başarıyla güncellendi.');
    }

    public function destroy($id)
    {
        $template = ThreeDTemplate::findOrFail($id);
        $template->delete();

        return redirect()->route('admin.templates.index')->with('success', '3D Şablon başarıyla silindi.');
    }
}
