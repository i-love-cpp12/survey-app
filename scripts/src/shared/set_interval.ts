type Callback = () => void;
export function setIntervalNoDelay(callback: Callback, timeMS: number): number
{
    callback();
    return setInterval(callback, timeMS);
}