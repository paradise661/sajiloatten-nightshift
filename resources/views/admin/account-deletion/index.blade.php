@extends('layouts.admin.master')
@section('content')
    @include('admin.includes.message')
    <div class="grid grid-cols-12 gap-6 mt-4">
        <div class="xl:col-span-12 col-span-12">
            <div class="box custom-box">
                <div class="box-header justify-between">
                    <div class="box-title">
                        Account Deletions
                        @if ($accounts->isNotEmpty())
                            <span
                                class="badge bg-light text-default rounded-full ms-1 text-[0.75rem] align-middle">{{ $accounts->total() }}</span>
                        @endif
                    </div>

                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table whitespace-nowrap min-w-full">
                            <thead>
                                <tr class="border-b border-defaultborder">
                                    <th class="text-start px-4 py-2" scope="col">#</th>
                                    <th class="text-start px-4 py-2" scope="col">Message</th>
                                    <th class="text-start px-4 py-2" scope="col">Deleted At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($accounts->isNotEmpty())
                                    @foreach ($accounts as $key => $account)
                                        <tr class="{{ $loop->last ? '' : 'border-b border-defaultborder' }}">
                                            <th class="px-4 py-2" scope="row">{{ $key + $accounts->firstItem() }}
                                            </th>
                                            <td class="px-4 py-2">
                                                {!! $account->employee->fullname ?? '<i>Deleted User</i>' !!} has
                                                deleted
                                                their
                                                account.</td>
                                            <td class="px-4 py-2">
                                                {{ App\Services\DateService::ADToBS($account->created_at->format('Y-m-d') ?? '') }}
                                                {{ date('H:i:s', strtotime($account->created_at)) }}
                                            </td>
                                            {{-- <td class="text-end px-4 py-2">
                                                <div class="btn-list flex gap-3">
                                                    <form class="delete-form"
                                                        action="{{ route('accountdeletion.update', $account->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button
                                                            class="ti-btn ti-btn-primary-full !py-1 !px-3 ti-btn-wave delete_button"
                                                            id="" data-type="confirm" type="submit"
                                                            title="Manage"> <i class="ri-loop-right-line"></i>
                                                            Manage Request</button>
                                                    </form>
                                                </div>
                                            </td> --}}
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
                </div>

                {{ $accounts->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
@endsection
