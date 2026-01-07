<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache; // <--- JANGAN LUPA IMPORT INI
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Api\V1\ProfileResource;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\ProfileCollection;
use App\Http\Requests\Api\V1\ProfileStoreRequest;
use App\Http\Requests\Api\V1\ProfileUpdateRequest;

class ProfileController extends BaseController
{
    // Kita buat key statis karena Profile biasanya cuma satu (Singleton)
    private const CACHE_KEY = 'site_profile';

    public function index(Request $request)
    {
        // CACHE READ: Simpan selama 24 jam (86400 detik) atau selamanya sampai di-update
        // Karena profil jarang berubah, durasi lama tidak masalah.
        $profile = Cache::remember(self::CACHE_KEY, 86400, function () {
            return Profile::first();
        });

        // Pastikan handle jika profile belum dibuat sama sekali (null)
        if (!$profile) {
            return $this->sendError("Profile not found.", [], 404);
        }

        return $this->sendResponse(new ProfileResource($profile), "Profile retrieved successfully.");
    }

    public function show(Request $request, Profile $profile)
    {
        // Sebenarnya method ini mungkin redundan jika Anda sudah punya index yang me-return Profile::first()
        // Tapi jika tetap dipakai, tidak perlu cache khusus karena index sudah meng-cover data utama.
        return new ProfileResource($profile);
    }

    public function store(ProfileStoreRequest $request)
    {
        $profile = Profile::create($request->validated());

        // HAPUS CACHE: Agar index() selanjutnya mengambil data yang baru dibuat
        Cache::forget(self::CACHE_KEY);

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

        // HAPUS CACHE: Ini Bagian Paling Penting
        // Setelah update foto/text, hapus cache lama agar Frontend dapat data baru
        Cache::forget(self::CACHE_KEY);

        return $this->sendResponse(new ProfileResource($profile), "Profile updated successfully.");
    }

    public function destroy(Request $request, Profile $profile)
    {
        if ($profile->avatar) {
             Storage::disk('public')->delete($profile->avatar);
        }

        $profile->delete();

        // HAPUS CACHE: Agar index() selanjutnya tidak menampilkan data hantu (yang sudah dihapus)
        Cache::forget(self::CACHE_KEY);

        return response()->noContent();
    }
}
