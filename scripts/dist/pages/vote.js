import { $ } from "../shared/selectors.js";
import { copyIcon, copiedIcon } from "../data/icons.js";
import { copyToClipboard } from "../shared/clipboard.js";
async function getSurveyInfo(code) {
    const responce = await fetch("/survey/backend/get_survey_info.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ "code": code })
    });
    if (!responce.ok)
        return null;
    const data = await responce.json();
    console.log(data);
    if (data["error"] !== "" || !data["surveyInfo"])
        return null;
    return data["surveyInfo"];
}
function renderSurvey(data) {
    $("header .copy span").innerText = data.surveyCode;
    $("section > h2").innerText = data.question;
    const optionContainerElem = $("section .options");
    optionContainerElem.innerHTML = "";
    data.options.forEach((optionData) => {
        const optionElem = document.createElement("div");
        optionElem.classList.add("option");
        optionElem.innerHTML =
            `
            <button class="clear-btn" data-option-id="${optionData.id}">${optionData.value}</button>
        `;
        optionContainerElem.appendChild(optionElem);
    });
}
let code = new URL(document.URL).searchParams.get("code");
console.log(code);
if (!code)
    document.location.href = "/survey";
code = code;
let surveyInfo = await getSurveyInfo(code);
if (!surveyInfo)
    document.location.href = "/survey";
surveyInfo = surveyInfo;
renderSurvey(surveyInfo);
const copyBtnElem = $("button.copy");
let copyBtnSetTimeOutId = null;
copyBtnElem.addEventListener("click", async () => {
    copyBtnElem.blur();
    const sucess = await copyToClipboard(code.toUpperCase(), copyBtnElem);
    if (!sucess) {
        console.error("Copy unsucessful, try again or copy manualy");
        return;
    }
    if (copyBtnSetTimeOutId)
        clearTimeout(copyBtnSetTimeOutId);
    copyBtnElem.classList.add("copied");
    copyBtnElem.innerHTML =
        `
        ${copiedIcon}
        <span>Copied!</span>
    `;
    copyBtnSetTimeOutId = setTimeout(() => {
        copyBtnElem.classList.remove("copied");
        copyBtnElem.innerHTML =
            `
            ${copyIcon}
            <span>${code}</span>
        `;
    }, 1500);
});
