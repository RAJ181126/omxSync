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
                        <a class="btn btn-primary" href="{{ route('task.index') }}" id="check_in" role="button">All Tasks</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form action="{{route('task.upsert')}}" method="POST">
                            @csrf
                            <div class="p-3">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" {{$read_only}} class="form-control" id="title" name="title" value="{{old('title', isset($tasks) ? $tasks?->title : '')}}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="user" class="form-label">Assign To</label>
                                        <select name="user_id" id="user" class="form-control form-select" {{$read_only ? 'disabled' : ''}}>
                                            <option value="">Select User</option>
                                            @foreach($users as $user)
                                            @php
                                            $selected = (isset($tasks) && $user->id == $tasks?->user_id) ? 'selected' : '';
                                            @endphp
                                            <option value="{{$user->id}}" {{$selected}}>{{$user->name}}</option>
                                            @endforeach
                                        </select>
                                        @if(isset($tasks) && Auth::user()->hasRole('employee'))
                                        <input type="hidden" name="user_id" value="{{$tasks?->user_id}}">
                                        @endif
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="due_date" class="form-label">Due Date</label>
                                        <input type="date" {{$read_only}} class="form-control" id="due_date" name="due_date" value="{{old('due_date', isset($tasks) ? $tasks?->due_date?->format('Y-m-d') : '')}}">
                                    </div>
                                    @if(isset($tasks) && Auth::user()->hasRole('employee') && $tasks?->status->value != 'completed')
                                    <div class="col-md-4 mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" id="status" class="form-control form-select">
                                            <option value="">Select Status</option>
                                            @foreach($status as $s)
                                            @php
                                            $selected = (isset($tasks) && $s['value'] == $tasks?->status->value) ? 'selected' : '';
                                            @endphp
                                            <option value="{{$s['value']}}" {{$selected}}>{{$s['label']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                    <div class="col-md-12 mb-3">
                                        <label for="desc" class="form-label">Description</label>
                                        <textarea class="form-control" name="description" id="desc" rows="3">{{old('description', isset($tasks) ? $tasks?->description : '')}}</textarea>
                                    </div>
                                    <div class="col-12 text-end">
                                        <input type="hidden" name="id" value="{{old('id', isset($tasks) ? $tasks?->id : '')}}">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // checkin
        });
    </script>
    @endpush
</x-app-layout>