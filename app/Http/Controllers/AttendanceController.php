<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
      /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user            = Auth::user();
        $attendance      = Attendance::where('date', Carbon::today())->where('user_id', Auth::user()->id)->first();
        $user_attendance = Attendance::where('user_id', Auth::user()->id)->get();
        return view('attendance.index', compact('attendance', 'user_attendance'));
    }

    public function checkIn()
    {
        $user          = Auth::user();
        $checkin_time  = now();
        $punctual_time = Carbon::today()->setTime(10, 0, 0);
        $interval      = null;
        if ($checkin_time->greaterThan($punctual_time)) {
              // Rounded down to the nearest value to give the user a few seconds of flexibility.
            $interval = floor($punctual_time->diffInMinutes($checkin_time));
            $on_time  = false;
        } else {
            $on_time = true;
        }
        $data = [
            'user_id'      => $user->id,
            'date'         => Carbon::today(),
            'check_in'     => $checkin_time,
            'punctual'     => $on_time,
            'minutes_late' => $interval,
        ];
        Attendance::create($data);
        return redirect()->back();
    }

      /**
     * Store a newly created resource in storage.
     */
    public function checkOut()
    {
        $attendance = Attendance::where('date', Carbon::today())->where('user_id', Auth::user()->id)->first();
        $check_out  = now();
        $update     = $attendance->update([
            'check_out' => $check_out
        ]);
        return redirect()->back();
    }

      /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
          //
    }

      /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
          //
    }

      /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
          //
    }

      /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
          //
    }
}
