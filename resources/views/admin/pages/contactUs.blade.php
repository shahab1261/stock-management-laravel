@extends('admin.layout.master')
@section('title', 'Stock Management | Contact us')
@section('description', 'Contact us')
@section('content')
    <div class="container-fluid mt-24">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4 p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap  w-100 ">
                        <h2 class="seller-dashboard-heading">Contact Us</h2>
                    </div>
                    <div class="row mt-32 ">
                        <div class="col-12">
                            <div class=" mb-4">

                                <div class="card-body px-0 pt-0 pb-2">
                                    <div class="table-responsive p-0" style="min-height: 400px">
                                        <table class="table align-items-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-capitalized fs-12-secondary">
                                                        #
                                                    </th>
                                                    <th class="text-capitalized fs-12-secondary">
                                                        Name
                                                    </th>
                                                    <th class="text-capitalized fs-12-secondary">
                                                        Email
                                                    </th>
                                                    <th class="text-capitalized fs-12-secondary">
                                                        Description
                                                    </th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($contacts as $contact)
                                                    <tr>
                                                        <td>
                                                            <p
                                                                class="text-xs text-secondary mb-0 dashboard-text-align-left text-center  ">
                                                                {{ $contact->id }}</p>
                                                        </td>
                                                        <td>
                                                            <p
                                                                class="text-xs text-secondary mb-0 dashboard-text-align-left">
                                                                {{ $contact->name }}</p>
                                                        </td>
                                                        <td class="align-middle text-center text-sm">
                                                            <p class="text-xs text-secondary mb-0">
                                                                {{ $contact->email }}</p>
                                                        </td>
                                                        <td class="align-middle text-center text-sm">
                                                            <p class="text-xs text-secondary mb-0">
                                                                {{ $contact->description }}</p>
                                                        </td>
                                                        

                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6">No emails were found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        <div class="mt-3 d-flex justify-content-end">
                                            {{ $contacts->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.pages.modals.deleteCategory')
    @include('admin.pages.modals.editCategory')
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $(".contact-admin").addClass("active");
        });
    </script>
    <script src="{{ asset('js/category.js') }}"></script>
@endpush
