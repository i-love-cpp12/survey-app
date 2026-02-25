import { $ } from "../shared/selectors.js";
import { requestPOST } from "../shared/request.js";
const showPopup = (() => {
    let popupSetTimeOutId = null;
    return (error) => {
        const popupElem = $(".popup");
        popupElem.querySelector(".title").innerText = error.title;
        popupElem.querySelector("div").innerText = error.content;
        popupElem.classList.remove("hidden");
        if (popupSetTimeOutId !== null)
            clearTimeout(popupSetTimeOutId);
        popupSetTimeOutId = setTimeout(() => {
            popupElem.classList.add("hidden");
            popupSetTimeOutId = null;
        }, 5000);
    };
})();
async function onFormSubmit(e, formElem) {
    if (e)
        e.preventDefault();
    try {
        const formData = new FormData(formElem);
        const code = formData.get("survey-code")?.toString() ?? "";
        const data = await requestPOST("/survey/backend/validate_survey_code.php", { surveyCode: code });
        if (data === null)
            throw new Error("Server error");
        const isValid = data && data.error === "" && data.isCodeOk;
        isValid ? console.log(data) : console.error(data);
        console.log("is valid:", isValid);
        if (isValid) {
            document.location.href = `/survey/pages/vote.html?code=${encodeURIComponent(code)}`;
        }
        else
            showPopup({ title: "Survey not found", content: "No survey exists with that code. Check and try again." });
    }
    catch (err) {
        showPopup({ title: "Server Error", content: "Something went wrong. Please try again later." });
    }
}
function setFromEventListener() {
    const formElem = $(".js-enter-code-form");
    formElem.addEventListener("submit", (e) => { onFormSubmit(e, formElem); });
}
async function init() {
    const url = new URL(document.URL);
    const paramCode = url.searchParams.get("code");
    const paramErrorTitle = url.searchParams.get("error-title");
    const paramErrorConent = url.searchParams.get("error-content");
    if (paramErrorTitle !== null)
        url.searchParams.delete("error-title");
    if (paramErrorConent !== null)
        url.searchParams.delete("error-content");
    window.history.replaceState({}, "", url);
    const poupError = paramErrorTitle && paramErrorConent ? { title: paramErrorTitle, content: paramErrorConent } : null;
    if (paramCode) {
        const formElem = $(".js-enter-code-form");
        formElem["survey-code"].value = paramCode;
        onFormSubmit(null, formElem);
    }
    if (poupError)
        showPopup(poupError);
    setFromEventListener();
}
await init();
