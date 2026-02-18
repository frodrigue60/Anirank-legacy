import { API, csrfToken, token } from "@/app.js";
const formRequest = document.querySelector("#form-request");
const textareaRequest = formRequest.querySelector("#textarea-request");
const selectRequestType = formRequest.querySelector("#select-request-type");
let headersData = {};
let bodyData = {};

formRequest.addEventListener("submit", function (event) {
    event.preventDefault();
    sendRequest();
});

async function sendRequest() {
    try {
        const selectedOption =
            selectRequestType.options[selectRequestType.selectedIndex];
        const requestTitle = selectedOption.text;

        headersData = {
            "Content-Type": "application/json",
            Accept: "application/json, text/html;q=0.9",
            "X-CSRF-TOKEN": csrfToken,
            Authorization: "Bearer " + token,
        };
        bodyData = JSON.stringify({
            title: requestTitle,
            content: textareaRequest.value,
        });

        const response = await API.post(
            API.REQUESTS.STORE,
            headersData,
            bodyData,
        );

        if (response.success == true) {
            window.dispatchEvent(
                new CustomEvent("toast", {
                    detail: {
                        type: "success",
                        message: "Request Sent",
                        description: response.message,
                    },
                }),
            );
            hideModal("requestModal");

            formRequest.reset();
        }
    } catch (error) {
        throw new Error(error);
    }
}
