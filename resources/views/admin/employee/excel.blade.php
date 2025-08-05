@extends('layouts.admin.master')
@section('content')
    <div class="xl:col-span-6 col-span-12 mt-4">
        <div class="box">
            <div class="box-header justify-between">
                <div class="box-title gap-4">
                    Import Employees From Excel 
                    <span class="ml-4 bg-primary text-white px-4 py-1 text-xs rounded-sm">
                        <a class="" href="{{ asset('assets/sample.csv') }}">Download Sample</a>
                    </span>
                </div>
                <div class="prism-toggle">
                    <a class="ti-btn ti-btn-primary-full ti-btn-wave !text-[0.75rem] flex items-center gap-2"
                        href="{{ route('employees.index') }}">
                        <i class="ri-arrow-left-line"></i> Back
                    </a>
                </div>
            </div>
            <div class="box-body">
                @include('admin.includes.message')
                <form class="grid grid-cols-12 gap-4 mt-0" action="{{ route('employees.import.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="md:col-span-6 col-span-12">
                        <label class="form-label">Excel File (CSV only)</label>
                        <div>
                            <label class="sr-only" for="file-input">Choose file</label>
                            <input required
                                class="block w-full  border border-gray-200 focus:shadow-sm dark:focus:shadow-white/10 rounded-sm text-sm focus:z-10 focus:outline-0 focus:border-gray-200 dark:focus:border-white/10 dark:border-white/10 dark:text-white/50 file:border-0 file:bg-light file:me-4 file:py-3 file:px-4 dark:file:bg-black/20 dark:file:text-white/50 image"
                                id="file-input" type="file" name="file">
                        </div>
                        @error('file')
                            <p class="text-sm text-red-600 mt-2" id="hs-validation-name-error-helper">
                                <i>*{{ $message }}</i>
                            </p>
                        @enderror
                    </div>

                    <div class="col-span-12">
                        <button class="ti-btn ti-btn-primary-full submitbtn" type="submit">
                            Import
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
        });
    </script>
@endsection
