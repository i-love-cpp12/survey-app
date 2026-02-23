import { $ } from "../shared/selectors.js";

const paramCode: string | null = new URL(document.URL).searchParams.get("code");

const formElem = $(".js-enter-code-form") as HTMLFormElement

let popupSetTimeOutId: number | null = null;

async function onFormSubmit(e: Event | null): Promise<void>
{
    if(e)
        e.preventDefault();

    const formData = new FormData(formElem);

    const code = formData.get("survey-code")?.toString() as string;

    const responce = await fetch("/survey/backend/validate_survey_code.php", {
        method: "POST",
        headers: {"Content-Type" : "application/json"},
        body: JSON.stringify(
            {code: code}
        )
    })
    let data = undefined;
    if(responce.ok)
    {
        data = await responce.json();
    }

    const isValid = data["error"] === "" && data["isCodeOk"] === true;
    
    console.log(data);
    console.log(isValid);

    if(isValid)
    {
        document.location.href = `/survey/pages/vote.html?code=${code}`;
    }
    else
    {
        const popupElem = $(".popup") as HTMLElement;
        popupElem.classList.remove("hidden");

        if (popupSetTimeOutId)
            clearTimeout(popupSetTimeOutId);
        
        popupSetTimeOutId = setTimeout(() => {
            popupElem.classList.add("hidden");
            popupSetTimeOutId = null;
        }, 5000)
    }
}

if(paramCode)
{
    formElem["survey-code"].value = paramCode;
    onFormSubmit(null);
}

formElem.addEventListener("submit", onFormSubmit);