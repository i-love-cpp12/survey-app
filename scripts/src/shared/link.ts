
export const homeDir = "http://localhost/survey/";
export function goTo(dir: string, args: object = {}): void
{
    console.log(homeDir + dir);
    const url = new URL(homeDir + dir);

    for(const [key, value] of Object.entries(args))
    {
        url.searchParams.append(key, value);
    }
    location.href = url.href;
}