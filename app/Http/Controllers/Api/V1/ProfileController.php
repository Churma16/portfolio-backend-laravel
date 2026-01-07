<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Api\V1\ProfileResource;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\ProfileCollection;
use App\Http\Requests\Api\V1\ProfileStoreRequest;
use App\Http\Requests\Api\V1\ProfileUpdateRequest;

class ProfileController extends BaseController
{
    public function index(Request $request)
    {
        $profiles = Profile::first();

        return $this->sendResponse(new ProfileResource($profiles), "Profile retrieved successfully.");
    }

    public function show(Request $request, Profile $profile)
    {
        return new ProfileResource($profile);
    }

    public function store(ProfileStoreRequest $request)
    {
        $profile = Profile::create($request->validated());

        return new ProfileResource($profile);
    }

    public function update(ProfileUpdateRequest $request, Profile $profile)
    {
        $data = $request->validated();

        // Decode JSON strings
        if ($request->has('socials') && isset($data['socials']) && is_string($data['socials'])) {
            $data['socials'] = json_decode($data['socials'], true);
        }

        if ($request->has('hero_image_codes') && isset($data['hero_image_codes']) && is_string($data['hero_image_codes'])) {
            $data['hero_image_codes'] = json_decode($data['hero_image_codes'], true);
        }

        // store avatar
        if ($request->hasFile('avatar')) {
            if ($profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        if ($request->hasFile('cv_files')) {
            if ($profile->cv_files) {
                Storage::disk('public')->delete($profile->cv_files);
            }
            $path = $request->file('cv_files')->store('cv_files', 'public');
            $data['cv_files'] = $path;
        }

        $profile->update($data);

        return $this->sendResponse(new ProfileResource($profile), "Profile updated successfully.");
    }

    public function destroy(Request $request, Profile $profile)
    {
        $profile->delete();

        return response()->noContent();
    }
}
