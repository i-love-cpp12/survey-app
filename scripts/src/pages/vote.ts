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
    const responce = await fetch("/survey/backend/get_survey_info.php", {
        method: "POST",
        headers: {
            "Content-Type" : "application/json"
        },
        body: JSON.stringify({"code":code})
    })

    if(!responce.ok) return null;

    const data = await responce.json();

    console.log(data);

    if(data["error"] !== "" || !data["surveyInfo"]) return null;

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

let code: string | null = new URL(document.URL).searchParams.get("code");

console.log(code);

if(!code) document.location.href = "/survey";

code = code as string;

let surveyInfo: SurveyData | null = await getSurveyInfo(code);
if(!surveyInfo)
    document.location.href = "/survey";
surveyInfo = surveyInfo as SurveyData;
renderSurvey(surveyInfo);

const copyBtnElem: HTMLButtonElement = $("button.copy") as HTMLButtonElement;
let copyBtnSetTimeOutId: number | null = null;

copyBtnElem.addEventListener("click", async () => {
    copyBtnElem.blur();
    const sucess = await copyToClipboard(code.toUpperCase(), copyBtnElem);
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
            <span>${code}</span>
        `;
    }, 1500);
});



