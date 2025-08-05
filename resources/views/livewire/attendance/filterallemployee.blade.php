<div>
    <!-- Filter Section -->
    @can('filter allemployeesattendance')
        <div class="">
            @if (session('calendar') == 'BS')
                <form action="" method="GET">
                    <div class="flex items-center justify-end mb-4 gap-2">
                        <div class="xl:col-span-6 col-span-12 mt-4 relative w-[15%]">
                            <select class="form-control w-full  !px-3 !py-2 !text-sm !rounded-md" name="branch"
                                aria-label="Filter by status">
                                <option value="">All Branches</option>
                                @foreach ($branches as $b)
                                    <option {{ $branch == $b->id ? 'selected' : '' }} value="{{ $b->id }}">
                                        {{ $b->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="xl:col-span-6 col-span-12 mt-4 relative">
                            <input
                                class="nepali-datepicker form-control w-full !px-3 !py-2 !text-sm !rounded-l-md !border-r-0"
                                id="" name="date" type="text" aria-label="Search by date" autocomplete="off"
                                placeholder="Date" value="{{ $date }}">
                        </div>

                        <div class="xl:col-span-6 col-span-12 mt-4 relative w-[8%]">
                            <select class="form-control w-full !px-3 !py-2 !text-sm !rounded-md"
                                aria-label="Filter by status" name="status">
                                <option {{ $status == 'All' ? 'selected' : '' }} value="All">All Status</option>
                                <option {{ $status == 'Present' ? 'selected' : '' }} value="Present">Present</option>
                                <option {{ $status == 'Absent' ? 'selected' : '' }} value="Absent">Absent</option>
                                <option {{ $status == 'Leave' ? 'selected' : '' }} value="Leave">Leave</option>
                            </select>
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
                    <div class="flex items-center justify-end gap-2 w-full">

                        <!-- Branch Dropdown -->
                        <select class="form-control w-full !px-3 !py-2 !text-sm !rounded-md" aria-label="Filter by branch"
                            wire:model.live="branch" style="max-width: 135px;">
                            <option value="">All Branches</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name ?? '' }}</option>
                            @endforeach
                        </select>

                        <input class="form-control w-full !px-3 !py-2 !text-sm !rounded-l-md !border-r-0" id="date"
                            type="text" wire:model.live="searchTerms" aria-label="Search by date" autocomplete="off"
                            placeholder="Search by date" style="max-width: 140px;">

                        <select class="form-control w-full !px-3 !py-2 !text-sm !rounded-md" aria-label="Filter by status"
                            wire:model.live="status" style="max-width: 128px;">
                            <option value="All">All Status</option>
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                            <option value="Leave">Leave</option>
                        </select>

                        <!-- Clear Button -->
                        @if ($searchTerms || $branch)
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

        <!-- Table Section -->
        <div class="box-body">
            <div class="table-responsive">
                <table class="table whitespace-nowrap min-w-full">
                    <thead>
                        <tr class="border-b border-defaultborder">
                            <th class="text-start px-4 py-2" scope="col">#</th>
                            <th class="text-start px-4 py-2" scope="col">Employee</th>
                            <th class="text-start px-4 py-2" scope="col">Date</th>
                            <th class="text-start px-4 py-2" scope="col">Check In</th>
                            <th class="text-start px-4 py-2" scope="col">Check Out</th>
                            <th class="text-start px-4 py-2" scope="col">Break Start</th>
                            <th class="text-start px-4 py-2" scope="col">Break End</th>
                            <th class="text-start px-4 py-2" scope="col">Total Worked</th>
                            {{-- <th class="text-start px-4 py-2" scope="col">Overtime Minutes</th>
                            <th class="text-start px-4 py-2" scope="col">Short Minutes</th> --}}
                            <th class="text-start px-4 py-2" scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($attendances->isNotEmpty())
                            @php
                                $totalPresent = 0;
                                $totalAbsent = 0;
                                $totalLeave = 0;
                            @endphp
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
                                @endphp
                                <tr class="{{ $loop->last ? '' : 'border-b border-defaultborder' }} cursor-pointer attendance-row"
                                    attendance="{{ json_encode($attendance) }}"
                                    date="{{ session('calendar') == 'BS' ? App\Services\DateService::ADToBS($attendance->date ?? '') : $attendance->date ?? '-' }}">
                                    <th class="px-4 py-2" scope="row">{{ $loop->iteration }}</th>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <span class="avatar avatar-xs me-2 online avatar-rounded">
                                                <a class="fancybox" data-fancybox="demo"
                                                    href="{{ $attendance->image ?? '' }}">
                                                    <img src="{{ $attendance->image ?? '' }}" alt="profile">
                                                </a>
                                            </span>
                                            <div>
                                                {{ $attendance->full_name ?? '' }} <br>
                                                <span
                                                    class="text-xs text-gray-600">{{ $attendance->branch ?? '' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-xs">
                                        @if (session('calendar') == 'BS')
                                            {{ App\Services\DateService::ADToBS($attendance->date ?? '') }}
                                        @else
                                            {{ $attendance->date ?? '-' }}
                                        @endif
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
                                    {{-- <td class="px-4 py-2 text-xs">{{ $attendance->overtime_minute ?? '-' }}</td>
                                    <td class="px-4 py-2 text-xs">{{ $attendance->short_minutes ?? '-' }}</td> --}}
                                    <td class="px-4 py-2">
                                        @if ($attendance->type === 'Absent')
                                            <span
                                                class="bg-red-500 px-2 py-1 text-white text-xs rounded-sm">Absent</span>
                                        @endif
                                        @if ($attendance->type === 'Leave')
                                            <span
                                                class="bg-yellow-500 px-2 py-1 text-white text-xs rounded-sm">Leave</span>
                                        @endif
                                        @if ($attendance->type === 'Holiday')
                                            <span
                                                class="bg-gray-800 px-2 py-1 text-white text-xs rounded-sm">Holiday</span>
                                        @endif
                                        @if ($attendance->type === 'Weekend')
                                            <span
                                                class="bg-gray-500 px-2 py-1 text-white text-xs rounded-sm">Weekend</span>
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
                                <td colspan="9"
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

        {{-- {{ $attendances->links('vendor.pagination.custom') }} --}}
    </div>
    <div class="flex gap-4">

        <div class=" mb-6 p-4 bg-white rounded-lg shadow-sm">
            <div class="flex">
                <div class="text-sm font-medium text-gray-800">
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
                    Total Employees: <span class="font-semibold">{{ $attendances->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
