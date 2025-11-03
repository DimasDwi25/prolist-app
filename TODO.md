# TODO: Implementasi Event dan Notifikasi untuk Approval Log

## Status: Completed ✅

### Tugas yang Telah Diselesaikan:

-   [x] Membuat event `LogApprovalUpdated` untuk broadcast perubahan approval log (approved/rejected)
-   [x] Membuat event `LogCreatedRequiringApproval` untuk broadcast log yang butuh approval
-   [x] Membuat notifikasi `LogApprovalNotification` untuk mengirim notifikasi ketika approval disetujui/ditolak
-   [x] Membuat notifikasi `LogCreatedRequiringApprovalNotification` untuk mengirim notifikasi ketika log dibuat dan butuh approval
-   [x] Membuat listener `SendLogApprovalNotification` untuk menangani pengiriman notifikasi approval
-   [x] Membuat listener `SendLogCreatedRequiringApprovalNotification` untuk menangani pengiriman notifikasi log butuh approval
-   [x] Mendaftarkan semua event dan listener di `EventServiceProvider`
-   [x] Mengupdate `LogObserver` untuk memicu event ketika log dibuat (jika butuh approval) dan ketika status log berubah (approved/rejected)

### Cara Kerja:

1. **Ketika log dibuat dan butuh approval (`need_response = true`):**

    - `LogObserver::created` mendeteksi dan memicu event `LogCreatedRequiringApproval`
    - Listener mengirim notifikasi ke user yang dituju (`response_by`)

2. **Ketika approval log disetujui/ditolak:**
    - `ApprovallController::updateStatusLog` mengubah status log
    - `LogObserver::updated` mendeteksi perubahan status dan memicu event `LogApprovalUpdated`
    - Listener mengirim notifikasi ke user yang membuat log (`users_id`)

### Testing:

-   Pastikan notifikasi muncul di database notifications ketika log dibuat dan butuh approval
-   Pastikan notifikasi muncul ketika approval disetujui/ditolak
-   Pastikan broadcast event diterima oleh frontend
-   Verifikasi bahwa hanya user pembuat log yang menerima notifikasi
