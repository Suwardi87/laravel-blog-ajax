let Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
})

const toastSuccess = (message) => {
    Toast.fire({
        icon: 'success',
        title: message
    })
}

const toastError = (message) => {
    let resJson = JSON.parse(message);

    let errorText = '';

    for (let key in resJson.errors) {
        errorText = resJson.errors[key];
        break;
    }

    Toast.fire({
        icon: 'error',
        title: 'Ops! Data Tidak Valid <br>' + errorText
    })
}

const startLoading = (str = 'Please wait...') => {
    Swal.fire({
        title: 'Loading!',
        text: str,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    })
}

const stopLoading = () => {
    Swal.close()
}

const reloadTable = () => {
    $('#yajra').DataTable().draw(false);
}


const resetValidation = () => {
    $('.is-invalid').removeClass('is-invalid');
    $('.is-valid').removeClass('is-valid');
    $('span.invalid-feedback').remove();
}

// Fungsi untuk mereset form
const resetForm = (selector) => {
    const form = $(selector)[0];
    if (form && form.tagName === 'FORM') { // Pastikan elemen adalah form
        form.reset(); // Reset form jika form benar ditemukan
    } else {
        console.error("resetForm: Element is not a form.");
    }
};
