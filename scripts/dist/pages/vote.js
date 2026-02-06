import { $ } from "../shared/selectors.js";
async function getSurveyInfo(code) {
    const responce = await fetch("/survey/backend/get_survey_info.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ "code": code })
    });
    if (!responce.ok)
        return null;
    const data = await responce.json();
    console.log(data);
    if (data["error"] !== "" || !data["surveyInfo"])
        return null;
    return data["surveyInfo"];
}
function renderSurvey(data) {
    $("header .copy span").innerText = data.surveyCode;
    $("section > h2").innerText = data.question;
    const optionContainerElem = $("section .options");
    optionContainerElem.innerHTML = "";
    data.options.forEach((optionData) => {
        const optionElem = document.createElement("div");
        optionElem.classList.add("option");
        optionElem.innerHTML =
            `
            <button class="clear-btn">${optionData.value}</button>
        `;
        optionContainerElem.appendChild(optionElem);
    });
}
async function init() {
    let code = new URL(document.URL).searchParams.get("code");
    console.log(code);
    if (!code)
        document.location.href = "/survey";
    code = code;
    let surveyInfo = await getSurveyInfo(code);
    if (!surveyInfo)
        document.location.href = "/survey";
    surveyInfo = surveyInfo;
    renderSurvey(surveyInfo);
}
init();
