<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SidebarItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $sidebarItems = SidebarItem::whereNull('parent_id')->with('children')->orderBy('order')->get();
        
        return view('settings.index', compact('settings', 'sidebarItems'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'app_name' => 'required|string|max:255',
            'brand_name' => 'required|string|max:255',
            'brand_logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string',
            'company_email' => 'nullable|string',
            'bank_info' => 'nullable|string',
            'signature_name' => 'nullable|string',
            'signature_title' => 'nullable|string',
            'signature_image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:1024',
            'primary_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ]);

        Setting::updateOrCreate(['key' => 'app_name'], ['value' => $data['app_name']]);
        Setting::updateOrCreate(['key' => 'brand_name'], ['value' => $data['brand_name']]);
        Setting::updateOrCreate(['key' => 'company_address'], ['value' => $data['company_address'] ?? '']);
        Setting::updateOrCreate(['key' => 'company_phone'], ['value' => $data['company_phone'] ?? '']);
        Setting::updateOrCreate(['key' => 'company_email'], ['value' => $data['company_email'] ?? '']);
        Setting::updateOrCreate(['key' => 'bank_info'], ['value' => $data['bank_info'] ?? '']);
        Setting::updateOrCreate(['key' => 'signature_name'], ['value' => $data['signature_name'] ?? '']);
        Setting::updateOrCreate(['key' => 'signature_title'], ['value' => $data['signature_title'] ?? '']);
        Setting::updateOrCreate(['key' => 'primary_color'], ['value' => $data['primary_color'] ?? '#203764']);

        if ($request->hasFile('brand_logo')) {
            $path = $request->file('brand_logo')->store('logos', 'public');
            Setting::updateOrCreate(['key' => 'brand_logo'], ['value' => $path]);
        }
        
        if ($request->hasFile('signature_image')) {
            $path = $request->file('signature_image')->store('signatures', 'public');
            Setting::updateOrCreate(['key' => 'signature_image'], ['value' => $path]);
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui');
    }

    public function toggleSidebarItem(SidebarItem $item)
    {
        $item->is_active = !$item->is_active;
        $item->save();

        return response()->json(['success' => true, 'is_active' => $item->is_active]);
    }

    public function addSidebarItem(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:sidebar_items,id',
        ]);

        $maxOrder = SidebarItem::where('parent_id', $request->parent_id)->max('order') ?? 0;
        $data['order'] = $maxOrder + 1;
        $data['is_active'] = true;

        SidebarItem::create($data);

        return redirect()->back()->with('success', 'Menu berhasil ditambahkan');
    }

    public function deleteSidebarItem(SidebarItem $item)
    {
        $item->delete();
        return redirect()->back()->with('success', 'Menu berhasil dihapus');
    }

    public function sortSidebar(Request $request)
    {
        $items = $request->items; // Array of IDs in order
        foreach ($items as $index => $id) {
            SidebarItem::where('id', $id)->update(['order' => $index + 1]);
        }
        return response()->json(['success' => true]);
    }

    public function updateSidebarItem(Request $request, SidebarItem $item)
    {
        $data = $request->validate([
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
        ]);

        $item->update($data);

        return redirect()->back()->with('success', 'Menu berhasil diperbarui');
    }
}
