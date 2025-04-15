@extends('admin.layout.master')

@section('title', 'Manage FAQs - Root Sounds')
@section('description', 'Create, edit, and delete FAQs dynamically')

@section('content')

<div class="container-fluid mt-4">
    <link rel="stylesheet" href="{{ asset('assets/css/products.css') }}">
    <div class="card p-4 shadow">
        <h3 class="mb-4 text-center faq-title" style="font-size: 2.4rem; color: #4154f1;"><i class="bi bi-question-circle"></i> Manage FAQs</h3>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header custom-card-header">
                        <h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>Add/Edit FAQ</h5>
                    </div>
                    <div class="card-body">
                        <form id="faqForm" action="{{ route('admin.faqs.update') }}" method="POST">
                            @csrf
                            <input type="hidden" id="faq_id" name="faq_id">
                            <div class="mb-3">
                                <label for="question" class="form-label">Question</label>
                                <input type="text" id="question" name="question" class="form-control @error('question') is-invalid @enderror" autocomplete="off">
                                @error('question')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="answer" class="form-label">Answer</label>
                                <textarea id="answer" name="answer" class="form-control @error('answer') is-invalid @enderror"></textarea>
                                @error('answer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary action-btn">
                                <i class="bi bi-save"></i> Save FAQ
                                <span class="spinner-border spinner-border-sm d-none spinner" role="status"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header custom-card-header">
                        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Manage FAQs</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered" id="faqTable">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Question</th>
                                    <th class="text-center">Answer</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($faqs as $faq)
                                    <tr>
                                        <td>{{ $faq->id }}</td>
                                        <td>{{ Str::limit($faq->question, 30) }}</td>
                                        <td>
                                            <a href="#" class="viewAnswer text-dark" data-answer="{{ $faq->answer }}">
                                                {!! Str::limit(strip_tags($faq->answer), 40) !!}
                                            </a>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm editFaq action-btn" data-id="{{ $faq->id }}" data-question="{{ $faq->question }}" data-answer="{{ $faq->answer }}">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>
                                            <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" class="d-inline delete-faq" id="delete-faq">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm action-btn">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
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

<!-- Modal -->
<div class="modal fade" id="answerModal" tabindex="-1" aria-labelledby="answerModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header custom-card-header">
                <h5 class="modal-title" id="answerModalLabel">Full Answer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalAnswerContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#faqTable').DataTable();

            tinymce.init({
                selector: '#answer',
                menubar: false,
                plugins: 'lists link',
                toolbar: 'undo redo | bold italic underline | bullist numlist | link',
                height: 200,
                promotion: false,
                branding: false,
            });

            $(document).on('click', '.editFaq', function () {
                $('#faq_id').val($(this).data('id'));
                $('#question').val($(this).data('question'));

                tinymce.get('answer').setContent($(this).data('answer'));
            });

            $(document).on('click', '.viewAnswer', function (e) {
                e.preventDefault();
                $('#modalAnswerContent').html($(this).data('answer'));
                $('#answerModal').modal('show');
            });

        });
    </script>
@endpush

