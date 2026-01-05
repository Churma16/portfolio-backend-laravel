<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProfileResource;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\ProfileCollection;
use App\Http\Requests\Api\V1\ProfileStoreRequest;
use App\Http\Requests\Api\V1\ProfileUpdateRequest;

class ProfileController extends BaseController
{
    public function index(Request $request)
    {
        $profiles = Profile::all();

        return new ProfileCollection($profiles);
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
        $profile->update($request->validated());

        return new ProfileResource($profile);
    }

    public function destroy(Request $request, Profile $profile)
    {
        $profile->delete();

        return response()->noContent();
    }
}
