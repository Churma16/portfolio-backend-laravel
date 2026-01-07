<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Api\V1\ProfileResource;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\ProfileCollection;
use App\Http\Requests\Api\V1\ProfileStoreRequest;
use App\Http\Requests\Api\V1\ProfileUpdateRequest;

class ProfileController extends BaseController
{
    // Karena profil biasanya tunggal, kita cukup pakai satu key utama
    private const CACHE_KEY = 'site_profile';

    public function index(Request $request)
    {
        // CACHE READ: Simpan selama 24 jam (86400 detik)
        $profile = Cache::remember(self::CACHE_KEY, 86400, function () {
            $data = Profile::first();

            return $data ? (new ProfileResource($data))->resolve() : null;
        });

        if (!$profile) {
            return $this->sendError("Profile not found.", [], 404);
        }

        return $this->sendResponse($profile, "Profile retrieved successfully.");
    }

    public function show(Request $request, Profile $profile)
    {
        // Tetap menggunakan resource untuk konsistensi jika show dipanggil dengan ID
        return new ProfileResource($profile);
    }

    public function store(ProfileStoreRequest $request)
    {
        $profile = Profile::create($request->validated());

        $this->clearCache(self::CACHE_KEY, '');

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
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->hasFile('cv_files')) {
            if ($profile->cv_files) {
                Storage::disk('public')->delete($profile->cv_files);
            }
            $data['cv_files'] = $request->file('cv_files')->store('cv_files', 'public');
        }

        $profile->update($data);

        $this->clearCache(self::CACHE_KEY, '');

        return $this->sendResponse(new ProfileResource($profile), "Profile updated successfully.");
    }

    public function destroy(Request $request, Profile $profile)
    {
        if ($profile->avatar) {
            Storage::disk('public')->delete($profile->avatar);
        }

        $profile->delete();

        $this->clearCache(self::CACHE_KEY, '');

        return response()->noContent();
    }
}
