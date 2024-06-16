<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function uploadUserImage(Request $request)
    {
        logger('uploadUserImage hit');

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if($request->hasFile('image')) {
            $user = Auth::user();

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('public/profile_pictures', $filename);

            if ($user->image && $user->image !== 'profile_pictures/user.png') {
                Storage::delete('public/', $user->image);
            }

            $user->update(['image' => 'profile_pictures/' . $filename]);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile picture updated successfully',
                'image' => 'profile_pictures/' .$filename,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No image file found in the request'
        ], 400);
    }
}
