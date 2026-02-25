export function setIntervalStartNoDelay(callback, timeMS) {
    callback();
    return setInterval(callback, timeMS);
}
