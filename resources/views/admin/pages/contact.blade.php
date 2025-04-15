@extends('admin.layout.master')
@section('title', 'Root Sounds | Contact Messages')
@section('description', 'Manage customer contact messages and inquiries')
@section('content')
    <div class="container-fluid mt-4">
        <link rel="stylesheet" href="{{ asset('assets/css/products.css') }}">
        <div class="card p-4 shadow">
            <h3 class="mb-4 text-center faq-title" style="font-size: 2.4rem; color: #4154f1;"><i class="bi bi-envelope"></i> Contact Messages</h3>

            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header custom-card-header">
                            <h5 class="mb-0"><i class="bi bi-chat-dots me-2"></i>Customer Inquiries</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="contactTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="5%">#</th>
                                            <th class="text-center" width="20%">Name</th>
                                            <th class="text-center" width="20%">Email</th>
                                            <th class="text-center" width="30%">Message</th>
                                            <th class="text-center" width="15%">Sent At</th>
                                            <th class="text-center" width="15%">Newsletter</th>
                                            <th class="text-center" width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contacts as $contact)
                                            <tr>
                                                <td class="text-center">{{ $contact->id }}</td>
                                                <td class="text-center">{{ $contact->name }}</td>
                                                <td class="text-center">
                                                    <i class="bi bi-envelope-fill me-1"></i>{{ $contact->email }}
                                                </td>
                                                <td>
                                                    <div class="message-preview">
                                                        {{ Str::limit($contact->message, 60) }}
                                                        @if(strlen($contact->message) > 60)
                                                            <a href="javascript:void(0)" class="ms-1 text-primary view-message"
                                                               data-message="{{ $contact->message }}"
                                                               data-name="{{ $contact->name }}"
                                                               data-email="{{ $contact->email }}"
                                                               data-date="{{ $contact->created_at }}">
                                                                <i class="bi bi-eye"></i> View
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <span class="fw-bold">{{ $contact->created_at->format('M d, Y') }}</span>
                                                        <span class="text-muted small">{{ $contact->created_at->format('h:i A') }} UTC</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if ($contact->newsletter == 0)
                                                        <span class="badge rounded-pill bg-danger text-white">
                                                            <i class="bi bi-x-circle me-1"></i>Not Subscribed
                                                        </span>
                                                    @else
                                                        <span class="badge rounded-pill bg-success text-white">
                                                            <i class="bi bi-check-circle me-1"></i>Subscribed
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-primary btn-sm action-btn view-message"
                                                            data-message="{{ $contact->message }}"
                                                            data-name="{{ $contact->name }}"
                                                            data-email="{{ $contact->email }}"
                                                            data-date="{{ $contact->created_at }}">
                                                        <i class="bi bi-eye"></i> View Details
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header custom-card-header">
                    <h5 class="modal-title"><i class="bi bi-chat-quote me-2"></i>Message Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-circle fs-1 me-3" style="color: #4154f1;"></i>
                                <div>
                                    <h5 class="mb-0 contact-name"></h5>
                                    <p class="mb-0 contact-email text-muted"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="text-muted">
                                <i class="bi bi-calendar me-1"></i> <span class="contact-date"></span>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-0">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Message Content</h6>
                        </div>
                        <div class="card-body">
                            <div class="p-3 rounded contact-message"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-info reply-btn">
                        <i class="bi bi-reply"></i> Reply via Email
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/jszip/jszip.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#contactTable').DataTable({
                responsive: false,
                scrollX: true,
                order: [[0, 'desc']],
                dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f><"d-flex align-items-center"B>>rtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel me-1"></i> Export to Excel',
                        className: 'btn btn-success',
                        title: 'Contact Messages - ' + new Date().toLocaleDateString(),
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    }
                ],
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
            });

            // View message details
            $(document).on('click', '.view-message', function() {
                var message = $(this).data('message');
                var name = $(this).data('name');
                var email = $(this).data('email');
                var date = new Date($(this).data('date')).toLocaleString();

                $('.contact-name').text(name);
                $('.contact-email').text(email);
                $('.contact-date').text(date + ' UTC');
                $('.contact-message').text(message);
                $('.reply-btn').attr('href', 'mailto:' + email);

                $('#messageModal').modal('show');
            });
        });
    </script>
    <style>
        .message-preview {
            max-width: 100%;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .action-btn {
            min-width: 100px;
        }
        .dataTables_wrapper .btn-success {
            background-color: #198754;
            border-color: #198754;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .dataTables_wrapper .btn-success:hover {
            background-color: #157347;
            border-color: #146c43;
        }
        .custom-card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            padding: 0.75rem 1.25rem;
        }
        .contact-message {
            white-space: pre-wrap;
            background-color: #f8f9fa;
            border-left: 4px solid #4154f1;
        }
    </style>
@endpush
