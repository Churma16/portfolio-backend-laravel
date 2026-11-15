<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\ProfileStoreRequest;
use App\Http\Requests\Api\V1\ProfileUpdateRequest;
use App\Http\Resources\Api\V1\ProfileResource;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends BaseController
{
    private const CACHE_KEY = 'site_profile';

    public function index(Request $request)
    {
        $profile = Cache::remember(self::CACHE_KEY, 86400, function () {
            $data = Profile::first();

            return $data ? (new ProfileResource($data))->resolve() : null;
        });

        if (!$profile) {
            return $this->sendError("Profile not found.", 404);
        }

        return $this->sendResponse($profile, "Profile retrieved successfully.");
    }

    public function show(Request $request, Profile $profile)
    {
        return new ProfileResource($profile);
    }

    public function update(ProfileUpdateRequest $request, Profile $profile)
    {
        $data = $request->validated();

        if ($request->has('socials') && isset($data['socials']) && is_string($data['socials'])) {
            $data['socials'] = json_decode($data['socials'], true);
        }

        if ($request->has('hero_image_codes') && isset($data['hero_image_codes']) && is_string($data['hero_image_codes'])) {
            $data['hero_image_codes'] = json_decode($data['hero_image_codes'], true);
        }

        $oldAvatar = null;
        if ($request->hasFile('avatar')) {
            $oldAvatar = $profile->avatar;
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $oldCvFiles = null;
        if ($request->hasFile('cv_files')) {
            $oldCvFiles = $profile->cv_files;
            $data['cv_files'] = $request->file('cv_files')->store('cv_files', 'public');
        }

        DB::transaction(function () use ($profile, $data) {
            $profile->update($data);
        });

        if ($oldAvatar && Storage::disk('public')->exists($oldAvatar)) {
            Storage::disk('public')->delete($oldAvatar);
        }

        if ($oldCvFiles && Storage::disk('public')->exists($oldCvFiles)) {
            Storage::disk('public')->delete($oldCvFiles);
        }

        $this->clearCache(self::CACHE_KEY, '');

        return $this->sendResponse(new ProfileResource($profile), "Profile updated successfully.");
    }

    public function store(ProfileStoreRequest $request)
    {
        $profile = Profile::create($request->validated());

        $this->clearCache(self::CACHE_KEY, '');

        return new ProfileResource($profile);
    }

    public function destroy(Request $request, Profile $profile)
    {
        $oldAvatar = $profile->avatar;
        $oldCvFiles = $profile->cv_files;

        DB::transaction(function () use ($profile) {
            $profile->delete();
        });

        if ($oldAvatar && Storage::disk('public')->exists($oldAvatar)) {
            Storage::disk('public')->delete($oldAvatar);
        }

        if ($oldCvFiles && Storage::disk('public')->exists($oldCvFiles)) {
            Storage::disk('public')->delete($oldCvFiles);
        }

        $this->clearCache(self::CACHE_KEY, '');

        return response()->noContent();
    }
}
