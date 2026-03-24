import { $, $$ } from "../shared/selectors.js";
import { setCopyBtnEventListener, getSurveyInfo, SurveyOption, SurveyData, surveyDataEqualOpperator }
    from "../shared/vote_module.js";
import { setIntervalNoDelay } from "../shared/set_interval.js";
import { goTo } from "../shared/link.js";
//add code reading from url params 
function render(surveyData: SurveyData, refreshRateMS: number = 2000): void
{
    ($("header nav button.copy span") as HTMLElement).innerText = surveyData.code; 
    ($("section .title") as HTMLElement).innerText = surveyData.question;

    const optionContainerElem = $("section .options") as HTMLElement;
    optionContainerElem.innerHTML = "";

    const totalVoteCount = surveyData.options
        .reduce((acc, option) => {
            return acc + option.votesCount;
        }, 0);
    surveyData.options
        .forEach((option: SurveyOption) => {
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
                <div><span>${option.votesCount}</span> ${option.votesCount === 1 ? "vote" : "votes"}</div>
            `;

            optionContainerElem.append(optionElem);
        });
    ($("section .updates span") as HTMLElement).innerText = String(refreshRateMS / 1000);
    ($("section .total-votes span") as HTMLElement).innerText = String(totalVoteCount);
}

function setProgressBars(): void
{
    setTimeout(() => {
        ($$(".progress-bar .bar") as NodeListOf<HTMLElement>)
            .forEach((progressBar) => {
                progressBar.style = `transform: translateX(${Number(progressBar.getAttribute("data-procent")) - 100}%)`;
            })
    }, 200);
}
const refresh = (() => {
    let surveyDataOld: SurveyData | null = null;

    return async (surveyCode: string, refreshRateMS: number) => {
        const surveyData = await getSurveyInfo(surveyCode);

        if(surveyData === null)
        {
            goTo("index.html", {"error-title": "Something went wrong while resiving results", "error-content": "Try again later or try other survey"});
            return;
        }

        if(surveyDataOld !== null && surveyDataEqualOpperator(surveyData, surveyDataOld)) return;

        render(surveyData, refreshRateMS);

        setProgressBars();

        surveyDataOld = {...surveyData} as SurveyData;
    }
})();

async function init(refreshRateMS: number): Promise<void>
{
    const url = new URL(document.URL);
    const surveyCode: string | null = url.searchParams.get("code");
    if(!surveyCode)
    {
        goTo("index.html", {"error-title": "Something went wrong while resiving results", "error-content": "Try again later or try other survey"});
        return;
    }
    setIntervalNoDelay(() => {
        refresh(surveyCode, refreshRateMS);
    }, refreshRateMS);
    setCopyBtnEventListener(surveyCode);
}
await init(5000);