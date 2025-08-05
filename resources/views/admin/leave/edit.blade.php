@extends('layouts.admin.master')
@section('content')
    <div class="xl:col-span-6 col-span-12 mt-4">
        <div class="box">
            <div class="box-header justify-between">
                <div class="box-title">
                    Leave Request
                </div>
                <div class="prism-toggle">
                    <a class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2"
                        href="{{ route('leaves') }}">
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
                            {{ $leave->employee->full_name ?? '' }}
                            ({{ $leave->employee->branch->name ?? '' }})
                        </div>
                    </div>

                    <!-- Check In -->
                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Date</label>
                        <div class="text-gray-700 dark:text-gray-300">
                            @if (session('calendar') == 'BS')
                                {{ $leave->from_date == $leave->to_date ? App\Services\DateService::ADToBS($leave->from_date ?? '') : App\Services\DateService::ADToBS($leave->from_date ?? '') . ' to ' . App\Services\DateService::ADToBS($leave->to_date ?? '') }}
                            @else
                                {{ $leave->from_date == $leave->to_date ? $leave->from_date : "{$leave->from_date} to {$leave->to_date}" }}
                            @endif
                        </div>
                    </div>

                    <!-- Check Out -->
                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Leave Type</label>
                        <div class="text-gray-700 dark:text-gray-300">
                            {{ $leave->leavetype->name ?? '-' }}
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Days</label>
                        <div class="text-gray-700 dark:text-gray-300">
                            {{ $leave->no_of_days ?? '-' }}
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="md:col-span-12 col-span-12">
                        <label class="form-label">Reason</label>
                        <div class="text-gray-700 dark:text-gray-300">
                            {{ $leave->reason ?? '' }}
                        </div>
                    </div>

                    <div class="md:col-span-6 col-span-6">
                        <label class="form-label">Leave Status</label>
                        <div class="text-gray-700 dark:text-gray-300">
                            {{ $leave->status ?? '' }}
                        </div>
                    </div>

                    @if ($leave->action_by)
                        <div class="md:col-span-6 col-span-6">
                            <label class="form-label">Action By</label>
                            <div class="text-gray-700 dark:text-gray-300">
                                {{ $leave->actionBy->first_name ?? '' }}
                                {{ $leave->actionBy->last_name ?? '' }}
                            </div>
                        </div>
                    @endif

                    @if ($leave->action_reason)
                        <div class="md:col-span-12 col-span-12">
                            <label class="form-label">Reject Reason</label>
                            <div class="text-gray-700 dark:text-gray-300">
                                {{ $leave->action_reason ?? '' }}
                            </div>
                        </div>
                    @endif

                    <div class="md:col-span-12 col-span-12">
                        <label class="form-label">Applied On</label>
                        <div class="text-gray-700 dark:text-gray-300">
                            @if (session('calendar') == 'BS')
                                {{ App\Services\DateService::ADToBS($leave->created_at->format('Y-m-d') ?? '') }}
                            @else
                                {{ $leave->created_at->format('Y-m-d') }}
                            @endif
                            {{ $leave->created_at->format('h:i A') }}
                        </div>
                    </div>
                </div>
                <!-- Admin Response Form -->
                @if ($leave->status == 'Approved' || $leave->status == 'Cancelled' || $leave->status == 'Rejected')
                    <div class="md:col-span-12 col-span-12">
                        <div class="text-red-500 dark:text-red-400 font-semibold inline-flex items-center">
                            <i class="mr-1">Note: You cannot process this request once it has been approved or
                                cancelled or rejected.</i>
                        </div>
                    </div>
                @else
                    <hr>
                    <form class="grid grid-cols-12 gap-4 mt-4" action="{{ route('leaves.update', $leave->id) }}"
                        method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Status (Admin Input) -->
                        <div class="md:col-span-6 col-span-12">
                            <label class="form-label">Status</label>
                            <div class="relative">
                                <select class="ti-form-select rounded-sm !py-2 !px-3" id="status-select" name="status">
                                    <option {{ $leave->status == 'Pending' ? 'selected' : '' }} value="Pending">
                                        Pending
                                    </option>
                                    <option {{ $leave->status == 'Approved' ? 'selected' : '' }} value="Approved">
                                        Approve
                                    </option>
                                    <option {{ $leave->status == 'Rejected' ? 'selected' : '' }} value="Rejected">
                                        Reject
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Rejection Reason (Admin Input, Conditionally Displayed) -->
                        <div class="md:col-span-12 col-span-12" style="display: none" id="rejection-reason">
                            <label class="form-label">Rejection Reason</label>
                            <div class="relative">
                                <textarea class="sm:p-5 py-3 px-4 ti-form-input" rows="2" name="action_reason">{{ old('action_reason', $leave->action_reason) }}</textarea>
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
@endsection
