import axios from "axios";
import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});

// Ambil user ID dari meta tag
const userMeta = document.head.querySelector('meta[name="user-id"]');

// Public channel listener
window.Echo.channel("phc.notifications").listen(".phc.created", (e) => {
    // Filter supaya hanya user tertentu yang dapat notifikasi
    if (userMeta) {
        const userId = parseInt(userMeta.content, 10);
        if (e.user_ids.includes(userId)) {
            if (window.Livewire) {
                Livewire.emit("refreshNotifications");
            }

            // Bisa juga munculin notifikasi manual:
            console.log("New PHC Notification:", e.message);
        }
    }
});
