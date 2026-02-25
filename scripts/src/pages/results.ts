import { $, $$} from "../shared/selectors.js";
import { setCopyBtnEventListener, getSurveyInfo, SurveyOption, SurveyData, GetSurveyInfoResponse }
    from "../shared/vote_module.js";
//add refresh, add code reading from url params 
function render(surveyData: SurveyData, refreshRateMS: number = 200): void
{
    ($("header nav button.copy span") as HTMLElement).innerText = surveyData.surveyCode; 
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
                <div><span>${option.votesCount}</span> vote</div>
            `;

            optionContainerElem.append(optionElem);
        });
    ($("section .updates span") as HTMLElement).innerText = String(refreshRateMS / 100);
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


async function init(): Promise<void>
{
    const surveyCode = "ABCDE";
    setCopyBtnEventListener(surveyCode);
    const surveyData: SurveyData | null = await getSurveyInfo(surveyCode);

    if(surveyData === null)
    {
        location.href = "/survey/index.html?error-title=Something went wrong while resiving results&error-content=Try again later or try other survey";
        return;
    }

    render(surveyData);

    setProgressBars();
}
await init();