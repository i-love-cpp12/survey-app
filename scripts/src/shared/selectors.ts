export function $(selector: string): Element | null
{
    return document.querySelector(selector);
}
export function $$(selector: string): NodeListOf<Element> | null
{
    return document.querySelectorAll(selector);
}