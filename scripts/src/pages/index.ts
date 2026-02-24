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

const url = new URL(document.URL);

const paramCode: string | null = url.searchParams.get("code");
const paramErrorTitle: string | null = url.searchParams.get("error-title");
const paramErrorConent: string | null = url.searchParams.get("error-content");

const poupError: PopupError | null =
    paramErrorTitle && paramErrorConent ? {title: paramErrorTitle, content: paramErrorConent} as PopupError : null;

const formElem = $(".js-enter-code-form") as HTMLFormElement

let popupSetTimeOutId: number | null = null;

function popup(error: PopupError): void
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
    }, 5000)
}

async function onFormSubmit(e: Event | null): Promise<void>
{
    if(e)
        e.preventDefault();

    try
    {
        const formData = new FormData(formElem);

        const code = formData.get("survey-code")?.toString() ?? "";
        
        const data: ValidateResponce | null =
            await requestPOST<ValidateResponce>("/survey/backend/validate_survey_code.php", {surveyCode: code})
        
        const isValid = data && data.error === "" && data.isCodeOk;
        
        isValid ? console.log(data) : console.error(data);
        console.log("is valid:", isValid);

        if(isValid)
        {
            document.location.href = `/survey/pages/vote.html?code=${encodeURIComponent(code)}`;
        }
        else popup({title: "Survey not found", content: "No survey exists with that code. Check and try again."});
    }
    catch(err)
    {
        popup({title: "Server Error", content: "Something went wrong. Please try again later."} as PopupError);
    }
}

if(paramCode)
{
    formElem["survey-code"].value = paramCode;
    onFormSubmit(null);
}
if(poupError)
    popup(poupError)


formElem.addEventListener("submit", onFormSubmit);