@extends('layouts.admin.master')
@section('content')
    @include('admin.includes.message')
    <div class="xl:col-span-6 col-span-12 mt-4">
        <div class="box">
            <div class="box-header justify-between">
                <div class="box-title">
                    Manage Permissions <i>({{ ($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '') }})</i>
                </div>
                <div class="prism-toggle">
                    <a class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2"
                        href="{{ route('employees.index') }}">
                        <i class="ri-arrow-left-line"></i> Back
                    </a>
                </div>
            </div>
            <div class="box-body">
                <form class="grid grid-cols-12 gap-4 mt-0"
                    action="{{ route('employees.permissions.update', $employee->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="md:col-span-12 col-span-12">
                        <label class="form-label">Assign Role <span><i> (Enable administrative
                                    privileges)</i></span></label>
                        <div class="relative">
                            <select
                                class="ti-form-select select2 rounded-sm !py-2 !px-3 @error('roles') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                name="roles[]" multiple>
                                <option value=""> Please Select</option>
                                @foreach ($roles as $role)
                                    <option @if (in_array($role->name, old('roles', $assignedRoles))) selected @endif value="{{ $role->name }}">
                                        {{ $role->name ?? '' }}</option>
                                @endforeach
                            </select>
                            @error('roles')
                                <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                    <svg class="h-5 w-5 text-danger" width="16" height="16" fill="currentColor"
                                        viewBox="0 0 16 16" aria-hidden="true">
                                        <path
                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                    </svg>
                                </div>
                            @enderror
                        </div>
                        @error('roles')
                            <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                <i>*{{ $message }}</i>
                            </p>
                        @enderror
                    </div>

                    <div class="md:col-span-12 col-span-12 mt-3">
                        <div class="flex gap-2">
                            <div class="form-check form-check-md flex item-center">
                                <input class="form-check-input" type="checkbox" value="1" name="location_preference"
                                    {{ $employee->location_preference == 1 ? 'checked' : '' }}>
                            </div>
                            <label class="form-label">Location Preference </label>
                        </div>
                        <div class="relative">
                            <p class="text-xs text-gray-600">
                                <i>*Note: Check this to allow attendance only in your preferred area. Unchecked means any
                                    location is allowed.</i>
                            </p>
                        </div>
                    </div>

                    <div class="md:col-span-12 col-span-12 mt-3">
                        <div class="flex gap-2">
                            <div class="form-check form-check-md flex item-center">
                                <input class="form-check-input" type="checkbox" value="1" name="device_flexible"
                                    {{ $employee->device_flexible == 1 ? 'checked' : '' }}>
                            </div>
                            <label class="form-label">Allow Any Device </label>
                        </div>
                        <div class="relative">
                            <p class="text-xs text-gray-600">
                                <i>*Note: Check this to allow login from any device. Unchecked means login is restricted to
                                    registered device only.</i>
                            </p>
                        </div>
                    </div>

                    <div class="md:col-span-12 col-span-12 mt-3">
                        <div class="flex gap-2">
                            <div class="form-check form-check-md flex item-center">
                                <input class="form-check-input" type="checkbox" value="1"
                                    name="allow_attendance_request"
                                    {{ $employee->allow_attendance_request == 1 ? 'checked' : '' }}>
                            </div>
                            <label class="form-label">Allow Attendance Request </label>
                        </div>
                        <div class="relative">
                            <p class="text-xs text-gray-600">
                                <i>*Note: If checked, the employee will be able to request attendance from the mobile app.
                                    If unchecked, the attendance request option will be hidden in their app.</i>
                            </p>
                        </div>
                    </div>

                    <div class="md:col-span-12 col-span-12 mt-3">
                        <div class="flex gap-2">
                            <div class="form-check form-check-md flex item-center">
                                <input class="form-check-input" type="checkbox" value="1" name="allow_leave_request"
                                    {{ $employee->allow_leave_request == 1 ? 'checked' : '' }}>
                            </div>
                            <label class="form-label">Allow Leave Request </label>
                        </div>
                        <div class="relative">
                            <p class="text-xs text-gray-600">
                                <i>*Note: If checked, the employee will be able to apply for leave from the mobile app. If
                                    unchecked, the leave request menu will not appear in their app.</i>
                            </p>
                        </div>
                    </div>

                    <div class="md:col-span-12 col-span-12 mt-3">
                        <div class="flex gap-1">
                            <label class="form-label">Leave/Attendance Management
                            </label>
                            <span class="form-label">(For App
                                Only)</span>
                        </div>
                        <div class="relative">
                            <p class="text-xs text-gray-600">
                                <i>*Note: Users with the <strong>SENIOR-ADMIN</strong> role can manage attendance and leave
                                    requests across all branches. Users with the <strong>ADMIN</strong> role can manage
                                    requests for their respective branch only.</i>
                            </p>
                        </div>
                    </div>

                    <div class="col-span-12">
                        <button class="ti-btn ti-btn-primary-full btn-sm flex items-center gap-1 text-sm px-3 py-1"
                            type="submit">
                            <i class="ri-refresh-line text-base"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
