<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rocket Arena</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
</head>
<body>
<div class="row no-gutters" style="min-height: 100vh;">
    <div class="col-md-12 d-inline-flex justify-content-center">
        <div class="my-1" style="min-width: 30vw">
            <div class="" style="box-shadow: none">
                <div class="logo-center">
                    <a href="#"><b>Kebijakan Penghapusan Data Akun Member "Rocket Arena"</b></a>
                </div>
                <div class="card-body login-card-body">
                    <div class="mb-2">
                        <strong>Hapus Permanen</strong>
                    </div>
                    <div class="text-justify">
                        Kebijakan penghapusan data akun member "Rocket Arena" mencakup opsi untuk menghapus data pengguna secara permanen. Hapus permanen mengarah pada penghapusan seluruh data dan informasi yang terkait dengan akun tersebut, termasuk informasi pribadi, preferensi pengguna, dan aktivitas sebelumnya. Metode ini tidak dapat dipulihkan kembali setelah dilakukan. Hapus permanen direkomendasikan jika pengguna memutuskan untuk tidak lagi menggunakan layanan atau tidak ingin data mereka ada di platform "Rocket Arena". Dalam proses penghapusan permanen, semua informasi pribadi yang dikumpulkan dari akun tersebut akan dihapus sepenuhnya dari sistem.

                    </div>
                    <div class="mb-2 mt-2">
                        <strong>Hapus Sementara (1 Tahun)</strong>
                    </div>
                    <div class="text-justify">
                        Selain penghapusan permanen, kebijakan ini juga memberikan opsi penghapusan data sementara akun member "Rocket Arena" untuk periode waktu tertentu, yaitu 1 tahun. Penghapusan sementara memungkinkan pengguna untuk menonaktifkan akun mereka dan menghapus data yang terkait untuk sementara waktu. Metode ini berguna jika pengguna ingin mengambil jeda dari penggunaan platform "Rocket Arena" tanpa harus kehilangan semua data mereka secara permanen. Selama periode penghapusan sementara, data pengguna akan dinonaktifkan dari akses publik, dan pengguna dapat mengaktifkannya kembali dengan masuk kembali ke akun mereka. Setelah periode 1 tahun, data yang dihapus secara sementara akan dihapus secara permanen dari sistem.
                        Penting untuk dicatat bahwa kebijakan penghapusan data akun member "Rocket Arena" mungkin memerlukan verifikasi identitas dan persetujuan tertentu sebelum melakukan tindakan penghapusan, terutama dalam kasus penghapusan permanen. Hal ini dilakukan untuk melindungi integritas data pengguna dan mencegah akses yang tidak sah ke data pribadi.
                    </div>
                    <div class="mb-2 mt-2">
                        <strong>Note</strong>
                    </div>
                    <div class="font-italic text-justify">
                        Harap dicatat bahwa kebijakan ini dapat berubah seiring waktu sesuai dengan kebijakan perusahaan dan hukum yang berlaku. Sebelum mengambil tindakan penghapusan data akun, disarankan bagi pengguna untuk membaca dan memahami sepenuhnya kebijakan privasi dan syarat dan ketentuan yang berlaku untuk mendapatkan pemahaman yang lebih baik tentang konsekuensi dari tindakan penghapusan data tersebut.
                    </div>
                </div>
            </div>
            <div class="mt-3 w-25 ml-4">
                <form method="post">
                    @csrf
                    <div class="mb-3">
                        <label>Email</label>
                        <br>
                        <input type="email" name="member_email" class="form-control" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <button type="button" onclick="confirmDelete(this)" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<style>
    .logo-center{
        text-align: center;

    }
</style>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
<script>
    function confirmDelete(e) {
        var x = confirm('Apakah Anda yakin ingin menghapus akun Anda?');
        if (x) {
            alert('Akun Anda berhasil dihapus');
        } else {
            alert('Akun Anda tidak dihapus');
        }
        e.preventDefault();
    }
</script>
</body>
</html>
