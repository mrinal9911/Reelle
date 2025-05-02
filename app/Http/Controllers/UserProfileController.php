<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserProfileRequest;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function update(UpdateUserProfileRequest $request)
    {
        $user = auth()->user();

        $data = $request->validated();

        // Handle ID document upload
        if ($request->hasFile('id_document')) {
            $data['id_document_path'] = $request->file('id_document')->store('ids', 'public');
        }

        $user->update($data);

        return response()->json(['message' => 'Profile updated successfully.']);
    }
}
