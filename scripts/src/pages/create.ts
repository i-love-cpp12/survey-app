import { $ } from "../shared/selectors.js";
import { requestPOST } from "../shared/request.js";
import {CreateSurveyResponce, GetSurveyInfoResponse } from "../shared/vote_module.js";
import { copyToClipboard } from "../shared/clipboard.js";
import { goTo, homeDir} from "../shared/link.js";

interface FormCreateSurveyData
{
    question: string,
    options: Array<string>
}
function clearErrorsMsgInForm(question: HTMLInputElement, options: NodeListOf<HTMLInputElement>): void
{
    (question.nextElementSibling as HTMLElement).innerText = "";
    question?.parentElement?.classList.remove("field-not-valid");

    options.forEach((option) => {
        const optionContainer = (option.parentElement as HTMLElement).parentElement as HTMLElement;

        (optionContainer.querySelector(".error-content") as HTMLElement).innerText = "";
        optionContainer.classList.remove("field-not-valid");
    });
}
function validateForm(question: HTMLInputElement, options: NodeListOf<HTMLInputElement>): boolean | null
{
    clearErrorsMsgInForm(question, options);

    let valid = true;
    if(question.value === "")
    {
        (question.nextElementSibling as HTMLElement).innerText = "Question can not be empty";
        question?.parentElement?.classList.add("field-not-valid");

        valid = false;
    }
    if(options.length < 2) return null;
    options.forEach((option) => {
        if(option.value === "")
        {
            const optionContainer = (option.parentElement as HTMLElement).parentElement as HTMLElement;

            (optionContainer.querySelector(".error-content") as HTMLElement).innerText = "Option can not be empty";
            optionContainer.classList.add("field-not-valid");

            valid = false;
        }
    })
    return valid;
}

function setFormEventListener(): void
{
    const formElem = ($("#create-survey-form") as HTMLFormElement);

    formElem.addEventListener("submit", async (e) => {onFormSubmit(e, formElem)});
}

async function onFormSubmit(e: Event, formElem: HTMLFormElement): Promise<void>
{
    e.preventDefault();
    const questionElem: HTMLInputElement = formElem.querySelector(".question input") as HTMLInputElement;
    const optionElems: NodeListOf<HTMLInputElement> = formElem.querySelectorAll<HTMLInputElement>(".option input");

    const isValid = validateForm(questionElem, optionElems);
    if(isValid === null)
    {
        goTo("index.html", {"error-title": "Something went wrong while creating survey", "error-content": "Try again later"})
    }
    if(!isValid)
        return;

    const formData: FormCreateSurveyData = {question: questionElem.value, options: Array.from(optionElems).map((o) => o.value)};

    const surveyData: CreateSurveyResponce | null = await requestPOST<CreateSurveyResponce>(homeDir + "backend/survey/create", formData);

    if(!surveyData)
    {
        goTo("index.html", {"error-title": "Something went wrong while creating survey", "error-content": "Try again later"})
        return;
    }
    await copyToClipboard(surveyData.data.code, questionElem);
    goTo("index.html", {"code": surveyData.data.code});
}

function refreshOptions(): void
{
    const optionConatiner = $(".options") as HTMLElement;
    const optionsElems = optionConatiner.querySelectorAll(".option input");
    if(optionsElems?.length > 2)
        optionConatiner.classList.add("multiple-opt");
    else
        optionConatiner.classList.remove("multiple-opt");
    
    optionsElems?.forEach((optionElem, i) => {
        optionElem.setAttribute("placeholder", `Option ${i + 1}`);
    });
}

function addOption(): HTMLElement
{
    const optionsConatiner = $(".options") as HTMLElement;

    const optionElem = document.createElement("div");
    optionElem.classList.add("option");
    optionElem.innerHTML = 
    `
        <div>
            <input type="text" aria-label="Enter option 1">
            <button class="delete-opt" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M256-213.85 213.85-256l224-224-224-224L256-746.15l224 224 224-224L746.15-704l-224 224 224 224L704-213.85l-224-224-224 224Z"/></svg>
            </button>
        </div>
        <div class="error-content"></div>
    `;
    optionsConatiner.append(optionElem);

    optionsConatiner.classList.add("multiple-opt");

    return optionElem;
}

function deleteOption(deleteBtn: HTMLElement): void
{
    deleteBtn?.parentElement?.parentElement?.remove();
}

async function init(): Promise<void>
{
    setFormEventListener();

    document.addEventListener("click", (e: Event) => {
        const target = e.target as HTMLElement;

        const deleteBtnElem = target.closest(".delete-opt") as HTMLButtonElement | null;
        if(deleteBtnElem)
        {
            deleteOption(deleteBtnElem);
            refreshOptions();
        }
        else if(target.matches(".add-option-conatiner button"))
        {
            addOption().querySelector("input")?.focus();
            refreshOptions();
        }
    });

}

await init();