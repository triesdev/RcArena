import Swal from 'sweetalert2';

export const showAlert = (title: string, text: string, icon: 'success' | 'error' | 'warning' | 'info' | 'question') => {
    const originalBodyClass = document.body.className;

    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        confirmButtonText: 'OK',
    });
};
