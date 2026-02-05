import { $ } from "../shared/selectors.js";

const formElem = $(".js-enter-code-form") as HTMLFormElement 
formElem.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(formElem);

    const code = formData.get("survey-code")?.toString() as string;

    fetch("backend/validate_survey_code", {
        method: "POST",
        headers: {"Content-Type" : "application/json"},
        body: JSON.stringify(
            {code: code}
        )
    })
})