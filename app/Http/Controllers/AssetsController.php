<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetSubcategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\returnSelf;
use function PHPUnit\Framework\throwException;

class AssetsController extends Controller
{

    /**
     * | Asset Category List
     */
    public function assetCategoryList()
    {
        try {
            $mAssetCategory = new AssetCategory();
            $assetCategory  = $mAssetCategory->listCategory();

            return responseMsg(true, "Asset Category List", $assetCategory);
        } catch (Exception $e) {
            return responseMsg(false, $e->getMessage(), "");
        }
    }

    /**
     * | Asset Sub Category List
     */
    public function assetSubcategoryList(Request $request)
    {
        try {
            $mAssetSubcategory = new AssetSubcategory();
            $assetSubcategory  = $mAssetSubcategory->listCategory();

            return responseMsg(true, "Asset Sub Category List", $assetSubcategory);
        } catch (Exception $e) {
            return responseMsg(false, $e->getMessage(), "");
        }
    }

    /**
     * | Asset List of Auth User
     */
    public function assetList()
    {

        $assets = Auth::user()->assets()->latest()->where('status', 1)->get();
        return responseMsg(true, "Asset List", $assets);
    }

    /**
     * | Storing Assets Data
     */
    public function storeAsset(Request $request)
    {
        try {
            $data = $request->validate([
                'category_id'        => 'required',
                'subcategory_id'     => 'required',
                'name'               => 'required|string|max:255',
                'serial_number'      => 'nullable|string',
                'description'        => 'nullable|string',
                'value'              => 'nullable|numeric',
                'verification_level' => 'in:none,basic,verified',
                'attachments'        => 'nullable|array',
                'attachments.*'      => 'nullable|string|url', // Assume frontend sends uploaded file URLs
                'visibility'         => 'in:private,friends,public',
            ]);

            $data['user_id'] = Auth::id();
            $asset = Asset::create($data);

            return responseMsg(true, "Asset added succesfully", $asset);
        } catch (Exception $e) {
            return responseMsg(false, $e->getMessage(), "");
        }
    }

    /**
     * | Asset Details via Id
     */
    public function getAssetDetailsById(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required'
            ]);
            $asset = Asset::with('user') // optionally eager load related user
                ->where('id', $request->id)
                ->where('user_id', Auth::id())
                ->where('status', 1)
                ->first();

            if (!$asset)
                throw new Exception("Asset not found");

            return responseMsg(true, "Asset Found", $asset);
        } catch (Exception $e) {
            return responseMsg(false, $e->getMessage(), "");
        }
    }

    /**
     * | Update Asset Details
     */
    public function updateAsset(Request $request)
    {
        try {
            $data = $request->validate([
                'subcategory_id'     => 'nullable',
                'name'               => 'string|max:255',
                'serial_number'      => 'nullable|string',
                'description'        => 'nullable|string',
                'value'              => 'nullable|numeric',
                'verification_level' => 'in:none,basic,verified',
                'attachments'        => 'nullable|array',
                'attachments.*'      => 'nullable|string|url',
                'visibility'         => 'in:private,friends,public',
                'id'                 => 'required'
            ]);
            $mAsset = new Asset();
            $mAsset->editAsset($data);

            return responseMsg(true, "Asset details updated succesfully", "");
        } catch (Exception $e) {
            return responseMsg(false, $e->getMessage(), "");
        }
    }

    /**
     * | Delete Asset Details
     */
    public function destroyAsset(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required'
            ]);
            $mAsset = new Asset();
            $mAsset->where('id', $request->id)->update(['status' => '0']);
            return responseMsg(true, "Asset Deleted Succesfully", "");
        } catch (Exception $e) {
            return responseMsg(false, $e->getMessage(), "");
        }
    }



    /**
         Not Implemented for now
     */
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

    private function authorizeAccess($userId)
    {
        if ($userId != Auth::id()) {
            abort(403, 'Unauthorized access');
        }
    }
}
