<div>
    <!-- Filter Section -->
    @can('filter attendancerequest')
        <div class="relative">
            @if (session('calendar') == 'BS')
                <form action="" method="GET">
                    <div class="flex items-center justify-end mb-4 gap-2">
                        <div class="xl:col-span-6 col-span-12 mt-4 relative w-[15%]">
                            <select class="form-control w-80 !px-3 !py-2 !text-sm !rounded-md" aria-label="Filter by name"
                                name="employee">
                                <option value="">All Employees</option>
                                @foreach ($employees as $e)
                                    <option {{ $employee == $e->id ? 'selected' : '' }} value="{{ $e->id }}">
                                        {{ $e->full_name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="xl:col-span-6 col-span-12 mt-4 relative w-[15%]">
                            <select class="form-control w-full  !px-3 !py-2 !text-sm !rounded-md" name="status"
                                aria-label="Filter by status">
                                <option value="">All Status</option>
                                <option {{ $status == 'Pending' ? 'selected' : '' }} value="Pending">Pending</option>
                                <option {{ $status == 'Approved' ? 'selected' : '' }} value="Approved">Approved</option>
                                <option {{ $status == 'Cancelled' ? 'selected' : '' }} value="Cancelled">Cancelled</option>
                                <option {{ $status == 'Rejected' ? 'selected' : '' }} value="Rejected">Rejected</option>
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
                    <div class="flex items-center justify-end w-full max-w-xl gap-4">

                        <select class="form-control w-80 !px-3 !py-2 !text-sm !rounded-md" aria-label="Filter by branch"
                            wire:model.live="employee">
                            <option value="">All Employees</option>
                            @foreach ($employees as $e)
                                <option value="{{ $e->id }}">{{ $e->full_name ?? '' }}</option>
                            @endforeach
                        </select>

                        <select class="form-control !px-3 !py-2 !text-sm !rounded-md" aria-label="Filter by status"
                            wire:model.live="status" style="max-width: 128px;">
                            <option value="">All Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Cancelled">Cancelled</option>
                            <option value="Rejected">Rejected</option>
                        </select>

                        <input class="form-control w-full !px-3 !py-2 !text-sm !rounded-l-md !border-r-0" id="daterange"
                            type="text" wire:model.live="dateRange" aria-label="Search by date" autocomplete="off"
                            placeholder="Search by date">

                        @if ($dateRange)
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
                Attendance requests
                @if ($attendance_requests->isNotEmpty())
                    <span
                        class="badge bg-light text-default rounded-full ms-1 text-[0.75rem] align-middle">{{ $attendance_requests->total() }}</span>
                @endif
            </div>
        </div>

        <!-- Table Section -->
        <div class="box-body">
            <div class="table-responsive" style="overflow: visible">
                <table class="table whitespace-nowrap min-w-full">
                    <thead>
                        <tr class="border-b border-defaultborder">
                            <th class="text-start px-4 py-2 w-3" scope="col">#</th>
                            <th class="text-start px-4 py-2" scope="col">Employee</th>
                            <th class="text-start px-4 py-2" scope="col">Requested Date</th>
                            <th class="text-start px-4 py-2" scope="col">Check In</th>
                            <th class="text-start px-4 py-2" scope="col">Check Out</th>
                            <th class="text-start px-4 py-2" scope="col">Reason</th>
                            <th class="text-start px-4 py-2" scope="col">Applied On</th>
                            <th class="text-start px-4 py-2" scope="col">Status</th>
                            <th class="text-start px-4 py-2 w-20" scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($attendance_requests->isNotEmpty())
                            @foreach ($attendance_requests as $key => $attendance)
                                <tr class="{{ $loop->last ? '' : 'border-b border-defaultborder' }}">
                                    <th class="px-4 py-2" scope="row">{{ $loop->iteration }}</th>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <span class="avatar avatar-xs me-2 online avatar-rounded">
                                                <a class="fancybox" data-fancybox="demo"
                                                    href="{{ $attendance->employee->image ?? asset('assets/images/profile.jpg') }}">
                                                    <img src="{{ $attendance->employee->image ?? asset('assets/images/profile.jpg') }}"
                                                        alt="profile">
                                                </a>
                                            </span>
                                            <div class="flex-col">
                                                <div class="">
                                                    {{ $attendance->employee ? $attendance->employee->full_name : '' }}
                                                </div>
                                                <i
                                                    class="text-xs text-gray-600">{{ $attendance->employee ? $attendance->employee->branch->name : 'Deleted User' }}</i>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-xs">
                                        @if (session('calendar') == 'BS')
                                            {{ App\Services\DateService::ADToBS($attendance->date ?? '') }}
                                        @else
                                            {{ $attendance->date ?? '' }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-xs">{{ $attendance->checkin ?? '-' }}</td>
                                    <td class="px-4 py-2 text-xs">{{ $attendance->checkout ?? '-' }}</td>
                                    <td class="px-4 py-2 text-xs" style="white-space: normal">
                                        {{ $attendance->reason ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2" style="white-space: normal">
                                        @if (session('calendar') == 'BS')
                                            <span
                                                class="text-xs">{{ App\Services\DateService::ADToBS($attendance->created_at->format('Y-m-d') ?? '') }}</span>
                                        @else
                                            <span
                                                class="text-xs">{{ $attendance->created_at->format('Y-m-d') ?? '-' }}</span>
                                        @endif
                                        <span
                                            class="text-xs">{{ $attendance->created_at->format('h:i A') ?? '-' }}</span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span
                                            class="{{ $attendance->status == 'Approved' ? 'bg-green-500' : 'bg-red-500' }} text-xs px-2 py-1 text-white rounded-sm">{{ $attendance->status ?? '-' }}</span>
                                    </td>
                                    @can('manage attendancerequest')
                                        <td class="text-end px-4 py-2">
                                            <div class="relative inline-flex" x-cloak x-data="{ open: false }">
                                                <button
                                                    class="flex justify-center items-center size-8 text-sm font-semibold rounded-md border shadow-md border-gray-400 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-none"
                                                    @click="open = !open">
                                                    <svg class="flex-none size-4 text-gray-600"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="12" r="1" />
                                                        <circle cx="12" cy="5" r="1" />
                                                        <circle cx="12" cy="19" r="1" />
                                                    </svg>
                                                </button>

                                                <div class="absolute z-20 bg-white shadow-md top-6 rounded-md mt-2 w-40 transition duration-150 ease-in-out max-w-fit"
                                                    x-show="open" @click.away="open = false" x-transition
                                                    style="top: 25px; right: -14px;">
                                                    <a class="text-sm action-btn hover:bg-gray-100 ti-btn-wave text-gray-700 w-full flex !justify-start !border-b !border-gray-400"
                                                        href="{{ route('attendance.request.edit', $attendance->id) }}">
                                                        Manage Request
                                                    </a>
                                                </div>
                                            </div>

                                        </td>
                                    @endcan
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

        {{ $attendance_requests->appends(request()->query())->links('vendor.livewire.tailwind') }}
    </div>
</div>
