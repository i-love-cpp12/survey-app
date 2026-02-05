import { $ } from "../shared/selectors.js";
const formElem = $(".js-enter-code-form");
formElem.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(formElem);
    const code = formData.get("survey-code")?.toString();
    const responce = await fetch("/survey/backend/validate_survey_code.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ code: code })
    });
    let data = undefined;
    if (responce.ok) {
        data = await responce.json();
    }
    const isValid = data["error"] === "" && data["isCodeOk"] === true;
    console.log(data);
    console.log(isValid);
});
