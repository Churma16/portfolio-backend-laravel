<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MessageStoreRequest;
use App\Http\Requests\Api\MessageUpdateRequest;
use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MessageStoreRequest $request)
    {
        if ($request->filled('gotcha')) {
            return response()->json([
                'success' => true,
                'message' => 'Pesan terkirim!'
            ], 200);
        }

        $details = $request->validated();
        $message = Message::create($details);

        // Dispatch contact notification to portfolio-notifier microservice
        try {
            $notifierUrl = config('services.notifier.url', env('NOTIFIER_URL', 'http://portfolio-notifier:8080/api/notify'));
            Http::timeout(5)->post($notifierUrl, [
                'name'    => $details['name'],
                'email'   => $details['email'],
                'message' => $details['content'] ?? ($details['message'] ?? ''),
                'source'  => 'laravel',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to dispatch notification to portfolio-notifier: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Email sedang dikirim!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MessageUpdateRequest $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        //
    }
}
