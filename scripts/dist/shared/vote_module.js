import { $ } from "../shared/selectors.js";
import { copyIcon, copiedIcon } from "../data/icons.js";
import { copyToClipboard } from "../shared/clipboard.js";
import { requestPOST } from "../shared/request.js";
export function surveyOptionEqualOpperator(o1, o2) {
    return o1.id === o2.id && o1.value === o2.value && o1.votesCount === o2.votesCount;
}
export function surveyDataEqualOpperator(s1, s2) {
    if (s1.surveyId !== s2.surveyId || s1.surveyCode !== s2.surveyCode || s1.question !== s2.question)
        return false;
    for (let i = 0; i < s1.options.length || i < s2.options.length; i++) {
        const s1Option = s1.options[i];
        const s2Option = s2.options[i];
        if (!s1Option || !s2Option)
            console.log("s1 or s2 is null");
        if (!s1Option || !s2Option || !surveyOptionEqualOpperator(s1Option, s2Option))
            return false;
    }
    return true;
}
export async function getSurveyInfo(code) {
    const data = await requestPOST("/survey/backend/get_survey_info.php", { surveyCode: code });
    if (!data || data.error !== "")
        return null;
    return data.surveyInfo;
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
function render(data) {
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
const onCopy = (() => {
    let copyBtnSetTimeOutId = null;
    return async (surveyCode, copyBtnElem) => {
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
    };
})();
export function setCopyBtnEventListener(code) {
    const copyBtnElem = $("button.copy");
    copyBtnElem.addEventListener("click", async () => { await onCopy(code, copyBtnElem); });
}
function setOptionsEventListener(code) {
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
}
export async function init() {
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
    render(surveyInfo);
    setCopyBtnEventListener(code);
    setOptionsEventListener(code);
}
