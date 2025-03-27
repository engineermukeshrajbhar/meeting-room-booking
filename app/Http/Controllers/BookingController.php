<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MeetingRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        return Inertia::render('Dashboard', [
            'meetingRooms' => MeetingRoom::all(),
            'bookings' => $user->bookings()
                ->with('meetingRoom')
                ->orderBy('start_time', 'desc')
                ->paginate(10),
            'upcomingBookings' => $user->bookings()
                ->with('meetingRoom')
                ->where('start_time', '>=', now())
                ->orderBy('start_time')
                ->get(),
            'pastBookings' => $user->bookings()
                ->with('meetingRoom')
                ->where('start_time', '<', now())
                ->orderBy('start_time', 'desc')
                ->get(),
            'userSubscription' => $user->subscription
        ]);
    }

    public function availableRooms(Request $request)
    {
        $validated = $request->validate([
            'start_time' => 'required|date|after:now',
            'duration' => 'required|in:30,60,90',
            'members' => 'required|integer|min:1'
        ]);
    
        $start = Carbon::parse($validated['start_time']);
        $end = $start->copy()->addMinutes($validated['duration']);
    
        $rooms = MeetingRoom::where('capacity', '>=', $validated['members'])
            ->whereDoesntHave('bookings', function($query) use ($start, $end) {
                $query->where(function($q) use ($start, $end) {
                    $q->whereBetween('start_time', [$start, $end])
                      ->orWhere(function($q) use ($start, $end) {
                          $q->where('start_time', '<', $start)
                            ->whereRaw('DATE_ADD(start_time, INTERVAL duration MINUTE) > ?', [$start]);
                      });
                });
            })
            ->get();
    
        // Return as Inertia shared data
        return inertia()->render('Dashboard', [
            'availableRooms' => $rooms,
            // Include all other existing props
            'meetingRooms' => MeetingRoom::all(),
            'bookings' => $request->user()->bookings()
                ->with('meetingRoom')
                ->orderBy('start_time', 'desc')
                ->paginate(10),
            'upcomingBookings' => $request->user()->bookings()
                ->with('meetingRoom')
                ->where('start_time', '>=', now())
                ->orderBy('start_time')
                ->get(),
            'pastBookings' => $request->user()->bookings()
                ->with('meetingRoom')
                ->where('start_time', '<', now())
                ->orderBy('start_time', 'desc')
                ->get(),
            'userSubscription' => $request->user()->subscription
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $maxBookings = $user->subscription ? $user->subscription->bookings_per_day : 3;
        
        if ($user->bookings()->whereDate('created_at', today())->count() >= $maxBookings) {
            return back()->withErrors([
                'message' => "You've reached your daily booking limit ($maxBookings)"
            ]);
        }

        $validated = $request->validate([
            'meeting_name' => 'required|string|max:255',
            'start_time' => 'required|date|after:now',
            'duration' => 'required|in:30,60,90',
            'members' => 'required|integer|min:1',
            'meeting_room_id' => 'required|exists:meeting_rooms,id'
        ]);

        $room = MeetingRoom::findOrFail($validated['meeting_room_id']);
        
        if ($room->capacity < $validated['members']) {
            return back()->withErrors([
                'message' => 'Selected room capacity is insufficient'
            ]);
        }

        $user->bookings()->create($validated);

        return redirect()->back()->with('success', 'Booking created successfully');
    }
}