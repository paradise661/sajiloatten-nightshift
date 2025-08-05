@extends('layouts.admin.master')
@section('content')
    @include('admin.includes.message')
    <div class="grid grid-cols-12 gap-6 mt-4">
        <div class="xl:col-span-12 col-span-12">
            <div class="box custom-box">
                <div class="box-header justify-between">
                    <div class="box-title">
                        Shifts
                        @if ($shifts->isNotEmpty())
                            <span
                                class="badge bg-light text-default rounded-full ms-1 text-[0.75rem] align-middle">{{ $shifts->total() }}</span>
                        @endif
                    </div>

                    @can('create shift')
                        <div class="prism-toggle">
                            <a class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2"
                                href="{{ route('shifts.create') }}">
                                New Shift <i class="ri-add-line"></i>
                            </a>
                        </div>
                    @endcan
                </div>

                <div class="box-body overflow-x-auto">
                    <table class="table whitespace-nowrap min-w-full">
                        <thead>
                            <tr class="border-b border-defaultborder">
                                <th class="text-start px-4 py-2 w-3" scope="col">#</th>
                                <th class="text-start px-4 py-2" scope="col">Shift Name</th>
                                <th class="text-start px-4 py-2" scope="col">Updated At</th>
                                <th class="text-start px-4 py-2 w-20" scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($shifts->isNotEmpty())
                                @foreach ($shifts as $key => $shift)
                                    <tr class="{{ $loop->last ? '' : 'border-b border-defaultborder' }}">
                                        <th class="px-4 py-2" scope="row">{{ $key + $shifts->firstItem() }}</th>
                                        <td class="px-4 py-2 font-semibold">{{ $shift->name ?? '-' }}</td>
                                        <td class="px-4 py-2">
                                            {{ $shift->updated_at ? $shift->updated_at->diffForHumans() : '-' }}
                                        </td>
                                        @canany(['edit shift', 'delete shift'])
                                            <td class="text-end px-4 py-2">
                                                <div class="hs-dropdown relative inline-flex">
                                                    <button
                                                        class="hs-dropdown-toggle flex justify-center items-center size-9 text-sm font-semibold rounded-md border shadow-lg border-gray-400 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none"
                                                        type="button" aria-haspopup="menu" aria-expanded="false"
                                                        aria-label="Dropdown">
                                                        <svg class="flex-none size-4 text-gray-600"
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="1" />
                                                            <circle cx="12" cy="5" r="1" />
                                                            <circle cx="12" cy="19" r="1" />
                                                        </svg>
                                                    </button>

                                                    <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 z-20 hidden bg-white shadow-md rounded-md mt-2"
                                                        role="menu" aria-orientation="vertical">
                                                        <div class="px-1 max-w-fit">
                                                            @can('edit shift')
                                                                <a class="text-sm action-btn hover:bg-gray-100 ti-btn-wave text-gray-700 w-full flex !justify-start !border-b !border-gray-400"
                                                                    href="{{ route('shifts.edit', $shift->id) }}">
                                                                    Edit
                                                                </a>
                                                            @endcan

                                                            @can('delete shift')
                                                                <form class="delete-form w-full"
                                                                    action="{{ route('shifts.destroy', $shift->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button
                                                                        class="delete_button text-sm action-btn hover:bg-gray-100 text-red-600 w-full flex !justify-start"
                                                                        data-type="confirm" type="submit" title="Delete">
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4"
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

                {{ $shifts->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
@endsection
