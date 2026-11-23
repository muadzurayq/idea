<?php

namespace App\Http\Controllers;

use App\Notifications\EmailChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class ProfileController extends Controller
{
    //
    public function edit()
    {
        return view('profile.edit', [
            'user' => auth()->user()
        ]);

    }
    public function update(Request $request)
    {

            $user = auth()->user();

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
                'password' => 'nullable|string|min:4',
            ]);

            $originalEmail = $user->email;

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : $user->password,            ]);


            Notification::assertSentOnDemand(EmailChanged::class, function ($notification, $channels, $notifiable) use ($originalEmail) {
                    return $notifiable->routes['mail'] === $originalEmail;
            });

            return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');


    }
}

