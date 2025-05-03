<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetsController extends Controller
{
    public function index()
    {
        $assets = Auth::user()->assets()->latest()->get();
        return response()->json($assets);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|in:lifestyle,vehicles,properties,art,documents,others',
            'subcategory' => 'nullable|string',
            'name' => 'required|string|max:255',
            'serial_number' => 'nullable|string',
            'description' => 'nullable|string',
            'value' => 'nullable|numeric',
            'verification_level' => 'in:none,basic,verified',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|string|url', // Assume frontend sends uploaded file URLs
            'visibility' => 'in:private,friends,public',
        ]);

        $data['user_id'] = Auth::id();

        $asset = Asset::create($data);

        return response()->json($asset, 201);
    }

    public function show(Asset $asset)
    {
        $this->authorizeAccess($asset);
        return response()->json($asset);
    }

    public function update(Request $request, Asset $asset)
    {
        $this->authorizeAccess($asset);

        $data = $request->validate([
            'subcategory' => 'nullable|string',
            'name' => 'string|max:255',
            'serial_number' => 'nullable|string',
            'description' => 'nullable|string',
            'value' => 'nullable|numeric',
            'verification_level' => 'in:none,basic,verified',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|string|url',
            'visibility' => 'in:private,friends,public',
        ]);

        $asset->update($data);

        return response()->json($asset);
    }

    public function destroy(Asset $asset)
    {
        $this->authorizeAccess($asset);
        $asset->delete();
        return response()->json(['message' => 'Asset deleted']);
    }

    // === Extra Actions ===

    public function reportLost(Asset $asset)
    {
        $this->authorizeAccess($asset);
        $asset->update(['is_reported_lost' => true]);
        return response()->json(['message' => 'Asset marked as lost']);
    }

    public function toggleSale(Asset $asset)
    {
        $this->authorizeAccess($asset);
        $asset->update(['is_listed_for_sale' => !$asset->is_listed_for_sale]);
        return response()->json(['listed' => $asset->is_listed_for_sale]);
    }

    public function toggleVisibility(Asset $asset)
    {
        $this->authorizeAccess($asset);
        $asset->update(['is_visible' => !$asset->is_visible]);
        return response()->json(['visible' => $asset->is_visible]);
    }

    private function authorizeAccess(Asset $asset)
    {
        if ($asset->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
    }
}
