import { $ } from "../shared/selectors.js";
import { copyIcon, copiedIcon } from "../data/icons.js";
import { copyToClipboard } from "../shared/clipboard.js";
import { requestPOST, requestGET, ResponseWithErrorField } from "../shared/request.js";
import { goTo, homeDir } from "./link.js";

export interface SurveyOption
{
    id: number,
    value: string,
    votesCount: number
}
export interface SurveyData
{
    id: number,
    code: string,
    question: string,
    options: Array<SurveyOption>
}
export interface GetSurveyInfoResponse extends ResponseWithErrorField
{
    data: SurveyData,
}
export interface CreateSurveyResponce extends ResponseWithErrorField
{
    data: {code: string},
}
interface VoteResponse extends ResponseWithErrorField{}
interface HasVotedResponse extends ResponseWithErrorField
{
    data: {hasVoted: boolean}
}
export function surveyOptionEqualOpperator(o1: SurveyOption, o2: SurveyOption): boolean
{
    return o1.id === o2.id && o1.value === o2.value && o1.votesCount === o2.votesCount;
}
export function surveyDataEqualOpperator(s1: SurveyData, s2: SurveyData): boolean
{
    if(s1.id !== s2.id || s1.code !== s2.code || s1.question !== s2.question) return false;

    for(let i = 0; i < s1.options.length || i < s2.options.length; i++)
    {
        const s1Option = s1.options[i];
        const s2Option = s2.options[i];

        if(!s1Option || !s2Option) console.log("s1 or s2 is null");
        if(!s1Option || !s2Option || !surveyOptionEqualOpperator(s1Option, s2Option)) return false;

    }
    return true;
}
export async function getSurveyInfo(surveyCode: string): Promise<SurveyData | null>
{
    const data: GetSurveyInfoResponse | null =
        await requestGET<GetSurveyInfoResponse>(homeDir + `backend/survey/${surveyCode}`);
    if(!data || data.error !== "") return null;
    return data.data;
}

async function vote(optionId: number, surveyCode: string): Promise<boolean>
{
    const data: VoteResponse | null =
        await requestPOST<VoteResponse>(homeDir + `backend/${surveyCode}/vote`, {
                optionId: optionId
        });
    if(!data || data.error !== "") return false;
    return true;
}

async function hasVoted(surveyCode: string): Promise<boolean | null>
{   
    const data: HasVotedResponse | null =
        await requestGET<HasVotedResponse>(homeDir + `backend/${surveyCode}/voted`);
    if(!data || data.error !== "") return null;
    return data.data.hasVoted;
}

function render(data: SurveyData)
{
    ($("header .copy span") as HTMLElement).innerText = data.code;
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
    });
}
const onCopy = (() => {
    let copyBtnSetTimeOutId: number | null = null;

    return async (surveyCode: string, copyBtnElem: HTMLButtonElement): Promise<void> => 
    {
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
    };
})();

export function setCopyBtnEventListener(code: string): void
{
    const copyBtnElem: HTMLButtonElement = $("button.copy") as HTMLButtonElement;
    copyBtnElem.addEventListener("click", async () => {await onCopy(code, copyBtnElem)});
}
function setOptionsEventListener(code: string): void
{
    const chooseOptionContainerElem = $(".js-choose-option-container") as HTMLFormElement;
    const optionButtonElems = chooseOptionContainerElem.querySelectorAll("button");

    optionButtonElems.forEach((btn) => {
        const optionId = parseInt(btn.getAttribute("data-option-id") as string);

        btn.addEventListener("click", async () => {
            if(!await vote(optionId, code))
            {
                goTo("index.html", {"error-title": "Something went wrong while voting", "error-content": "Try again later or try other survey"});
            }
            else
            {
                goTo("pages/results.html", {"code": code});
            }
        })
    })
}
export async function init(): Promise<void>
{
    let code: string | null = new URL(document.URL).searchParams.get("code");

    console.log(code);

    if(!code) goTo("index.html");

    code = code as string;


    let surveyInfo: SurveyData | null = await getSurveyInfo(code);
    if(!surveyInfo)
        goTo("index.html", {"error-title": "Something went wrong while loading survey data", "error-content": "Try again later or try other survey"});
    surveyInfo = surveyInfo as SurveyData;

    const voted = await hasVoted(code);
    if(voted === null)
        goTo("index.html", {"error-title": "Something went wrong while voting", "error-content": "Try again later or try other survey"});
    if(voted === true)
        goTo("pages/results.html", {"code": code});

    render(surveyInfo);

    setCopyBtnEventListener(code);

    setOptionsEventListener(code);
}