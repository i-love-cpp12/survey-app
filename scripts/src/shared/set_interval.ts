type Callback = () => void;
export function setIntervalStartNoDelay(callback: Callback, timeMS: number): number
{
    callback();
    return setInterval(callback, timeMS);
}