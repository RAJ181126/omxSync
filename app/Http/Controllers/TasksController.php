<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Http\Requests\Task\UpsertTaskRequest;
use App\Models\Tasks;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Tasks::with('user')->when(Auth::user()->hasRole('employee'), function($query){
            $query->where('user_id', Auth::user()->id);
        })->get();
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $read_only = '';
        $users  = User::all();
        return view('tasks.create', compact('users', 'read_only'));
    }

    public function upsert(UpsertTaskRequest $request)
    {
        $data   = $request->validated();
        if($data['status'] == 'completed'){
            $data['completed_at'] = now()->format('Y-m-d');
            $data['status'] = $data['completed_at'] > $data['due_date'] ? 'delays' : $data['status'];
        }
        $upsert = Tasks::upsert(
            [$data],
            ['id'],
            ['user_id', 'title', 'description', 'due_date', 'completed_at', 'status']
        );

        return redirect()->back();
    }

    public function show(Tasks $tasks)
    {
        $read_only = Auth::user()->hasRole('employee') ? 'readonly' : '';
        $users  = User::all();
        $status = TaskStatus::all();
        return view('tasks.create', compact('users', 'status', 'tasks', 'read_only'));
    }

    public function destroy(Tasks $tasks)
    {
        //
    }
}
