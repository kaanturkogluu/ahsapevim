<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingCompany;
use Illuminate\Http\Request;

class ShippingCompanyController extends Controller
{
    public function index()
    {
        $companies = ShippingCompany::latest()->paginate(15);
        return view('admin.shipping_companies.index', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'website_url' => 'nullable|url|max:500',
        ]);

        ShippingCompany::create([
            'name' => $request->name,
            'website_url' => $request->website_url,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Kargo şirketi başarıyla eklendi.');
    }

    public function update(Request $request, $id)
    {
        $company = ShippingCompany::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'website_url' => 'nullable|url|max:500',
            'is_active' => 'required|boolean',
        ]);

        $company->update([
            'name' => $request->name,
            'website_url' => $request->website_url,
            'is_active' => $request->is_active,
        ]);

        return redirect()->back()->with('success', 'Kargo şirketi başarıyla güncellendi.');
    }

    public function destroy($id)
    {
        $company = ShippingCompany::findOrFail($id);
        $company->delete();

        return redirect()->back()->with('success', 'Kargo şirketi silindi.');
    }
}
