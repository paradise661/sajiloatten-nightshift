@extends('layouts.admin.master')
@section('content')
    @include('admin.includes.message')
    <div class="grid grid-cols-12 gap-6 mt-4">
        <div class="xl:col-span-12 col-span-12">
            <div class="box custom-box">
                <div class="box-header justify-between">
                    <div class="box-title">
                        Notices
                        @if ($notices->isNotEmpty())
                            <span
                                class="badge bg-light text-default rounded-full ms-1 text-[0.75rem] align-middle">{{ $notices->total() }}</span>
                        @endif
                    </div>
                    @can('create appnotice')
                        <div class="prism-toggle">
                            <a class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2"
                                href="{{ route('notices.create') }}">
                                New Notice <i class="ri-add-line"></i>
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
                                    <th class="text-start px-4 py-2" scope="col">Title</th>
                                    <th class="text-start px-4 py-2" scope="col">Notice</th>
                                    <th class="text-start px-4 py-2" scope="col">Date</th>
                                    <th class="text-start px-4 py-2 w-20" scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($notices->isNotEmpty())
                                    @foreach ($notices as $key => $notice)
                                        <tr class="{{ $loop->last ? '' : 'border-b border-defaultborder' }}">
                                            <th class="px-4 py-2" scope="row">{{ $key + $notices->firstItem() }}</th>
                                            <td class="px-4 py-2" scope="row" style="white-space: normal">
                                                {{ $notice->title ?? '-' }}</td>
                                            <td class="px-4 py-2" scope="row">
                                                <div class="relative" x-cloak x-data="{ show: false }">
                                                    <button
                                                        class="flex items-center text-sm text-primary font-medium focus:outline-none"
                                                        @click="show = !show">
                                                        View Notice
                                                        <svg class="w-4 h-4 ml-1 text-gray-500" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M9 2a7 7 0 100 14A7 7 0 009 2zM8 7h2v5H8V7zm0 6h2v2H8v-2z" />
                                                        </svg>
                                                    </button>

                                                    <div class="absolute z-50 mt-2 w-72 bg-white border border-gray-200 rounded-lg shadow-lg p-4 text-sm text-gray-800"
                                                        x-show="show" @click.away="show = false" x-transition>
                                                        <div class="p-4" style="white-space: normal;text-align: left">
                                                            <span
                                                                class="text-primary mb-4">{{ $notice->title ?? '-' }}</span>
                                                            <p></p>
                                                            <br>
                                                            <span
                                                                class="text-xs text-gray-600">{{ $notice->description ?? '-' }}</span>
                                                            <p></p>
                                                            <br>
                                                            @foreach ($notice->departments as $department)
                                                                <span class="badge bg-secondary text-white">
                                                                    {{ $department->name ?? '' }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="px-4 py-2">
                                                @if (session('calendar') == 'BS')
                                                    {{ App\Services\DateService::ADToBS($notice->date ?? '') }}
                                                @else
                                                    {{ $notice->date ?? '' }}
                                                @endif
                                            </td>

                                            @can('delete appnotice')
                                                <td class="text-end px-4 py-2">
                                                    <div class="hs-dropdown relative inline-flex">
                                                        <button
                                                            class="hs-dropdown-toggle flex justify-center items-center size-9 text-sm font-semibold rounded-md border shadow-lg border-gray-400 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none"
                                                            id="hs-dropdown-custom-icon-trigger" type="button"
                                                            aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                                                            <svg class="flex-none size-4 text-gray-600"
                                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <circle cx="12" cy="12" r="1" />
                                                                <circle cx="12" cy="5" r="1" />
                                                                <circle cx="12" cy="19" r="1" />
                                                            </svg>
                                                        </button>

                                                        <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 z-20 hidden  bg-white shadow-md rounded-md mt-2"
                                                            role="menu" aria-orientation="vertical"
                                                            aria-labelledby="hs-dropdown-custom-icon-trigger">
                                                            <div class="px-1 max-w-fit">

                                                                <form class="delete-form w-full"
                                                                    action="{{ route('notices.destroy', $notice->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button
                                                                        class="delete_button text-sm action-btn hover:bg-gray-100 text-red-600 w-full flex !justify-start"
                                                                        id="" data-type="confirm" type="submit"
                                                                        title="Delete">
                                                                        Delete</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5"
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

                {{ $notices->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
@endsection
