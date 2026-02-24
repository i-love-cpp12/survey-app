import { $ } from "../shared/selectors.js";
import { requestPOST } from "../shared/request.js";
const url = new URL(document.URL);
const paramCode = url.searchParams.get("code");
const paramErrorTitle = url.searchParams.get("error-title");
const paramErrorConent = url.searchParams.get("error-content");
const poupError = paramErrorTitle && paramErrorConent ? { title: paramErrorTitle, content: paramErrorConent } : null;
const formElem = $(".js-enter-code-form");
let popupSetTimeOutId = null;
function popup(error) {
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
}
async function onFormSubmit(e) {
    if (e)
        e.preventDefault();
    try {
        const formData = new FormData(formElem);
        const code = formData.get("survey-code")?.toString() ?? "";
        const data = await requestPOST("/survey/backend/validate_survey_code.php", { surveyCode: code });
        const isValid = data && data.error === "" && data.isCodeOk;
        isValid ? console.log(data) : console.error(data);
        console.log("is valid:", isValid);
        if (isValid) {
            document.location.href = `/survey/pages/vote.html?code=${encodeURIComponent(code)}`;
        }
        else
            popup({ title: "Survey not found", content: "No survey exists with that code. Check and try again." });
    }
    catch (err) {
        popup({ title: "Server Error", content: "Something went wrong. Please try again later." });
    }
}
if (paramCode) {
    formElem["survey-code"].value = paramCode;
    onFormSubmit(null);
}
if (poupError)
    popup(poupError);
formElem.addEventListener("submit", onFormSubmit);
