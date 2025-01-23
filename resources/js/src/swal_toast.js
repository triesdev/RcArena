export default function (title = 'Berhasil', icon= 'success', timer = 2500){
    Swal.fire({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: timer,
        icon: icon,
        title: title
    })
}

