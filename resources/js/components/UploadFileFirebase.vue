<template>
    <div id="app">
        <input type="file" ref="myfile" />
    </div>
</template>

<script>
import { storage } from "../firebase";
import { ref, getDownloadURL, uploadBytes } from "firebase/storage";
import moment from "moment";

export default {
    name: "UPLOAD_FILE",
    props: ['next_periode'],
    data: () => {
        return {
            url: null,
            date: null,
            cabang_id: null,
            form: new form({
                cabang_id: null,
                file_url: null,
                date: null,
                file_name: null,
            }),
        };
    },
    methods: {
        upload: async function (fileName) {
            const path = "JadwalExcel/" + fileName;
            const storageRef = ref(storage, path);
            uploadBytes(storageRef, this.$refs.myfile.files[0]).then(() => {
                getDownloadURL(ref(storage, path)).then((download_url) => {
                    this.form.file_url = download_url;
                    this.postData();
                });
            });
        },
        postData() {
            this.form
                .post("/rcms-resource/upload-file")
                .then((data) => {
                    this.popGlobalSuccess("Berhasil upload file !");
                    this.$parent.reloadDataParentUploadFile();
                    this.$refs.myfile.value = null;
                })
                .catch((error) => { });
        },
        handleFileProcessData() {
            const file = this.$refs.myfile.files[0];
            // Generate new file name
            const newFileName = this.generateFileName(file);
            this.form.file_name = newFileName;
            this.upload(newFileName);
        },
        handleFile(cabang_id, date, file_exist) {
            this.form.cabang_id = cabang_id;
            this.form.date = date;
            const file = this.$refs.myfile.files[0];
            if (file) {
                // Validate the file
                const isValid = this.validateFile(file);
                if (!isValid) {
                    this.$parent.removeDisableParentButton();
                    Swal.fire({
                        icon: "error",
                        title: "Error !",
                        text: "Format file yang di upload salah !",
                    });
                    return;
                }
            } else {
                this.$parent.removeDisableParentButton();
                Swal.fire({
                    icon: "error",
                    title: "Error !",
                    text: "Silahkan isi data terlebih dahulu !",
                });
                return;
            }

            if (file_exist) {
                Swal.fire({
                    title: `Data sudah tersedia!`,
                    text: `Anda sudah upload data untuk bulan ${this.next_periode}. Apakah ingin melanjutkan proses upload?`,
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#acacac",
                    confirmButtonText: "Ya!",
                    cancelButtonText: "Batal",
                }).then((result) => {
                    if (result.value) {
                        this.handleFileProcessData();
                    } else {
                        this.$parent.removeDisableParentButton();
                    }
                });
            } else {
                this.handleFileProcessData();
            }
        },
        validateFile(file) {
            const validMimeTypes = [
                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                "application/vnd.ms-excel",
            ];
            const validExtensions = [".xlsx", ".xls"];

            // Check MIME type
            if (!validMimeTypes.includes(file.type)) {
                this.errorMessage =
                    "Invalid file type. Please select an Excel file.";
                return false;
            }

            // Check file extension
            const fileExtension = file.name.slice(
                ((file.name.lastIndexOf(".") - 1) >>> 0) + 2
            );
            if (!validExtensions.includes(`.${fileExtension}`)) {
                this.errorMessage =
                    "Invalid file extension. Please select an .xlsx or .xls file.";
                return false;
            }

            this.errorMessage = "";
            return true;
        },
        generateFileName(file) {
            const dateTimeString = moment().add(1, "month").format("YYYYMM");
            const fileExtension = file.name.slice(
                ((file.name.lastIndexOf(".") - 1) >>> 0) + 2
            );

            return `${dateTimeString}_${this.form.cabang_id}.${fileExtension}`;
        },
    },
};
</script>
