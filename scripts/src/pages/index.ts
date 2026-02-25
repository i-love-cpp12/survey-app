import { $ } from "../shared/selectors.js";
import { requestPOST, ResponseWithErrorField } from "../shared/request.js";

interface PopupError
{
    title: string,
    content: string
}
interface ValidateResponce extends ResponseWithErrorField
{
    isCodeOk: boolean,
}

const showPopup = (() => {
    let popupSetTimeOutId: number | null = null;
    
    return (error: PopupError): void =>
    {
        const popupElem = $(".popup") as HTMLElement;

        (popupElem.querySelector(".title") as HTMLElement).innerText = error.title;
        (popupElem.querySelector("div") as HTMLElement).innerText = error.content;

        popupElem.classList.remove("hidden");

        if (popupSetTimeOutId !== null)
            clearTimeout(popupSetTimeOutId);
        
        popupSetTimeOutId = setTimeout(() => {
            popupElem.classList.add("hidden");
            popupSetTimeOutId = null;
        }, 5000);
    };
})();

async function onFormSubmit(e: Event | null, formElem: HTMLFormElement): Promise<void>
{
    if(e)
        e.preventDefault();

    try
    {
        const formData = new FormData(formElem);

        const code = formData.get("survey-code")?.toString() ?? "";
        
        const data: ValidateResponce | null =
            await requestPOST<ValidateResponce>("/survey/backend/validate_survey_code.php", {surveyCode: code})
        
        if(data === null) throw new Error("Server error");

        const isValid = data && data.error === "" && data.isCodeOk;
        
        isValid ? console.log(data) : console.error(data);
        console.log("is valid:", isValid);

        if(isValid)
        {
            document.location.href = `/survey/pages/vote.html?code=${encodeURIComponent(code)}`;
        }
        else showPopup({title: "Survey not found", content: "No survey exists with that code. Check and try again."});
    }
    catch(err)
    {
        showPopup({title: "Server Error", content: "Something went wrong. Please try again later."} as PopupError);
    }
}

function setFromEventListener(): void
{
    const formElem = $(".js-enter-code-form") as HTMLFormElement
    formElem.addEventListener("submit", (e: Event) => {onFormSubmit(e, formElem)});
}

async function init(): Promise<void>
{
    const url = new URL(document.URL);
    
    const paramCode: string | null = url.searchParams.get("code");
    const paramErrorTitle: string | null = url.searchParams.get("error-title");
    const paramErrorConent: string | null = url.searchParams.get("error-content");
    
    if(paramErrorTitle !== null) url.searchParams.delete("error-title");
    if(paramErrorConent !== null) url.searchParams.delete("error-content");
    
    window.history.replaceState({}, "", url);
    
    const poupError: PopupError | null =
        paramErrorTitle && paramErrorConent ? {title: paramErrorTitle, content: paramErrorConent} as PopupError : null;
        
    if(paramCode)
    {
        const formElem = $(".js-enter-code-form") as HTMLFormElement
        formElem["survey-code"].value = paramCode;
        onFormSubmit(null, formElem);
    }
    if(poupError)
        showPopup(poupError)

    setFromEventListener();
}

await init();