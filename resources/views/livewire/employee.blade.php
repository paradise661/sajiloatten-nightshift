<div>
    <div class="flex items-center justify-end mb-4">
        <div class="flex items-center w-full max-w-xl gap-2">
            <select class="form-control !px-3 !py-2 !text-sm !rounded-md" style="width: 45%;" aria-label="Filter by branch"
                wire:model.live="branch">
                <option value="">All Branches</option>
                @foreach ($branches as $e)
                    <option {{ $branch == $e->id ? 'selected' : '' }} value="{{ $e->id }}">
                        {{ $e->name ?? '' }}
                    </option>
                @endforeach
            </select>
            <select class="form-control !px-3 !py-2 !text-sm !rounded-md" style="width: 45%;"
                aria-label="Filter by status" wire:model.live="status">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Suspended">Suspended</option>
            </select>

            <input class="form-control w-full !px-3 !py-2 !text-sm !rounded-l-md !border-r-0" type="text"
                wire:model.live="searchTerms" aria-label="Search by anything" autocomplete="off"
                placeholder="Search by anything ...">

            <!-- Clear Button -->
            @if ($searchTerms)
                <button class="ti-btn !mb-0 ti-btn-danger-full !rounded-r-md !px-4" wire:click="clearFilters"
                    type="button" aria-label="Clear Filter">
                    Clear <i class="ri-close-line"></i>
                </button>
            @endif
        </div>
    </div>

    <div class="box custom-box">
        <div class="box-header justify-between">
            <div class="box-title">
                Employees
                @if ($employees->isNotEmpty())
                    <span
                        class="badge bg-light text-default rounded-full ms-1 text-[0.75rem] align-middle">{{ $employees->total() }}</span>
                @endif
            </div>
            @can('import excel')
                <div class="prism-toggle">
                    <a class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2"
                        href="{{ route('employees.import') }}">
                        Import From Excel <i class="ri-download-line"></i>
                    </a>
                </div>
            @endcan
            @can('create employee')
                <div class="prism-toggle">
                    <a class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2"
                        href="{{ route('employees.create') }}">
                        New Employee <i class="ri-add-line"></i>
                    </a>
                </div>
            @endcan
        </div>
        <div class="box-body">
            <div class="table-responsive" style="overflow: visible">
                <table class="table whitespace-nowrap min-w-full">
                    <thead>
                        <tr class="border-b border-defaultborder">
                            <th class="text-start px-4 py-2 w-3" scope="col">#</th>
                            <th class="text-start px-4 py-2" scope="col">Employee</th>
                            <th class="text-start px-4 py-2" scope="col">Contact</th>
                            <th class="text-start px-4 py-2" scope="col">Designation</th>
                            <th class="text-start px-4 py-2" scope="col">Department</th>
                            <th class="text-start px-4 py-2" scope="col">Status</th>
                            <th class="text-start px-4 py-2 w-20" scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($employees->isNotEmpty())
                            @foreach ($employees as $key => $employee)
                                <tr class="{{ $loop->last ? '' : 'border-b border-defaultborder' }}">
                                    <th class="px-4 py-2" scope="row">{{ $key + $employees->firstItem() }}
                                    </th>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center relative">
                                                <a class="fancybox" data-fancybox="demo"
                                                    href="{{ $employee->image ?? '' }}">
                                                    <img class="avatar avatar-xs online avatar-rounded"
                                                        src="{{ $employee->image ?? '' }}" alt="profile">
                                                </a>

                                                @if ($employee->platform === 'android')
                                                    <img class="w-4 h-4 ml-1 bg-white rounded-full shadow p-0.5"
                                                        src="{{ asset('assets/images/android.png') }}" alt="Android"
                                                        title="Android Device">
                                                @elseif ($employee->platform === 'ios')
                                                    <img class="w-3.5 h-3.5 ml-1 bg-white rounded-full shadow p-0.5"
                                                        src="{{ asset('assets/images/ios.png') }}" alt="iOS"
                                                        title="iOS Device">
                                                @else
                                                    <div class="w-4 h-4 absolute -top-1 -right-1"></div>
                                                @endif
                                            </div>

                                            <div>
                                                <span class="font-medium">{{ $employee->full_name ?? '' }}</span><br>
                                                <i class="text-xs text-gray-600">
                                                    {{ $employee->branch->name ?? 'Deleted User' }}
                                                </i>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-2 text-xs">
                                        <div class="">
                                            {{ $employee->email ?? '-' }}
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            {{ $employee->phone ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-xs">{{ $employee->designation ?? '-' }}</td>
                                    <td class="px-4 py-2 text-xs">
                                        {{ $employee->department->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-xs">
                                        <span
                                            class="px-2 py-1 text-white rounded-sm {{ $employee->status == 'Active' ? 'bg-green-500' : 'bg-gray-500' }}">
                                            {{ $employee->status ?? '-' }}
                                        </span>
                                    </td>
                                    @canany([
                                        'edit employee',
                                        'reset device',
                                        'app permissions',
                                        'bank details',
                                        'manage
                                        salary',
                                        'delete employee',
                                        ])
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

                                                <div class="absolute z-50 bg-white shadow-md rounded-md mt-2 w-40 transition duration-150 ease-in-out max-w-fit"
                                                    x-show="open" @click.away="open = false" x-transition
                                                    style="z-index: 9999;top: 25px; right: -14px;">
                                                    @can('edit employee')
                                                        <a class="text-sm action-btn hover:bg-gray-100 ti-btn-wave text-gray-700 w-full flex !justify-start !border-b !border-gray-400"
                                                            href="{{ route('employees.edit', $employee->id) }}">
                                                            Edit
                                                        </a>
                                                    @endcan
                                                    @can('reset device')
                                                        <a class="text-sm action-btn hover:bg-gray-100 ti-btn-wave text-gray-700 w-full flex !justify-start !border-b !border-gray-400 reset_device"
                                                            href="{{ route('employees.devicereset', $employee->id) }}">
                                                            Reset Device
                                                        </a>
                                                    @endcan
                                                    @can('app permissions')
                                                        <a class="text-sm action-btn hover:bg-gray-100 ti-btn-wave text-gray-700 w-full flex !justify-start !border-b !border-gray-400"
                                                            href="{{ route('employees.permissions', $employee->id) }}">
                                                            App Permissions
                                                        </a>
                                                    @endcan
                                                    @can('bank details')
                                                        <a class="text-sm action-btn hover:bg-gray-100 ti-btn-wave text-gray-700 w-full flex !justify-start !border-b !border-gray-400"
                                                            href="{{ route('employees.bank', $employee->id) }}">
                                                            Bank Details
                                                        </a>
                                                    @endcan
                                                    @can('manage salary')
                                                        <a class="text-sm action-btn hover:bg-gray-100 ti-btn-wave text-gray-700 w-full flex !justify-start !border-b !border-gray-400"
                                                            href="{{ route('employees.salary', $employee->id) }}">
                                                            Manage Salary
                                                        </a>
                                                    @endcan
                                                    @can('delete employee')
                                                        <form class="delete-form w-full"
                                                            action="{{ route('employees.destroy', $employee->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button
                                                                class="delete_button text-sm action-btn hover:bg-gray-100 text-red-600 w-full flex !justify-start"
                                                                id="" data-type="confirm" type="submit"
                                                                title="Delete">
                                                                Delete</button>
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
                                <td colspan="7"
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

        {{ $employees->links('vendor.livewire.tailwind') }}
    </div>
</div>

@section('scripts')
    <script>
        $('.reset_device').click(function(e) {
            e.preventDefault();
            const url = $(this).attr('href');

            swal({
                    title: `Are you sure?`,
                    text: "Do you really want to reset device?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        window.location.href = url;
                    }
                });
        });
    </script>
@endsection
