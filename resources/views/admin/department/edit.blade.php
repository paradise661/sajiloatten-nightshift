@extends('layouts.admin.master')
@section('content')
    <div class="xl:col-span-6 col-span-12 mt-4">
        <div class="box">
            <div class="box-header justify-between">
                <div class="box-title">
                    Edit Department
                </div>
                <div class="prism-toggle">
                    <a class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2"
                        href="{{ route('departments.index') }}">
                        <i class="ri-arrow-left-line"></i> Back
                    </a>
                </div>
            </div>
            <div class="box-body">
                <form class="grid grid-cols-12 gap-4 mt-0" action="{{ route('departments.update', $department->id) }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <div class="md:col-span-12 col-span-12 grid grid-cols-12 gap-4">

                        <div class="md:col-span-12 col-span-12">
                            <label class="form-label">Department Name<span class="text-red-500"> *</span></label>
                            <div class="relative">
                                <input
                                    class="form-control @error('name') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                    type="text" aria-label="Department Name" name="name"
                                    value="{{ old('name', $department->name) }}">
                                @error('name')
                                    <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                        <svg class="h-5 w-5 text-danger" width="16" height="16" fill="currentColor"
                                            viewBox="0 0 16 16" aria-hidden="true">
                                            <path
                                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                        </svg>
                                    </div>
                                @enderror
                            </div>
                            @error('name')
                                <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                    <i>*{{ $message }}</i>
                                </p>
                            @enderror
                        </div>

                        <div class="md:col-span-6 col-span-12">
                            <label class="form-label">Branch<span class="text-red-500"> *</span></label>
                            <div class="relative">
                                <select
                                    class="select2 ti-form-select rounded-sm !py-2 !px-3 @error('branch_id') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                    name="branch_id">
                                    <option value="">Select Branch</option>
                                    @foreach ($branches as $branch)
                                        <option {{ $branch->id == $department->branch_id ? 'selected' : '' }}
                                            value="{{ $branch->id ?? '' }}">
                                            {{ $branch->name ?? '' }}</option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                        <svg class="h-5 w-5 text-danger" width="16" height="16" fill="currentColor"
                                            viewBox="0 0 16 16" aria-hidden="true">
                                            <path
                                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                        </svg>
                                    </div>
                                @enderror
                            </div>
                            @error('branch_id')
                                <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                    <i>*{{ $message }}</i>
                                </p>
                            @enderror
                        </div>

                        <div class="md:col-span-6 col-span-12">
                            <label class="form-label">Shift<span class="text-red-500"> *</span></label>
                            <div class="relative">
                                <select
                                    class="select2 ti-form-select rounded-sm !py-2 !px-3 @error('shift') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                    name="shift">
                                    <option value="">Select Shift</option>
                                    @foreach ($shifts as $shift)
                                        <option value="{{ $shift->id }}"
                                            {{ $department->shifts->contains($shift->id) ? 'selected' : '' }}>
                                            {{ $shift->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('shift')
                                    <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                        <svg class="h-5 w-5 text-danger" width="16" height="16" fill="currentColor"
                                            viewBox="0 0 16 16" aria-hidden="true">
                                            <path
                                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                        </svg>
                                    </div>
                                @enderror
                            </div>
                            @error('shift')
                                <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                    <i>*{{ $message }}</i>
                                </p>
                            @enderror
                        </div>

                        {{-- <div class="md:col-span-12 col-span-12">
                            <label class="form-label" for="inputPassword4">Description</label>
                            <div class="relative">
                                <textarea class="sm:p-5 py-3 px-4 ti-form-input" rows="2" name="description">{{ old('description', $department->description) }}</textarea>
                            </div>
                            @error('description')
                                <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                    <i>*{{ $message }}</i>
                                </p>
                            @enderror
                        </div> --}}

                        <div class="md:col-span-6 col-span-12">
                            <label class="form-label">Status</label>
                            <div class="relative">
                                <select class="select2 ti-form-select rounded-sm !py-2 !px-3" name="status">
                                    <option {{ $department->status == 1 ? 'selected' : '' }} value="1">Active</option>
                                    <option {{ $department->status == 0 ? 'selected' : '' }} value="0">Inactive
                                    </option>
                                </select>
                                @error('status')
                                    <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                        <svg class="h-5 w-5 text-danger" width="16" height="16" fill="currentColor"
                                            viewBox="0 0 16 16" aria-hidden="true">
                                            <path
                                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                        </svg>
                                    </div>
                                @enderror
                            </div>
                            @error('status')
                                <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                    <i>*{{ $message }}</i>
                                </p>
                            @enderror
                        </div>

                        <div class="md:col-span-6 col-span-12">
                            <label class="form-label" for="inputEmail4">Order</label>
                            <div class="relative">
                                <input
                                    class="form-control @error('order') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                    id="inputEmail4" type="number" name="order"
                                    value="{{ old('order', $department->order) }}" min="1">
                                @error('order')
                                    <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                        <svg class="h-5 w-5 text-danger" width="16" height="16" fill="currentColor"
                                            viewBox="0 0 16 16" aria-hidden="true">
                                            <path
                                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                        </svg>
                                    </div>
                                @enderror
                            </div>
                            @error('order')
                                <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                    <i>*{{ $message }}</i>
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- <div class="md:col-span-2 col-span-12">
                        <label class="form-label">Weekend</label>
                        <div class="grid grid-cols-1 gap-2">
                            @php
                                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                $savedHolidays = json_decode($department->holidays, true) ?? [];
                            @endphp
                            @foreach ($days as $day)
                                <div class="flex items-center space-x-2">
                                    <input class="form-checkbox ti-form-checkbox text-primary" type="checkbox"
                                        name="holidays[]" value="{{ $day }}"
                                        {{ in_array($day, $savedHolidays) ? 'checked' : '' }}>
                                    <label>{{ $day }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div> --}}

                    <div class="col-span-12">
                        <button class="ti-btn ti-btn-primary-full" type="submit">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
