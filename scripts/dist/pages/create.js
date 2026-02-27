import { $ } from "../shared/selectors.js";
import { requestPOST } from "../shared/request.js";
import { copyToClipboard } from "../shared/clipboard.js";
import { goTo, homeDir } from "../shared/link.js";
function clearErrorsMsgInForm(question, options) {
    question.nextElementSibling.innerText = "";
    question?.parentElement?.classList.remove("field-not-valid");
    options.forEach((option) => {
        const optionContainer = option.parentElement.parentElement;
        optionContainer.querySelector(".error-content").innerText = "";
        optionContainer.classList.remove("field-not-valid");
    });
}
function validateForm(question, options) {
    clearErrorsMsgInForm(question, options);
    let valid = true;
    if (question.value === "") {
        question.nextElementSibling.innerText = "Question can not be empty";
        question?.parentElement?.classList.add("field-not-valid");
        valid = false;
    }
    if (options.length < 2)
        return null;
    options.forEach((option) => {
        if (option.value === "") {
            const optionContainer = option.parentElement.parentElement;
            optionContainer.querySelector(".error-content").innerText = "Option can not be empty";
            optionContainer.classList.add("field-not-valid");
            valid = false;
        }
    });
    return valid;
}
function setFormEventListener() {
    const formElem = $("#create-survey-form");
    formElem.addEventListener("submit", async (e) => { onFormSubmit(e, formElem); });
}
async function onFormSubmit(e, formElem) {
    e.preventDefault();
    const questionElem = formElem.querySelector(".question input");
    const optionElems = formElem.querySelectorAll(".option input");
    const isValid = validateForm(questionElem, optionElems);
    if (isValid === null) {
        goTo("index.html", { "error-title": "Something went wrong while creating survey", "error-content": "Try again later" });
    }
    if (!isValid)
        return;
    const formData = { question: questionElem.value, options: Array.from(optionElems).map((o) => o.value) };
    const surveyData = await requestPOST(homeDir + "backend/create_survey.php", formData);
    if (!surveyData) {
        goTo("index.html", { "error-title": "Something went wrong while creating survey", "error-content": "Try again later" });
        return;
    }
    await copyToClipboard(surveyData.surveyInfo.surveyCode, questionElem);
    goTo("index.html", { "code": surveyData.surveyInfo.surveyCode });
}
function refreshOptions() {
    const optionConatiner = $(".options");
    const optionsElems = optionConatiner.querySelectorAll(".option input");
    if (optionsElems?.length > 2)
        optionConatiner.classList.add("multiple-opt");
    else
        optionConatiner.classList.remove("multiple-opt");
    optionsElems?.forEach((optionElem, i) => {
        optionElem.setAttribute("placeholder", `Option ${i + 1}`);
    });
}
function addOption() {
    const optionsConatiner = $(".options");
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
function deleteOption(deleteBtn) {
    deleteBtn?.parentElement?.parentElement?.remove();
}
async function init() {
    setFormEventListener();
    document.addEventListener("click", (e) => {
        const target = e.target;
        const deleteBtnElem = target.closest(".delete-opt");
        if (deleteBtnElem) {
            deleteOption(deleteBtnElem);
            refreshOptions();
        }
        else if (target.matches(".add-option-conatiner button")) {
            addOption().querySelector("input")?.focus();
            refreshOptions();
        }
    });
}
await init();
