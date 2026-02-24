import { $ } from "../shared/selectors.js";
import { copyIcon, copiedIcon } from "../data/icons.js";
import { copyToClipboard } from "../shared/clipboard.js";

interface SurveyOption
{
    id: number,
    value: string
}
interface SurveyData
{
    surveyId: number,
    surveyCode: string,
    question: string,
    options: Array<SurveyOption>
}

async function getSurveyInfo(code: string): Promise<SurveyData | null>
{
    const response = await fetch("/survey/backend/get_survey_info.php", {
        method: "POST",
        headers: {
            "Content-Type" : "application/json"
        },
        body: JSON.stringify({surveyCode: code})
    })

    if(!response.ok) return null;

    const data = await response.json();

    console.log(data);

    if(!data || data["error"] !== "" || !data["surveyInfo"]) return null;

    return data["surveyInfo"] as SurveyData;
}
function renderSurvey(data: SurveyData)
{
    ($("header .copy span") as HTMLElement).innerText = data.surveyCode;
    ($("section > h2") as HTMLElement).innerText = data.question;

    const optionContainerElem = $("section .options") as HTMLElement;
    optionContainerElem.innerHTML = "";

    data.options.forEach((optionData) => {
        const optionElem = document.createElement("div");
        optionElem.classList.add("option");
        optionElem.innerHTML = 
        `
            <button class="clear-btn" data-option-id="${optionData.id}">${optionData.value}</button>
        `;
        optionContainerElem.appendChild(optionElem);
    })
}
async function onCopy(surveyCode: string): Promise<void>
{
    surveyCode = surveyCode as string;

    copyBtnElem.blur();
    const sucess = await copyToClipboard(surveyCode.toUpperCase(), copyBtnElem);
    if(!sucess)
    {
        console.error("Copy unsucessful, try again or copy manualy");
        return;
    }
    if(copyBtnSetTimeOutId)
        clearTimeout(copyBtnSetTimeOutId)
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
async function vote(optionId: number, surveyCode: string): Promise<boolean>
{
    const response = await fetch("/survey/backend/vote.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            surveyCode: surveyCode,
            optionId: optionId
        })
    })

    const data = await response.json();

    console.log(data);

    if(!data || data["error"] !== "" || !data["voted"]) return false;

    return data["voted"] as boolean;
}
async function hasVoted(surveyCode: string): Promise<boolean | null>
{   
    const response = await fetch("/survey/backend/has_voted.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            surveyCode: surveyCode,
        })
    })

    const data = await response.json();

    console.log(data);

    if(!response.ok || !data || data["error"] !== "") return null;

    return data["hasVoted"] as boolean;
}


let code: string | null = new URL(document.URL).searchParams.get("code");

console.log(code);

if(!code) document.location.href = "/survey";

code = code as string;

const voted = await hasVoted(code);
if(voted === null)
    location.href = "/survey/index.html?error-title=Something went wrong while voting&error-content=Try again later or try other survey";
if(voted === true) location.href = `/survey/pages/results.html?code=${code}`;

let surveyInfo: SurveyData | null = await getSurveyInfo(code);
if(!surveyInfo)
    document.location.href = "/survey";
surveyInfo = surveyInfo as SurveyData;
renderSurvey(surveyInfo);

const copyBtnElem: HTMLButtonElement = $("button.copy") as HTMLButtonElement;
let copyBtnSetTimeOutId: number | null = null;

copyBtnElem.addEventListener("click", async () => {await onCopy(code)});

const chooseOptionConatinerElem = $(".js-choose-option-container") as HTMLFormElement;

const optionButtonElems = chooseOptionConatinerElem.querySelectorAll("button");

optionButtonElems.forEach((btn) => {
    const optionId = parseInt(btn.getAttribute("data-option-id") as string);

    btn.addEventListener("click", async () => {
        if(!await vote(optionId, code))
            location.href = "/survey/index.html?error-title=Something went wrong while voting&error-content=Try again later or try other survey";
        location.href = `/survey/pages/results.html?code=${code}`;
    })
})