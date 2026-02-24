import { $ } from "../shared/selectors.js";
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
    if (popupSetTimeOutId)
        clearTimeout(popupSetTimeOutId);
    popupSetTimeOutId = setTimeout(() => {
        popupElem.classList.add("hidden");
        popupSetTimeOutId = null;
    }, 5000);
}
async function onFormSubmit(e) {
    if (e)
        e.preventDefault();
    const formData = new FormData(formElem);
    const code = formData.get("survey-code")?.toString();
    const response = await fetch("/survey/backend/validate_survey_code.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ surveyCode: code })
    });
    const data = await response.json();
    const isValid = response.ok && data && data["error"] === "" && data["isCodeOk"] === true;
    isValid ? console.log(data) : console.error(data);
    console.log("is valid:", isValid);
    if (isValid) {
        document.location.href = `/survey/pages/vote.html?code=${code}`;
    }
    else
        popup({ title: "Survey not found", content: "No survey exists with that code. Check and try again." });
}
if (paramCode) {
    formElem["survey-code"].value = paramCode;
    onFormSubmit(null);
}
if (poupError)
    popup(poupError);
formElem.addEventListener("submit", onFormSubmit);
