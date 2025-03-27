<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'meeting_room_id',
        'meeting_name',
        'start_time',
        'duration',
        'members'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function meetingRoom()
    {
        return $this->belongsTo(MeetingRoom::class);
    }
}
