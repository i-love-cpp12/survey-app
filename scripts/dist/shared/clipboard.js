export async function copyToClipboard(message, domElem = null) {
    if (!message)
        return false;
    if (navigator && window.isSecureContext) {
        try {
            await navigator.clipboard.writeText(message);
            return true;
        }
        catch (err) {
            console.error("Could'n copy to clipborad. trying fallback method. " + err);
        }
    }
    if (!domElem)
        return false;
    const toCopyFromElem = document.createElement("textarea");
    toCopyFromElem.style.position = "fixed";
    toCopyFromElem.style.bottom = "-99999px";
    toCopyFromElem.style.left = "-99999px";
    toCopyFromElem.innerText = message;
    domElem.append(toCopyFromElem);
    toCopyFromElem.select();
    try {
        document.execCommand("copy");
    }
    catch (err) {
        console.error("Fallback method did'n work either. " + err);
        toCopyFromElem.remove();
        return false;
    }
    toCopyFromElem.remove();
    return true;
}
