import { $ } from "../shared/selectors.js";
import { copyIcon, copiedIcon } from "../data/icons.js";
import { copyToClipboard } from "../shared/clipboard.js";
import { requestPOST } from "../shared/request.js";
async function getSurveyInfo(code) {
    const data = await requestPOST("/survey/backend/get_survey_info.php", { surveyCode: code });
    if (!data || data.error !== "")
        return null;
    return data.surveyInfo;
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
async function onCopy(surveyCode) {
    surveyCode = surveyCode;
    copyBtnElem.blur();
    const sucess = await copyToClipboard(surveyCode.toUpperCase(), copyBtnElem);
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
            <span>${surveyCode}</span>
        `;
    }, 1500);
}
async function vote(optionId, surveyCode) {
    const data = await requestPOST("/survey/backend/vote.php", {
        surveyCode: surveyCode,
        optionId: optionId
    });
    if (!data || data.error !== "")
        return false;
    return data.voted;
}
async function hasVoted(surveyCode) {
    const data = await requestPOST("/survey/backend/has_voted.php", { surveyCode: surveyCode });
    if (!data || data.error !== "")
        return null;
    return data.hasVoted;
}
let code = new URL(document.URL).searchParams.get("code");
console.log(code);
if (!code)
    document.location.href = "/survey";
code = code;
let surveyInfo = await getSurveyInfo(code);
if (!surveyInfo)
    document.location.href = "/survey/index.html?error-title=Something went wrong while loading survey data&error-content=Try again later or try other survey";
surveyInfo = surveyInfo;
const voted = await hasVoted(code);
if (voted === null)
    location.href = "/survey/index.html?error-title=Something went wrong while voting&error-content=Try again later or try other survey";
if (voted === true)
    location.href = `/survey/pages/results.html?code=${encodeURIComponent(code)}`;
renderSurvey(surveyInfo);
const copyBtnElem = $("button.copy");
let copyBtnSetTimeOutId = null;
copyBtnElem.addEventListener("click", async () => { await onCopy(code); });
const chooseOptionContainerElem = $(".js-choose-option-container");
const optionButtonElems = chooseOptionContainerElem.querySelectorAll("button");
optionButtonElems.forEach((btn) => {
    const optionId = parseInt(btn.getAttribute("data-option-id"));
    btn.addEventListener("click", async () => {
        if (!await vote(optionId, code)) {
            location.href = "/survey/index.html?error-title=Something went wrong while voting&error-content=Try again later or try other survey";
        }
        else {
            location.href = `/survey/pages/results.html?code=${encodeURIComponent(code)}`;
        }
    });
});
