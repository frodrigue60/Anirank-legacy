import API from "@api/index.js";
import "../css/app.css";

/* function hideModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add("hidden");
    }
} */

/* function showModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove("hidden");
    }
} */

/* window.hideModal = hideModal; */
/* window.showModal = showModal; */

const token = localStorage.getItem("api_token");
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

/* function onDomReady(callback) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback);
    } else {
        callback();
    }
} */

export { API, token, csrfToken };
