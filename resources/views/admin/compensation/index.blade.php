@extends('layouts.admin.master')
@section('content')
    @include('admin.includes.message')
    @livewire('admin.additional-salary-component')
    @include('admin.compensation.create')
    @include('admin.compensation.edit')
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('.openSalaryModal').click(function() {
                $('#salarySettingsModal').removeClass('hidden');
            });

            $('#closeSalaryModal').click(function() {
                $('#salarySettingsModal').addClass('hidden');
            });
        });

        $(document).on('click', '.open-edit-compensation-modal', function() {
            window.dispatchEvent(new Event('close-dropdown'));
            const component = JSON.parse($(this).attr('component'));

            $('#title').val(component?.title);
            $('#user_id').val(component?.user_id);
            $('#amount').val(component?.amount);
            $('#month').val(component?.month);
            $('#type').val(component?.type);
            $('#remarks').val(component?.remarks);
            $('#is_taxable').prop('checked', component?.is_taxable);

            $('#editCompensationForm').attr('action', `{{ url('compensation') }}/${component?.id}`);
            $('#editCompensationModal').removeClass('hidden');
        });

        $('#closeEditCompensationModal').click(function() {
            $('#editCompensationModal').addClass('hidden');
        });
    </script>
@endsection
