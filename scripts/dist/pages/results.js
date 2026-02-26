import { $, $$ } from "../shared/selectors.js";
import { setCopyBtnEventListener, getSurveyInfo, surveyDataEqualOpperator } from "../shared/vote_module.js";
import { setIntervalNoDelay } from "../shared/set_interval.js";
//add refresh, add code reading from url params 
function render(surveyData, refreshRateMS = 2000) {
    $("header nav button.copy span").innerText = surveyData.surveyCode;
    $("section .title").innerText = surveyData.question;
    const optionContainerElem = $("section .options");
    optionContainerElem.innerHTML = "";
    const totalVoteCount = surveyData.options
        .reduce((acc, option) => {
        return acc + option.votesCount;
    }, 0);
    surveyData.options
        .forEach((option) => {
        const optionElem = document.createElement("div");
        optionElem.classList.add("option");
        const procent = Math.round((option.votesCount / totalVoteCount) * 100);
        optionElem.innerHTML =
            `
                <div>
                    <h3 class="option-name" data-option-id="${option.id}">${option.value}</h3>
                    <span>${procent}%</span>
                </div>
                <div class="progress-bar">
                    <div class="bar" data-procent="${procent}"></div>
                </div>
                <div><span>${option.votesCount}</span> vote</div>
            `;
        optionContainerElem.append(optionElem);
    });
    $("section .updates span").innerText = String(refreshRateMS / 1000);
    $("section .total-votes span").innerText = String(totalVoteCount);
}
function setProgressBars() {
    setTimeout(() => {
        $$(".progress-bar .bar")
            .forEach((progressBar) => {
            progressBar.style = `transform: translateX(${Number(progressBar.getAttribute("data-procent")) - 100}%)`;
        });
    }, 200);
}
const refresh = (() => {
    let surveyDataOld = null;
    return async (surveyCode, refreshRateMS) => {
        const surveyData = await getSurveyInfo(surveyCode);
        if (surveyData === null) {
            location.href = "/survey/index.html?error-title=Something went wrong while resiving results&error-content=Try again later or try other survey";
            return;
        }
        if (surveyDataOld !== null && surveyDataEqualOpperator(surveyData, surveyDataOld))
            return;
        render(surveyData, refreshRateMS);
        setProgressBars();
        surveyDataOld = { ...surveyData };
    };
})();
async function init(refreshRateMS) {
    const url = new URL(document.URL);
    const surveyCode = url.searchParams.get("code");
    if (!surveyCode) {
        location.href = "/survey/index.html?error-title=Something went wrong while resiving results&error-content=Try again later or try other survey";
        return;
    }
    setIntervalNoDelay(() => {
        refresh(surveyCode, refreshRateMS);
    }, refreshRateMS);
    setCopyBtnEventListener(surveyCode);
}
await init(5000);
