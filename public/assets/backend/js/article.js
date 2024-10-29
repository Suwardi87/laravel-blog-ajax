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
