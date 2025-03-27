<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia; // Add this import

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,advance,premium'
        ]);

        $plans = [
            'basic' => ['bookings_per_day' => 5, 'duration' => 30],
            'advance' => ['bookings_per_day' => 7, 'duration' => 30],
            'premium' => ['bookings_per_day' => 10, 'duration' => 30]
        ];

        $user = Auth::user();
        
        // Cancel any existing subscription
        $user->subscription()->delete();

        // Create new subscription
        $subscription = $user->subscription()->create([
            'plan_name' => ucfirst($request->plan) . ' Plan',
            'bookings_per_day' => $plans[$request->plan]['bookings_per_day'],
            'expires_at' => now()->addDays($plans[$request->plan]['duration'])
        ]);

        // For Inertia, you have two good options:

        // Option 1: Return a redirect with the data
        return redirect()->back()->with([
            'subscription' => $subscription,
            'message' => 'Subscription updated successfully'
        ]);

        // Option 2: Return an Inertia response (if you're handling this with a dedicated page)
        // return Inertia::render('Subscription/Show', [
        //     'subscription' => $subscription,
        //     'message' => 'Subscription updated successfully'
        // ]);
    }
}