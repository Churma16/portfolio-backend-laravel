<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MessageStoreRequest;
use App\Http\Requests\Api\MessageUpdateRequest;
use App\Mail\ContactFormMail;
use App\Models\Message;
use Illuminate\Support\Facades\Mail;

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
        $details = $request->validated();

        // Pastikan key validation sama dengan yang dipakai di blade
        // (misal: 'name', 'email', 'content')

        // GANTI DENGAN EMAIL PRIBADI ANDA
        Mail::to('fathanmf16@gmail.com')->send(new ContactFormMail($details));

        $message = Message::create($details);

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
