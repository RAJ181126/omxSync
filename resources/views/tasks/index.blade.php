<x-app-layout>
    <div class="container">
        <nav class="" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Task</li>
            </ol>
        </nav>

        <div class="py-12 mb-5">
            <div class="max-w-7xl mx-auto">
                <div class="d-flex align-items-center justify-content-end text-gray-900 dark:text-gray-100">
                    <div class="ps-3">
                        @if(Auth::user()->hasRole('admin'))
                        <a class="btn btn-primary" href="{{ route('task.create') }}" id="check_in" role="button">Create</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Assigned Employee</th>
                                    <th scope="col">Due Date</th>
                                    <th scope="col">Completed Date</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tasks as $index => $task)
                                <tr>
                                    <td>{{$index + 1}}</td>
                                    <td>{{$task->title}}</td>
                                    <td>{{$task->user->name}}</td>
                                    <td>{{$task->due_date?->format('Y-m-d')}}</td>
                                    <td>{{$task->completed_at?->format('Y-m-d') ?? '--'}}</td>
                                    <td><span class='badge bg-{{$task->status->color()}}'>{{$task->status->label()}}</span></td>
                                    <td>
                                        <a href="{{route('task.show', $task->id)}}" class="btn btn-sm btn-primary"><i class="fa-solid fa-pencil"></i></a>
                                        <a class="btn btn-sm btn-danger btn-delete" href="{{route('task.destroy', $task->id)}}"><i class="fa-solid fa-trash"></i></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No Task Found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>