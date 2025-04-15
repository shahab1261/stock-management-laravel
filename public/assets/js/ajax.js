$(document).ready(function() {
        /************* Adding Product *************/
    $('#addProductForm').submit(function(e) {
        e.preventDefault();
        let formdata = new FormData(this);
        console.log(formdata);

        $.ajax({
            url: $('#addProductForm').attr('action'),
            type: "POST",
            data: formdata,
            contentType: false,
            processData: false,
            dataType: 'json',
            beforeSend: function() {
                $('.spinner').removeClass('d-none');
                $('.action-btn').addClass('disabled');
            },
            success: function(response) {
                $('.spinner').addClass('d-none');
                $('.action-btn').removeClass('disabled');
                if (response.status == 'success') {
                    Swal.fire({
                        title: 'Product Added!',
                        text: 'Product has been added successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = response.redirect || '/admin/products';
                        }
                    });
                } else{
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(error, xhr, status) {
                $('.spinner').addClass('d-none');
                $('.action-btn').removeClass('disabled');
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });

    /************* Editing Product *************/
    $('#editProductForm').submit(function(e) {
        e.preventDefault();
        let formdata = new FormData(this);

        $.ajax({
            url: $("#editProductForm").attr('action'),
            type: "POST",
            data: formdata,
            contentType: false,
            processData: false,
            dataType: 'json',
            beforeSend: function() {
                $('.spinner').removeClass('d-none');
                $('.action-btn').addClass('disabled');
            },
            success: function(response) {
                $('.spinner').addClass('d-none');
                $('.action-btn').removeClass('disabled');

                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Product Updated!',
                        text: 'Product has been updated successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = response.redirect || '/admin/products';
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(xhr, error) {
                $('.spinner').addClass('d-none');
                $('.action-btn').removeClass('disabled');

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errorMessage = '<ul class="mb-0">';
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errorMessage += '<li>' + value + '</li>';
                    });
                    errorMessage += '</ul>';

                    Swal.fire({
                        title: 'Validation Error!',
                        html: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    });
                }
            }
        });
    });

    /************* View Product *************/
    $(document).on('click','.view-product', function() {
        let productId = $(this).data('id');

        $('#productModal .modal-body').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading product details...</p></div>');
        $('#productModal').modal('show');

        $.ajax({
            url: `/admin/products/${productId}`,
            type: "GET",
            success: function(product) {
                $('#productModal .modal-body').html(`
                    <table class="table table-bordered product-table">
                        <tbody>
                            <tr><th>Name</th><td id="modalProductName"></td></tr>
                            <tr><th>Slug</th><td id="modalProductSlug"></td></tr>
                            <tr><th>Price</th><td id="modalProductPrice"></td></tr>
                            <tr><th>Discount Toggle</th><td id="modalProductDiscountToggle"></td></tr>
                            <tr><th>Discounted Price</th><td id="modalProductDiscountedPrice"></td></tr>
                            <tr><th>Description</th><td id="modalProductDescription" style="white-space:pre-wrap;"></td></tr>
                        </tbody>
                    </table>
                `);

                $('#modalProductName').text(product.name);
                $('#modalProductSlug').text(product.slug);
                $('#modalProductPrice').html('US$ ' + product.price.replace(/##/g, '<br>'));
                $('#modalProductDiscountToggle').text(product.discount_toggle ? 'On' : 'Off');
                $('#modalProductDiscountedPrice').text(product.discount ? 'US$ ' + product.discount.replace(/##/g, '<br>') : 'N/A');
                $('#modalProductDescription').text(product.short_description);
            }
        });
    });

    /************* Deleting Product *************/
    $(document).on('click','.delete-product', function() {
        let productId = $(this).data('id');
        let csrfToken = $('meta[name="csrf-token"]').attr('content');

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4154f1",
            cancelButtonColor: "#151311",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/product/${productId}`,
                    type: "DELETE",
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    success: function(response) {
                        if(response.status == 'success') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'The product has been deleted.',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#4154f1'
                            }).then(() => {
                                location.reload();
                            });
                        } else{
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#4154f1'
                            });
                        }
                    },
                    error: function(error, xhr, status) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4154f1'
                        });
                    }
                });
            }
        });
    });

    /************* Adding/Updating FAQ *************/
    $('#faqForm').on('submit', function(e) {
        e.preventDefault();
        tinymce.triggerSave();

        let formdata = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formdata,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('.spinner').removeClass('d-none');
                $('.action-btn').addClass('disabled');
            },
            success: function(response) {
                $('.spinner').addClass('d-none');
                $('.action-btn').removeClass('disabled');
                if(response.status == 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    }).then(() => {
                        location.reload();
                    });
                } else{
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(error, xhr, status) {
                $('.spinner').addClass('d-none');
                $('.action-btn').removeClass('disabled');
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });

    /************* Deleting FAQ *************/
    $(document).on('submit', '#delete-faq', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4154f1",
            cancelButtonColor: "#151311",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if(response.status == 'success') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'The FAQ has been deleted.',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#4154f1'
                            }).then(() => {
                                location.reload();
                            });
                        } else{
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#4154f1'
                            });
                        }
                    },
                    error: function(error, xhr, status) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4154f1'
                        });
                    }
                });
            }
        });
    });


    /************* Terms & Conditions *************/
    $('#termsForm').on('submit', function(e) {
        e.preventDefault();
        tinymce.triggerSave();

        let formdata = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formdata,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('.spinner').removeClass('d-none');
                $('.action-btn').addClass('disabled');
            },
            success: function(response) {
                $('.spinner').addClass('d-none');
                $('.action-btn').removeClass('disabled');
                if(response.status == 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    }).then(() => {
                        location.reload();
                    });
                } else{
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(error, xhr, status) {
                $('.spinner').addClass('d-none');
                $('.action-btn').removeClass('disabled');
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });

    /************* Updating Refund *************/
    $('#refundForm').on('submit', function(e) {
        e.preventDefault();
        tinymce.triggerSave();

        let formdata = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formdata,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('.spinner').removeClass('d-none');
                $('.action-btn').addClass('disabled');
            },
            success: function(response) {
                $('.spinner').addClass('d-none');
                $('.action-btn').removeClass('disabled');
                if(response.status == 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    }).then(() => {
                        location.reload();
                    });
                } else{
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(error, xhr, status) {
                $('.spinner').addClass('d-none');
                $('.action-btn').removeClass('disabled');
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });

    /************* Updating Licence Agreement *************/
    $('#licenseForm').on('submit', function(e) {
        e.preventDefault();
        tinymce.triggerSave();

        let formdata = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formdata,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('.spinner').removeClass('d-none');
                $('.action-btn').addClass('disabled');
            },
            success: function(response) {
                $('.spinner').addClass('d-none');
                $('.action-btn').removeClass('disabled');
                if(response.status == 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    }).then(() => {
                        location.reload();
                    });
                } else{
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(error, xhr, status) {
                $('.spinner').addClass('d-none');
                $('.action-btn').removeClass('disabled');
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });

    /************** Updating Privacy Policy ***************/
    $('#privacyForm').on('submit', function(e){
        e.preventDefault();
        tinymce.triggerSave();

        let newFormData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: newFormData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('.spinner').removeClass('d-none');
                $('.action-btn').addClass('disabled');
            },
            success: function(response) {
                $('.spinner').addClass('d-none');
                $('.action-btn').removeClass('disabled');
                if(response.status == 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    }).then(() => {
                        location.reload();
                    });
                } else{
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(error, xhr, status) {
                $('.spinner').addClass('d-none');
                $('.action-btn').removeClass('disabled');
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });


});
