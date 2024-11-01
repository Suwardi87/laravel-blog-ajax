let submit_method;

$(document).ready(function () {
    articleTable();
});

// datatable serverside
function articleTable() {
    $('#yajra').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        // pageLength: 20, // set default records per page
        ajax: "/admin/articles/serverside",
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex'
            },
            {
                data: 'title',
                name: 'title'
            },
            {
                data: 'category_id',
                name: 'category_id'
            },
            {
                data: 'tag_id',
                name: 'tag_id'
            },
            {
                data: 'views',
                name: 'views'
            },
            {
                data: 'published',
                name: 'published'
            },
            {
                data: 'is_confirm',
                name: 'is_confirm'
            },
            {
                data: 'action',
                name: 'action',
                orderable: true,
                searchable: true
            },
        ]
    });
};


const deleteData = (e) => {
    let id = e.getAttribute('data-id');

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to delete this article?",
        icon: "question",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel",
        allowOutsideClick: false,
        showCancelButton: true,
        showCloseButton: true
    }).then((result) => {
        if (result.value) {
            startLoading();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "DELETE",
                url: "/admin/articles/" + id,
                dataType: "json",
                success: function (response) {
                    stopLoading();
                    reloadTable();
                    toastSuccess(response.message);
                },
                error: function (response) {
                    console.log(response);
                }
            });
        }
    })
}

// Event submit form menggunakan AJAX
$('#formUpdateArticle').on('submit', function (e) {
    e.preventDefault();
    startLoading();

    let id = $('#id').val();
    let publishedStatus = $('#published').val();

    $.ajax({
        type: "POST",
        url: "/admin/articles/" + id + "/update-status",
        data: {
            _method: "PUT", // Simpan metode HTTP PUT jika diperlukan
            _token: '{{ csrf_token() }}',
            published: publishedStatus
        },
        success: function (response) {
            stopLoading();
            $('#formStatusArticle').modal('hide'); // Tutup modal setelah submit

            Swal.fire({
                icon: 'success',
                title: "Success!",
                text: response.message,
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload(); // Reload halaman untuk melihat perubahan status
                }
            });
        },
        error: function (jqXHR) {
            console.log(jqXHR.responseText);
            toastError(jqXHR.responseText);
            stopLoading();
        }
    });
});
const confirmModal = (e) => {
    let uuid = e.getAttribute('data-uuid');
    // Set form action
    $('#confirmForm').attr('action', `articles/${uuid}/update-confirm`);
    $('#confirmModal').modal('show');
};

$(document).on('submit', '#confirmForm', function (e) {
    e.preventDefault();

    let url = $(this).attr('action');
    let method = 'PUT';  // Form is set to use the PUT method

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure CSRF protection
        },
        type: method,
        url: url,
        data: $(this).serialize(),
        success: function (response) {
            $('#confirmModal').modal('hide'); // Hide the modal
            $('#yajra').DataTable().ajax.reload(); // Reload DataTable

            Swal.fire({
                icon: response.status === 'success' ? 'success' : 'error',
                title: response.status === 'success' ? 'Success' : 'Error',
                text: response.message
            });
        },
        error: function (response) {
            $('#confirmModal').modal('hide'); // Hide the modal on error
            let errors = response.responseJSON.errors;
            let message = '';

            $.each(errors, function (key, value) {
                message += value + '<br>'; // Collect error messages
            });

            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: message // Display error messages in SweetAlert
            });
        }
    });
});

const publishedModal = (e) => {
    const uuid = e.getAttribute('data-uuid');
    $('#publishedForm').attr('action', `articles/${uuid}/update-published`);
    $('#publishedModal').modal('show');
};

$(document).on('submit', '#publishedForm', function (e) {
    e.preventDefault();

    const url = $(this).attr('action');

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: url,
        data: $(this).serialize(),
        success: function (response) {
            $('#publishedModal').modal('hide');
            $('#yajra').DataTable().ajax.reload();

            Swal.fire({
                icon: response.status === 'success' ? 'success' : 'error',
                title: response.status === 'success' ? 'Success' : 'Error',
                text: response.message
            });
        },
        error: function (response) {
            $('#publishedModal').modal('hide');
            let errors = response.responseJSON?.errors || {};
            let message = errors ? Object.values(errors).join('<br>') : 'An error occurred';

            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: message
            });
        }
    });
});


const deleteForceData = (e) => {
    let id = e.getAttribute('data-id');

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to delete permanently this article?",
        icon: "question",
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel",
        allowOutsideClick: false,
        showCancelButton: true,
        showCloseButton: true
    }).then((result) => {
        if (result.value) {
            startLoading();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "DELETE",
                url: "/admin/articles/force-delete/" + id,
                dataType: "json",
                success: function (response) {
                    stopLoading();

                    Swal.fire({
                        icon: 'success',
                        title: "Success!",
                        text: response.message,
                    }).then(result => {
                        if (result.isConfirmed) {
                            window.location.href = '/admin/articles';
                        }
                    })
                },
                error: function (response) {
                    console.log(response);
                }
            });
        }
    })
}
