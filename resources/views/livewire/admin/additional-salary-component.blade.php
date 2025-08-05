<div>
    @can('filter compensation')
        <div class="">
            @if (session('calendar') == 'BS')
                <form action="" method="GET">
                    <div class="flex items-center justify-end mb-4 gap-2">
                        <div class="xl:col-span-6 col-span-12 mt-4 relative w-[15%]">
                            <select class="form-control w-full  !px-3 !py-2 !text-sm !rounded-md" name="employee"
                                aria-label="Filter by status">
                                <option value="all">All Employees</option>
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
                <div class="flex items-center justify-end mb-4 mt-4">
                    <div class="flex items-center w-full max-w-xl gap-4">
                        <select class="form-control w-80 !px-3 !py-2 !text-sm !rounded-md" aria-label="Filter by branch"
                            wire:model.live="employee">
                            <option value="all">All Employees</option>
                            @foreach ($employees as $e)
                                <option value="{{ $e->id }}">{{ $e->full_name ?? '' }}</option>
                            @endforeach
                        </select>

                        <input class="form-control w-80 !px-3 !py-2 !text-sm !rounded-l-md !border-r-0"
                            id="daterangeCalendar" type="text" wire:model.live="dateRange" aria-label="Search by date"
                            autocomplete="off" placeholder="Filter by Date Range">

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

    <div class="xl:col-span-6 col-span-12 mt-4">
        <div class="box">
            <div class="box-header justify-between">
                <div class="box-title">
                    Compensations
                </div>
                @can('create compensation')
                    <div class="prism-toggle">
                        <button
                            class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2 openSalaryModal"
                            type="button">
                            New Compensation <i class="ri-add-line"></i>
                        </button>
                    </div>
                @endcan
            </div>
            <div class="box-body">
                <div>
                    <div class="box custom-box">
                        <div class="box-body">
                            <div class="table-responsive" style="overflow: visible">
                                <table class="table whitespace-nowrap min-w-full">
                                    <thead>
                                        <tr class="border-b border-defaultborder">
                                            <th class="text-start px-4 py-2 w-3" scope="col">#</th>
                                            <th class="text-start px-4 py-2" scope="col">Title</th>
                                            <th class="text-start px-4 py-2" scope="col">Employee</th>
                                            <th class="text-start px-4 py-2" scope="col">Date</th>
                                            <th class="text-start px-4 py-2" scope="col">Amount</th>
                                            <th class="text-start px-4 py-2" scope="col">Type</th>
                                            <th class="text-start px-4 py-2" scope="col">Remarks</th>
                                            <th class="text-start px-4 py-2" scope="col">Is Taxable?</th>
                                            <th class="text-start px-4 py-2" scope="col">Added On</th>
                                            <th class="text-start px-4 py-2 w-20" scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($components->isNotEmpty())
                                            @foreach ($components as $key => $component)
                                                @php
                                                    $component->month =
                                                        session('calendar') == 'BS'
                                                            ? App\Services\DateService::ADToBS($component->month)
                                                            : $component->month;
                                                @endphp
                                                <tr class="{{ $loop->last ? '' : 'border-b border-defaultborder' }}">
                                                    <th class="px-4 py-2" scope="row">{{ $key + 1 }}
                                                    </th>
                                                    <td class="px-4 py-2 text-xs">{{ $component->title ?? '-' }}</td>
                                                    <td class="px-4 py-2 text-xs">
                                                        {{ $component->employee->first_name ?? '' }}
                                                        {{ $component->employee->last_name ?? '' }}</td>
                                                    <td class="px-4 py-2 text-xs">
                                                        {{ $component->month }}
                                                    </td>
                                                    <td class="px-4 py-2 text-xs">{{ $component->amount ?? '-' }}</td>
                                                    <td class="px-4 py-2 text-xs">
                                                        {{ ucfirst($component->type ?? '-') }}
                                                    </td>
                                                    <td class="px-4 py-2 text-xs">{{ $component->remarks ?? '-' }}</td>
                                                    <td class="px-4 py-2 text-xs">
                                                        {{ $component->is_taxable == 1 ? 'Yes' : 'No' ?? '-' }}
                                                    </td>
                                                    <td class="px-4 py-2 text-xs">
                                                        {{ session('calendar') == 'BS' ? App\Services\DateService::ADToBS(date('Y-m-d', strtotime($component->created_at))) : date('Y-m-d', strtotime($component->created_at)) }}
                                                        {{ date('H:i:s', strtotime($component->created_at)) }}
                                                    </td>

                                                    @canany(['edit compensation', 'delete compensation'])
                                                        <td class="text-end px-4 py-2">
                                                            <div class="relative inline-flex" x-cloak
                                                                x-data="{ open: false }">
                                                                <button
                                                                    class="flex justify-center items-center size-8 text-sm font-semibold rounded-md border shadow-md border-gray-400 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-none"
                                                                    @click="open = !open">
                                                                    <svg class="flex-none size-4 text-gray-600"
                                                                        xmlns="http://www.w3.org/2000/svg" width="24"
                                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                        <circle cx="12" cy="12" r="1" />
                                                                        <circle cx="12" cy="5" r="1" />
                                                                        <circle cx="12" cy="19" r="1" />
                                                                    </svg>
                                                                </button>

                                                                <div class="action_button absolute z-50 bg-white shadow-md rounded-md mt-2 w-40 transition duration-150 ease-in-out max-w-fit"
                                                                    id="" x-show="open"
                                                                    @click.away="open = false"
                                                                    @close-dropdown.window="open = false" x-transition
                                                                    style="top: 25px; right: -14px;">

                                                                    @can('edit compensation')
                                                                        <a class="text-sm action-btn hover:bg-gray-100 ti-btn-wave text-gray-700 w-full flex !justify-start !border-b !border-gray-400 open-edit-compensation-modal"
                                                                            component='@json($component)'
                                                                            href="javascript:void(0);">
                                                                            Edit
                                                                        </a>
                                                                    @endcan

                                                                    @can('delete compensation')
                                                                        <form
                                                                            action="{{ route('compensation.destroy', $component->id) }}"
                                                                            method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button
                                                                                class="text-sm action-btn hover:bg-red-100 ti-btn-wave text-red-600 w-full flex !justify-start px-4 py-2 delete_button"
                                                                                type="submit">
                                                                                Delete
                                                                            </button>
                                                                        </form>
                                                                    @endcan
                                                                </div>
                                                            </div>
                                                        </td>
                                                    @endcanany
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="10"
                                                    style="text-align: center; height: 100px; vertical-align: middle; color: #6b7280; display: table-cell;">
                                                    <div
                                                        style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%;">
                                                        <p class="text-lg font-semibold">No data available</p>
                                                        <p class="mt-2 text-sm">There are no records to display at the
                                                            moment.
                                                            Please check again later.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{ $components->links('vendor.livewire.tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
