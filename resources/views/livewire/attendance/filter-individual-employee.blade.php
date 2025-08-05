<div>
    <!-- Filter Section -->
    @can('filter individualemployeeattendance')
        <div class="">
            @if (session('calendar') == 'BS')
                <form action="" method="GET">
                    <div class="flex items-center justify-end mb-4 gap-2">
                        <div class="xl:col-span-6 col-span-12 mt-4 relative w-[15%]">
                            <select class="form-control w-full  !px-3 !py-2 !text-sm !rounded-md" name="employee"
                                aria-label="Filter by status">
                                @foreach ($employees as $e)
                                    <option {{ $employee == $e->id ? 'selected' : '' }} value="{{ $e->id }}">
                                        {{ $e->full_name ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="xl:col-span-6 col-span-12 mt-4 relative">
                            <input
                                class="nepali-datepicker form-control w-full !px-3 !py-2 !text-sm !rounded-l-md !border-r-0"
                                id="fromDate" name="fromDate" type="text" aria-label="Search by date" autocomplete="off"
                                placeholder="From" value="{{ $fromDate }}">
                        </div>
                        <div class="xl:col-span-6 col-span-12 mt-4 relative">
                            <input
                                class="nepali-datepicker form-control w-full !px-3 !py-2 !text-sm !rounded-l-md !border-r-0"
                                id="" type="text" name="toDate" aria-label="Search by date" autocomplete="off"
                                placeholder="To" value="{{ $toDate }}">
                        </div>
                        <div class="xl:col-span-6 col-span-12 mt-4 relative">
                            <button class="ti-btn !mb-0 ti-btn-primary-full !rounded-r-md !px-4" type="submit"
                                aria-label="Filter">
                                Filter
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <div class="flex items-center justify-end mb-4">
                    <div class="flex items-center w-full max-w-xl gap-4">
                        <select class="form-control w-80 !px-3 !py-2 !text-sm !rounded-md" aria-label="Filter by branch"
                            wire:model.live="employee">
                            @foreach ($employees as $e)
                                <option value="{{ $e->id }}">{{ $e->full_name ?? '' }}</option>
                            @endforeach
                        </select>

                        <input class="form-control w-80 !px-3 !py-2 !text-sm !rounded-l-md !border-r-0" id="daterange"
                            type="text" wire:model.live="dateRange" aria-label="Search by date" autocomplete="off"
                            placeholder="Filter by Date Range">

                        @if ($dateRange || $employee)
                            <button class="ti-btn !mb-0 ti-btn-danger-full !rounded-r-md !px-4" wire:click="clearFilters"
                                type="button" aria-label="Clear Filter">
                                Clear <i class="ri-close-line"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endcan

    @if ($employees->count())
        <div class="box custom-box">
            <div class="box-header justify-between">
                <div class="box-title">
                    Attendances
                    @if ($attendances->isNotEmpty())
                        <span
                            class="badge bg-light text-default rounded-full ms-1 text-[0.75rem] align-middle">{{ $attendances->count() }}</span>
                    @endif
                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table whitespace-nowrap min-w-full">
                        <thead>
                            <tr class="border-b border-defaultborder">
                                <th class="text-start px-4 py-2" scope="col">Date</th>
                                <th class="text-start px-4 py-2" scope="col">Check In</th>
                                <th class="text-start px-4 py-2" scope="col">Check Out</th>
                                <th class="text-start px-4 py-2" scope="col">Break Start</th>
                                <th class="text-start px-4 py-2" scope="col">Break End</th>
                                <th class="text-start px-4 py-2" scope="col">Total Worked</th>
                                <th class="text-start px-4 py-2" scope="col">Total Break</th>
                                <th class="text-start px-4 py-2" scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalPresent = 0;
                                $totalAbsent = 0;
                                $totalLeave = 0;
                                $totalHolidays = 0;
                                $totalWeekends = 0;
                            @endphp
                            @if ($attendances->isNotEmpty())
                                @foreach ($attendances as $key => $attendance)
                                    @php
                                        if ($attendance->type === 'Present') {
                                            $totalPresent++;
                                        }
                                        if ($attendance->type === 'Absent') {
                                            $totalAbsent++;
                                        }
                                        if ($attendance->type === 'Leave') {
                                            $totalLeave++;
                                        }
                                        if ($attendance->type === 'Holiday') {
                                            $totalHolidays++;
                                        }
                                        if ($attendance->type === 'Weekend') {
                                            $totalWeekends++;
                                        }

                                        $requested = \App\Models\AttendanceRequest::where(
                                            'user_id',
                                            $attendance->user_id,
                                        )
                                            ->where('date', $attendance->date)
                                            ->where('status', 'Approved')
                                            ->get(['checkin', 'checkout']);
                                        $hasCheckinRequested = $requested->contains(function ($item) {
                                            return !is_null($item->checkin);
                                        });

                                        $hasCheckoutRequested = $requested->contains(function ($item) {
                                            return !is_null($item->checkout);
                                        });

                                    @endphp
                                    <tr class="{{ $loop->last ? '' : 'border-b border-defaultborder' }} cursor-pointer individual-row"
                                        attendance="{{ json_encode($attendance) }}"
                                        attendancerule="{{ json_encode($attendanceRule) }}"
                                        date="{{ session('calendar') == 'BS' ? App\Services\DateService::ADToBS($attendance->date ?? '') : $attendance->date ?? '-' }}"
                                        day="{{ date('l', strtotime($attendance->date)) }}"
                                        checkin="{{ $attendance->checkin ?? '-' }}"
                                        checkout="{{ $attendance->checkout ?? '-' }}"
                                        breakstart="{{ $attendance->break_start ?? '-' }}"
                                        breakend="{{ $attendance->break_end ?? '-' }}"
                                        workedhours="{{ $attendance->worked_hours ?? '-' }}"
                                        totalbreak="{{ $attendance->total_break ?? '-' }}"
                                        late-checkin-reason="{{ $attendance->late_checkin_reason ?? '-' }}"
                                        early-checkout-reason="{{ $attendance->early_checkout_reason ?? '-' }}"
                                        checkin_requested="{{ $hasCheckinRequested }}"
                                        checkout_requested="{{ $hasCheckoutRequested }}"
                                        status="{{ $attendance->type }}">
                                        <td class="px-4 py-2">
                                            <div class="">
                                                @if (session('calendar') == 'BS')
                                                    {{ App\Services\DateService::ADToBS($attendance->date ?? '') }}
                                                @else
                                                    {{ $attendance->date ?? '-' }}
                                                @endif
                                            </div>
                                            <span
                                                class="text-xs text-gray-600">{{ date('l', strtotime($attendance->date)) }}</span>
                                        </td>
                                        <td
                                            class="px-4 py-2 text-xs {{ isset($attendance->late_checkin_reason) && $attendance->late_checkin_reason ? 'text-red-500' : '' }}">
                                            {{ $attendance->checkin ?? '-' }}</td>
                                        <td
                                            class="px-4 py-2 text-xs {{ isset($attendance->early_checkout_reason) && $attendance->early_checkout_reason ? 'text-red-500' : '' }}">
                                            {{ $attendance->checkout ?? '-' }}</td>
                                        <td class="px-4 py-2 text-xs">{{ $attendance->break_start ?? '-' }}</td>
                                        <td class="px-4 py-2 text-xs">{{ $attendance->break_end ?? '-' }}</td>
                                        <td class="px-4 py-2 text-xs">{{ $attendance->worked_hours ?? '-' }}</td>
                                        {{-- <td class="px-4 py-2 text-xs">{{ $attendance->overtime_minute ?? '-' }}</td> --}}
                                        <td class="px-4 py-2 text-xs">{{ $attendance->total_break ?? '-' }}</td>
                                        <td class="px-4 py-2">
                                            @if ($attendance->type === 'Absent')
                                                <span
                                                    class="bg-red-500 px-2 py-1 text-white text-xs rounded-sm">Absent</span>
                                            @endif
                                            @if ($attendance->type === 'Leave')
                                                <span
                                                    class="bg-yellow-500 px-2 py-1 text-white text-xs rounded-sm">Leave</span>
                                            @endif
                                            @if ($attendance->type === 'Weekend')
                                                <span
                                                    class="bg-gray-500 px-2 py-1 text-white text-xs rounded-sm">Weekend</span>
                                            @endif
                                            @if ($attendance->type === 'Holiday')
                                                <span
                                                    class="bg-gray-800 px-2 py-1 text-white text-xs rounded-sm">Holiday</span>
                                            @endif
                                            @if ($attendance->type === 'Present')
                                                <span
                                                    class="bg-green-500 px-2 py-1 text-white text-xs rounded-sm">Present</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8"
                                        style="text-align: center; height: 100px; vertical-align: middle; color: #6b7280; display: table-cell;">
                                        <div
                                            style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%;">
                                            <p class="text-lg font-semibold">No data available</p>
                                            <p class="mt-2 text-sm">There are no records to display at the moment.
                                                Please check again later.</p>
                                        </div>
                                    </td>
                                </tr>

                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-6 mb-6 p-4 bg-white rounded-lg shadow-sm">
            <div class="flex justify-between gap-6">
                <div class="text-sm font-medium text-gray-800">
                    Total Break Taken: <span class="font-semibold">{{ formatMinutesToHours($totalBreakTaken) }}</span>
                </div>

                <div class="text-sm font-medium text-gray-800">
                    Total Worked: <span class="font-semibold">{{ formatWorkedHours($totalWorkedHour) }}</span>
                </div>
            </div>
        </div>

        <div class="flex gap-4">

            <div class=" mb-6 p-4 bg-white rounded-lg shadow-sm">
                <div class="flex">
                    <div class="text-sm font-medium text-green-500">
                        Total Present: <span class="font-semibold">{{ $totalPresent }}</span>
                    </div>
                </div>
            </div>
            <div class=" mb-6 p-4 bg-white rounded-lg shadow-sm">
                <div class="flex">
                    <div class="text-sm font-medium text-gray-800">
                        Total Absent: <span class="font-semibold">{{ $totalAbsent }}</span>
                    </div>
                </div>
            </div>
            <div class=" mb-6 p-4 bg-white rounded-lg shadow-sm">
                <div class="flex">
                    <div class="text-sm font-medium text-gray-800">
                        Total Leave: <span class="font-semibold">{{ $totalLeave }}</span>
                    </div>
                </div>
            </div>
            <div class=" mb-6 p-4 bg-white rounded-lg shadow-sm">
                <div class="flex">
                    <div class="text-sm font-medium text-gray-800">
                        Total Public Holidays: <span class="font-semibold">{{ $totalHolidays }}</span>
                    </div>
                </div>
            </div>
            <div class=" mb-6 p-4 bg-white rounded-lg shadow-sm">
                <div class="flex">
                    <div class="text-sm font-medium text-gray-800">
                        Total Weekends: <span class="font-semibold">{{ $totalWeekends }}</span>
                    </div>
                </div>
            </div>
            <div class=" mb-6 p-4 bg-white rounded-lg shadow-sm">
                <div class="flex">
                    <div class="text-sm font-medium text-gray-800">
                        Total Days: <span class="font-semibold">{{ $attendances->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="mt-6 mb-6 p-6 bg-white rounded-lg shadow-sm">
            <div class="box-title">
                No Employees Added Yet
            </div>
        </div>
    @endif
</div>
