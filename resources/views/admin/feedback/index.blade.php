@extends('layouts.admin.master')
@section('content')
    @include('admin.includes.message')
    <div class="grid grid-cols-12 gap-6 mt-4">
        <div class="xl:col-span-12 col-span-12">
            <div class="box custom-box">
                <div class="box-header justify-between">
                    <div class="box-title">
                        Feedbacks
                        @if ($feedbacks->isNotEmpty())
                            <span
                                class="badge bg-light text-default rounded-full ms-1 text-[0.75rem] align-middle">{{ $feedbacks->total() }}</span>
                        @endif
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table whitespace-nowrap min-w-full">
                            <thead>
                                <tr class="border-b border-defaultborder">
                                    <th class="text-start px-4 py-2 w-3" scope="col">#</th>
                                    <th class="text-start px-4 py-2" scope="col">Employee</th>
                                    <th class="text-start px-4 py-2" scope="col">Message</th>
                                    <th class="text-start px-4 py-2" scope="col">Date</th>
                                    <th class="text-start px-4 py-2 w-20" scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($feedbacks->isNotEmpty())
                                    @foreach ($feedbacks as $key => $feedback)
                                        <tr class="{{ $loop->last ? '' : 'border-b border-defaultborder' }}">
                                            <th class="px-4 py-2" scope="row">{{ $key + $feedbacks->firstItem() }}</th>
                                            <td>
                                                <div class="flex items-center gap-2"> <span
                                                        class="avatar avatar-xs me-2 online avatar-rounded">
                                                        <a class="fancybox" data-fancybox="demo"
                                                            href="{{ $feedback->employee->image ?? '' }}">
                                                            <img src="{{ $feedback->employee->image ?? '' }}"
                                                                alt="profile">
                                                        </a>
                                                    </span>
                                                    <div>
                                                        {{ $feedback->employee ? $feedback->employee->full_name : '' }}<br>
                                                        <i
                                                            class="text-xs text-gray-600">{{ $feedback->employee ? $feedback->employee->branch->name : 'Deleted User' }}</i>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-2" scope="row" style="white-space: normal">
                                                {{ $feedback->message ?? '-' }}</td>
                                            <td>
                                                @if (session('calendar') == 'BS')
                                                    {{ App\Services\DateService::ADToBS($feedback->created_at->format('Y-m-d')) }}
                                                    {{ $feedback->created_at->format('g:i A') }}
                                                @else
                                                    {{ $feedback->created_at->format('Y-m-d g:i A') }}
                                                @endif
                                            </td>
                                            @can('delete feedback')
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
                                                                    action="{{ route('feedbacks.destroy', $feedback->id) }}"
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

                {{ $feedbacks->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
@endsection
