<x-app-layout>
    <div class="container">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </x-slot>

        <div class="container my-4">
            <div class="row g-3">
                <!-- Left column: user card -->
                <div class="col-12 col-lg-4">
                    <div class="card card-hero card-compact p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width:64px;height:64px;font-size:1.25rem;">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-0">{{ $user->name ?? 'Unknown User' }}</h5>
                                <div class="small-muted">ID: {{ $user->id }}</div>
                            </div>
                            @php
                            $grade = $metrics['grade'] ?? ($gradeRecord->grade ?? 'N/A');
                            $gradeColors = ['A'=>'bg-success','B'=>'bg-primary','C'=>'bg-warning text-dark','D'=>'bg-danger'];
                            $gradeColor = $gradeColors[$grade] ?? 'bg-secondary';
                            @endphp
                            <div class="text-end">
                                <div class="grade-badge {{ $gradeColor }}"><h4 class="text-center p-1">{{ $grade }}</h4></div>
                                <div class="small-muted mt-1">Grade</div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small-muted">Final Score</div>
                                <div class="h3 mb-0">{{ isset($metrics['final_score']) ? number_format($metrics['final_score'],2) : '0.00' }}</div>
                            </div>

                            <div class="text-end">
                                <div class="small-muted">Attendance Points</div>
                                <div class="h4 mb-0">{{ $metrics['attendance_points'] ?? 0 }}</div>
                            </div>
                        </div>

                        @if(!empty($gradeRecord))
                        <div class="small-muted mt-3">Grade Updated At: {{ \Carbon\Carbon::parse($gradeRecord->calculated_at)->format('d M, Y H:i') }}</div>
                        @endif
                    </div>

                    <!-- Quick actions / links -->
                    <div class="mt-3">
                        <div class="d-grid gap-2">
                            <a href="{{ route('attendance.index')}}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-calendar-check me-1"></i> View Attendance
                            </a>
                            <a href="{{ route('task.index')}}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-list-task me-1"></i> View Tasks
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right column: performance & chart -->
                <div class="col-12 col-lg-8">
                    <div class="row g-3">
                        <!-- Top stats (4 small boxes) -->
                        <div class="col-12">
                            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">
                                <div class="col">
                                    <div class="stat-box d-flex align-items-start gap-3">
                                        <div class="flex-grow-1">
                                            <div class="metric-label">Assigned Tasks</div>
                                            <div class="h5 mb-0" id="assigned-count">{{ $metrics['assigned_count'] ?? 0 }}</div>
                                        </div>
                                        <div class="text-muted align-self-center"><i class="bi bi-clipboard-data fs-3"></i></div>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="stat-box d-flex align-items-start gap-3">
                                        <div class="flex-grow-1">
                                            <div class="metric-label">Completed</div>
                                            <div class="h5 mb-0" id="completed-count">{{ $metrics['completed_count'] ?? 0 }}</div>
                                        </div>
                                        <div class="text-muted align-self-center"><i class="bi bi-check-circle fs-3"></i></div>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="stat-box d-flex align-items-start gap-3">
                                        <div class="flex-grow-1">
                                            <div class="metric-label">Completion Points</div>
                                            <div class="h5 mb-0">{{ isset($metrics['completion_rate_points']) ? number_format($metrics['completion_rate_points'],2) : '0.00' }}</div>
                                        </div>
                                        <div class="text-muted align-self-center"><i class="bi bi-speedometer2 fs-3"></i></div>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="stat-box d-flex align-items-start gap-3">
                                        <div class="flex-grow-1">
                                            <div class="metric-label">On-time Points</div>
                                            <div class="h5 mb-0">{{ isset($metrics['on_time_points']) ? number_format($metrics['on_time_points'],2) : '0.00' }}</div>
                                        </div>
                                        <div class="text-muted align-self-center"><i class="bi bi-stopwatch fs-3"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chart + task performance details -->
                        <div class="col-12">
                            <div class="card card-compact p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">Task Performance</h6>
                                    <div class="small-muted">Total (of 80): <strong>{{ isset($metrics['task_performance_total']) ? number_format($metrics['task_performance_total'],2) : '0.00' }}</strong></div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="chart-wrap">
                                            <canvas id="taskChart" aria-label="Tasks completed vs pending"></canvas>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="metric-label">Penalties</div>
                                            <div class="h5 text-danger mb-0">{{ isset($metrics['penalties']) ? number_format($metrics['penalties'],2) : '0.00' }}</div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="metric-label">Punctuality</div>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="flex-grow-1">
                                                    <div class="progress" style="height:12px;">
                                                        <div class="progress-bar" role="progressbar" style="width: {{ isset($metrics['punctuality_percent']) ? $metrics['punctuality_percent'] : 0 }}%;" aria-valuenow="{{ $metrics['punctuality_percent'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <div class="small-muted mt-1">{{ isset($metrics['punctuality_percent']) ? number_format($metrics['punctuality_percent'],2) : '0.00' }}%</div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="small-muted">Attendance pts</div>
                                                    <div class="h6 mb-0">{{ $metrics['attendance_points'] ?? 0 }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-2">
                                            <div class="metric-label">Details</div>
                                            <ul class="list-unstyled small mt-2">
                                                <li><strong>Minor delays:</strong> {{ $metrics['minor_delay_count'] ?? 0 }}</li>
                                                <li><strong>Severe delays:</strong> {{ $metrics['severe_delay_count'] ?? 0 }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end chart card -->
                    </div>
                </div> <!-- end right column -->
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            $(function() {
                // Safely pull PHP metrics into JS
                var metrics = @json($metrics ?? []);
                // ensure numeric defaults
                var assigned = Number(metrics.assigned_count || 0);
                var completed = Number(metrics.completed_count || 0);
                var pending = Math.max(assigned - completed, 0);

                // Update the small counters in DOM (defensive)
                $('#assigned-count').text(assigned);
                $('#completed-count').text(completed);

                // Chart.js donut for Completed vs Pending
                var $canvas = $('#taskChart');
                if ($canvas.length) {
                    var canvas = $canvas[0];
                    var ctx = canvas.getContext('2d');
                    if (ctx) {
                        // destroy any previous instance (helpful in dev/hot reload environments)
                        if (canvas._chartInstance) {
                            canvas._chartInstance.destroy();
                        }

                        canvas._chartInstance = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: ['Completed', 'Pending'],
                                datasets: [{
                                    data: [completed, pending],
                                    // no explicit colors: Chart will use defaults; customize if you want
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    },
                                    tooltip: {
                                        enabled: true
                                    }
                                }
                            }
                        });
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>