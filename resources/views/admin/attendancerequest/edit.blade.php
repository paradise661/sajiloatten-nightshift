@extends('layouts.admin.master')
@section('content')
    <div class="xl:col-span-6 col-span-12 mt-4">
        <div class="box">
            <div class="box-header justify-between">
                <div class="box-title">
                    Attendance Request
                </div>
                <div class="prism-toggle">
                    <a class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2"
                        href="{{ route('attendance.request') }}">
                        <i class="ri-arrow-left-line"></i> Back
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="grid grid-cols-12 gap-4 mb-6">
                    <!-- Employee Information -->
                    <div class="md:col-span-12 col-span-12">
                        <label class="form-label">Employee</label>
                        <div class="text-gray-700 dark:text-gray-300">
                            {{ $attendancerequest->employee->full_name ?? '' }}
                            ({{ $attendancerequest->employee->branch->name ?? '' }})
                        </div>
                    </div>

                    <!-- Check In -->
                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Check In</label>
                        <div class="text-gray-700 dark:text-gray-300">
                            {{ $attendancerequest->checkin ?? '' }}
                        </div>
                    </div>

                    <!-- Check Out -->
                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Check Out</label>
                        <div class="text-gray-700 dark:text-gray-300">
                            {{ $attendancerequest->checkout ?? '' }}
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Date</label>
                        <div class="text-gray-700 dark:text-gray-300">
                            @if (session('calendar') == 'BS')
                                {{ App\Services\DateService::ADToBS($attendancerequest->date ?? '') }}
                            @else
                                {{ $attendancerequest->date ?? '' }}
                            @endif
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="md:col-span-12 col-span-12">
                        <label class="form-label">Reason</label>
                        <div class="text-gray-700 dark:text-gray-300">
                            {{ $attendancerequest->reason ?? '-' }}
                        </div>
                    </div>

                    <!-- Attendance Status -->
                    <div class="md:col-span-6 col-span-6">
                        <label class="form-label">Attendance Status</label>
                        <div class="text-gray-700 dark:text-gray-300">
                            {{ $attendancerequest->status ?? '' }}
                        </div>
                    </div>

                    <!-- Action By -->
                    @if ($attendancerequest->action_by)
                        <div class="md:col-span-6 col-span-6">
                            <label class="form-label">Action By</label>
                            <div class="text-gray-700 dark:text-gray-300">
                                {{ $attendancerequest->actionBy->first_name ?? '' }}
                                {{ $attendancerequest->actionBy->last_name ?? '' }}
                            </div>
                        </div>
                    @endif

                    @if ($attendancerequest->action_reason)
                        <div class="md:col-span-12 col-span-12">
                            <label class="form-label">Reject Reason</label>
                            <div class="text-gray-700 dark:text-gray-300">
                                {{ $attendancerequest->action_reason ?? '' }}
                            </div>
                        </div>
                    @endif

                    <!-- Applied On -->
                    <div class="md:col-span-12 col-span-12">
                        <label class="form-label">Applied On</label>
                        <div class="text-gray-700 dark:text-gray-300">
                            @if (session('calendar') == 'BS')
                                {{ App\Services\DateService::ADToBS($attendancerequest->created_at->format('Y-m-d') ?? '') }}
                            @else
                                {{ $attendancerequest->created_at->format('Y-m-d') ?? '' }}
                            @endif
                            {{ $attendancerequest->created_at->format('h:i A') ?? '' }}
                        </div>
                    </div>

                    <!-- Location Map -->
                    @if ($attendancerequest->latitude && $attendancerequest->longitude)
                        <div class="col-span-6 mt-2">
                            <label class="block text-gray-700 text-sm font-semibold mb-2">Location</label>
                            <div class="rounded-2xl shadow-lg border border-gray-200" id="map" style="height: 400px;">
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Admin Response Form -->
                @if ($attendancerequest->status == 'Approved' || $attendancerequest->status == 'Rejected')
                    <div class="md:col-span-12 col-span-12">
                        <div class="text-red-500 dark:text-red-400 font-semibold inline-flex items-center">
                            <i class="mr-1">Note: You cannot process this request once it has been approved or
                                rejected.</i>
                        </div>
                    </div>
                @else
                    <hr>
                    <form class="grid grid-cols-12 gap-4 mt-4"
                        action="{{ route('attendance.request.update', $attendancerequest->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Status -->
                        <div class="md:col-span-6 col-span-12">
                            <label class="form-label">Status</label>
                            <div class="relative">
                                <select class="ti-form-select rounded-sm !py-2 !px-3" id="status-select" name="status">
                                    <option {{ $attendancerequest->status == 'Pending' ? 'selected' : '' }}
                                        value="Pending">Pending</option>
                                    <option {{ $attendancerequest->status == 'Approved' ? 'selected' : '' }}
                                        value="Approved">Approve</option>
                                    <option {{ $attendancerequest->status == 'Rejected' ? 'selected' : '' }}
                                        value="Rejected">Reject</option>
                                </select>
                            </div>
                        </div>

                        <!-- Rejection Reason -->
                        <div class="md:col-span-12 col-span-12" style="display: none" id="rejection-reason">
                            <label class="form-label">Rejection Reason</label>
                            <div class="relative">
                                <textarea class="sm:p-5 py-3 px-4 ti-form-input" rows="2" name="action_reason">{{ old('action_reason', $attendancerequest->action_reason) }}</textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-span-12">
                            <button class="ti-btn ti-btn-primary-full submitbtn" type="submit">
                                Submit
                                <span style="display: none" class="ti-spinner text-white !w-[1rem] !h-[1rem]" role="status"
                                    aria-label="loading"></span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('form').on('submit', function() {
                let button = $(this).find('.submitbtn');
                let spinner = button.find('.ti-spinner');

                button.prop('disabled', true);
                spinner.show();
            });
        });

        $(document).ready(function() {
            const toggleRejectionReason = () => {
                const isRejected = $("#status-select").val() === "Rejected";
                $("#rejection-reason").toggle(isRejected);
                if (!isRejected) $("textarea[name='action_reason']").val("");
            };

            $("#status-select").change(toggleRejectionReason);
            toggleRejectionReason();
        });
    </script>

    <script>
        $(function() {
            @if ($attendancerequest->latitude && $attendancerequest->longitude)
                const lat = {{ $attendancerequest->latitude }};
                const lng = {{ $attendancerequest->longitude }};
                const map = L.map('map').setView([lat, lng], 16);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(map);

                // Improved custom icon
                const customIcon = L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -30]
                });

                // Add marker
                L.marker([lat, lng], {
                        icon: customIcon
                    })
                    .addTo(map)
                    .bindPopup("<strong>Requested Location</strong>")
                    .openPopup();
            @endif
        });
    </script>
@endsection
