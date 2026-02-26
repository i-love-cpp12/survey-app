export function setIntervalNoDelay(callback, timeMS) {
    callback();
    return setInterval(callback, timeMS);
}
