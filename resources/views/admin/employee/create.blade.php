@extends('layouts.admin.master')
@section('content')
    <style>
        .form-control {
            line-height: 1px !important;
        }
    </style>
    <div class="xl:col-span-6 col-span-12 mt-4">
        <div class="box">
            <div class="box-header justify-between">
                <div class="box-title">
                    New Employee
                </div>
                <div class="prism-toggle">
                    <a class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2"
                        href="{{ route('employees.index') }}">
                        <i class="ri-arrow-left-line"></i> Back
                    </a>
                </div>
            </div>
            <div class="box-body">
                <form class="grid grid-cols-12 gap-4 mt-0" action="{{ route('employees.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">First Name <span class="text-red-500"> *</span></label>
                        <div class="relative">
                            <input
                                class="form-control @error('first_name') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                type="text" aria-label="First Name" name="first_name" value="{{ old('first_name') }}">
                            @error('first_name')
                                <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                    <svg class="h-5 w-5 text-danger" width="16" height="16" fill="currentColor"
                                        viewBox="0 0 16 16" aria-hidden="true">
                                        <path
                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                    </svg>
                                </div>
                            @enderror
                        </div>
                        @error('first_name')
                            <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                <i>*{{ $message }}</i>
                            </p>
                        @enderror
                    </div>

                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Last Name <span class="text-red-500"> *</span></label>
                        <div class="relative">
                            <input
                                class="form-control @error('last_name') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                type="text" aria-label="First Name" name="last_name" value="{{ old('last_name') }}">
                            @error('last_name')
                                <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                    <svg class="h-5 w-5 text-danger" width="16" height="16" fill="currentColor"
                                        viewBox="0 0 16 16" aria-hidden="true">
                                        <path
                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                    </svg>
                                </div>
                            @enderror
                        </div>
                        @error('last_name')
                            <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                <i>*{{ $message }}</i>
                            </p>
                        @enderror
                    </div>

                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Email <span class="text-red-500"> *</span></label>
                        <div class="relative">
                            <input
                                class="form-control @error('email') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                type="text" aria-label="Email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                    <svg class="h-5 w-5 text-danger" width="16" height="16" fill="currentColor"
                                        viewBox="0 0 16 16" aria-hidden="true">
                                        <path
                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                    </svg>
                                </div>
                            @enderror
                        </div>
                        @error('email')
                            <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                <i>*{{ $message }}</i>
                            </p>
                        @enderror
                    </div>

                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Phone <span class="text-red-500"> *</span></label>
                        <div class="relative">
                            <input
                                class="form-control @error('phone') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                type="text" aria-label="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                    <svg class="h-5 w-5 text-danger" width="16" height="16" fill="currentColor"
                                        viewBox="0 0 16 16" aria-hidden="true">
                                        <path
                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                    </svg>
                                </div>
                            @enderror
                        </div>
                        @error('phone')
                            <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                <i>*{{ $message }}</i>
                            </p>
                        @enderror
                    </div>

                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Gender</label>
                        <div class="relative">
                            <select
                                class="select2 ti-form-select rounded-sm !py-2 !px-3 @error('gender') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                name="gender">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                            @error('gender')
                                <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                    <svg class="h-5 w-5 text-danger" width="16" height="16" fill="currentColor"
                                        viewBox="0 0 16 16" aria-hidden="true">
                                        <path
                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                    </svg>
                                </div>
                            @enderror
                        </div>
                        @error('gender')
                            <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                <i>*{{ $message }}</i>
                            </p>
                        @enderror
                    </div>

                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Designation<span class="text-red-500"> *</span></label>
                        <div class="relative">
                            <select
                                class="select2 ti-form-select rounded-sm !py-2 !px-3 @error('designation') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                id="designation-select" name="designation">
                                @foreach ($designations as $designation)
                                    <option value="{{ $designation->name ?? '' }}">{{ $designation->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('designation')
                                <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                    <svg class="h-5 w-5 text-danger" width="16" height="16" fill="currentColor"
                                        viewBox="0 0 16 16" aria-hidden="true">
                                        <path
                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                    </svg>
                                </div>
                            @enderror
                        </div>
                        @error('designation')
                            <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                <i>*{{ $message }}</i>
                            </p>
                        @enderror
                    </div>

                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Date of Birth <span class="text-red-500"> *</span></label>
                        <div class="relative">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-text text-[#8c9097] dark:text-white/50"> <i
                                            class="ri-calendar-line"></i> </div>
                                    @if (session('calendar') == 'BS')
                                        <input
                                            class="nepali-datepicker form-control flatpickr-input active @error('date_of_birth') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                            id="" type="text" name="date_of_birth" placeholder="Choose date"
                                            readonly="readonly" value="{{ old('date_of_birth') }}">
                                    @else
                                        <input
                                            class="form-control flatpickr-input active @error('date_of_birth') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                            id="date" type="text" name="date_of_birth" placeholder="Choose date"
                                            readonly="readonly" value="{{ old('date_of_birth') }}">
                                    @endif

                                    @error('date_of_birth')
                                        <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                            <svg class="h-5 w-5 text-danger" width="16" height="16"
                                                fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                                <path
                                                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                            </svg>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @error('date_of_birth')
                            <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                <i>*{{ $message }}</i>
                            </p>
                        @enderror
                    </div>

                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Join Date <span class="text-red-500"> *</span></label>
                        <div class="relative">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-text text-[#8c9097] dark:text-white/50"> <i
                                            class="ri-calendar-line"></i> </div>

                                    @if (session('calendar') == 'BS')
                                        <input
                                            class="nepali-datepicker form-control flatpickr-input active @error('join_date') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                            id="" type="text" name="join_date" placeholder="Choose date"
                                            readonly="readonly" value="{{ old('join_date') }}">
                                    @else
                                        <input
                                            class="form-control flatpickr-input active @error('join_date') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                            id="date" type="text" name="join_date" placeholder="Choose date"
                                            readonly="readonly" value="{{ old('join_date') }}">
                                    @endif
                                    @error('join_date')
                                        <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                            <svg class="h-5 w-5 text-danger" width="16" height="16"
                                                fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                                <path
                                                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                            </svg>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @error('join_date')
                            <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                <i>*{{ $message }}</i>
                            </p>
                        @enderror
                    </div>

                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Branch <span class="text-red-500"> *</span></label>
                        <div class="relative">
                            <select
                                class="select2 ti-form-select rounded-sm !py-2 !px-3 @error('branch_id') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                id="branch" name="branch_id">
                                <option value="">Please Select</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name ?? '' }}
                                    </option>
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
                        <label class="form-label">Department <span class="text-red-500"> *</span></label>
                        <div class="relative">
                            <select
                                class="select2 ti-form-select rounded-sm !py-2 !px-3 @error('department_id') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                id="department" name="department_id">
                                <option value="">Select Department</option>
                            </select>
                            @error('department_id')
                                <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                    <svg class="h-5 w-5 text-danger" width="16" height="16" fill="currentColor"
                                        viewBox="0 0 16 16" aria-hidden="true">
                                        <path
                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                    </svg>
                                </div>
                            @enderror
                        </div>
                        @error('department_id')
                            <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                <i>*{{ $message }}</i>
                            </p>
                        @enderror
                    </div>

                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Shift <span class="text-red-500"> *</span></label>
                        <div class="relative">
                            <select
                                class="select2 ti-form-select rounded-sm !py-2 !px-3 @error('shift_id') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                id="shift" name="shift_id">
                                <option value="">Select Shift</option>
                            </select>
                            @error('shift_id')
                                <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                    <svg class="h-5 w-5 text-danger" width="16" height="16" fill="currentColor"
                                        viewBox="0 0 16 16" aria-hidden="true">
                                        <path
                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                    </svg>
                                </div>
                            @enderror
                        </div>
                        @error('shift_id')
                            <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                <i>*{{ $message }}</i>
                            </p>
                        @enderror
                    </div>

                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Order</label>
                        <div class="relative">
                            <input
                                class="form-control  @error('order') ti-form-input !border-danger focus:border-danger focus:ring-danger @enderror"
                                type="number" aria-label="order" name="order" value="{{ old('order') }}"
                                min="1">
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

                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Image</label>
                        <div>
                            <label class="sr-only" for="file-input">Choose file</label>
                            <input
                                class="block w-full form-control border border-gray-200 focus:shadow-sm dark:focus:shadow-white/10 rounded-sm text-sm focus:z-10 focus:outline-0 focus:border-gray-200 dark:focus:border-white/10 dark:border-white/10 dark:text-white/50 file:border-0 file:bg-light file:me-4 file:py-3 file:px-4 dark:file:bg-black/20 dark:file:text-white/50 image"
                                id="file-input" type="file" name="image">
                            <img class="view-image mt-2" src="" style="max-height:80px">
                        </div>
                        @error('image')
                            <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                <i>*{{ $message }}</i>
                            </p>
                        @enderror
                    </div>

                    <div class="col-span-12">
                        <button class="ti-btn ti-btn-primary-full submitbtn" type="submit">
                            Submit
                            <span class="ti-spinner text-white !w-[1rem] !h-[1rem]" style="display: none" role="status"
                                aria-label="loading"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('form').on('submit', function() {
                let button = $(this).find('.submitbtn');
                let spinner = button.find('.ti-spinner');

                button.prop('disabled', true);
                spinner.show();
            });

            $('#designation-select').select2({
                tags: true, // allow new entries
                placeholder: 'Select or type a designation',
                tokenSeparators: [',', ' ']
            });
        });


        $(document).ready(function() {
            $('#branch').on('change', function() {
                let branch_id = $(this).val();
                $('#department').html('<option value="">Loading...</option>');

                $.get("{{ url('get-departments') }}/" + branch_id, function(data) {
                    $('#department').html('<option value="">Select Department</option>');
                    $.each(data, function(key, value) {
                        $('#department').append('<option value="' + value.id + '">' + value
                            .name + '</option>');
                    });
                });
            });

            $('#department').on('change', function() {
                let department_id = $(this).val();
                $('#shift').html('<option value="">Loading...</option>');

                $.get("{{ url('get-shifts') }}/" + department_id, function(data) {
                    $('#shift').html('<option value="">Select Shift</option>');
                    $.each(data, function(key, value) {
                        $('#shift').append('<option value="' + value.id + '">' + value
                            .name + '</option>');
                    });
                });
            });
        });
    </script>
@endsection
