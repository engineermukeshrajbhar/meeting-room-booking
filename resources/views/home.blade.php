@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>


                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <router-link to="/book-room" class="nav-link">Book Room</router-link>
                    <router-link to="/my-bookings" class="nav-link">My Bookings</router-link>
                    {{ __('You are logged in!') }}
                    {{ __('You are logged in! mukesh') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
