export class Utilities {
    static FormatStorage(bytes) {
        let unit = "";
        for (var i = 0; bytes >= 1000; i++)
            bytes /= 1000;
        switch (i) {
            case 0:
                unit = "B";
                break;
            case 1:
                unit = "KB";
                break;
            case 2:
                unit = "MB";
                break;
            case 3:
                unit = "GB";
                break;
            case 4:
                unit = "TB";
                break;
            case 5:
                unit = "PB";
                break;
            case 6:
                unit = "EB";
                break;
            case 7:
                unit = "ZB";
                break;
            case 8:
                unit = "YB";
                break;
            default:
                break;
        }
        return +bytes.toFixed(2) + unit;
    }
}
Utilities.ShowElement = (element, displayType) => { element.style.display = displayType; };
Utilities.ShowElements = (elements, displayType) => elements.forEach(element => Utilities.ShowElement(element, displayType));
Utilities.HideElement = (element) => { element.style.display = "none"; };
Utilities.HideElements = (elements) => elements.forEach(element => Utilities.HideElement(element));
Utilities.RemoveAllElements = (selector) => document.querySelectorAll(selector).forEach(element => element.remove());
Utilities.AddClass = (element, className) => element.classList.add(className);
Utilities.RemoveClass = (element, className) => element.classList.remove(className);
Utilities.RemoveAllClasses = (element, options = { except: "" }) => {
    let numOfClasses = element.classList.length;
    let numOfDeletedClasses = 0;
    for (let i = 0; i < numOfClasses; i++) {
        if (element.classList[i - numOfDeletedClasses] !== options.except) {
            Utilities.RemoveClass(element, element.classList[i - numOfDeletedClasses]);
            numOfDeletedClasses++;
        }
    }
};
Utilities.HasClass = (element, className) => element.classList.contains(className);
Utilities.RemoveAllAttributes = (element, options = { except: "" }) => {
    let numOfAttributes = element.attributes.length;
    let numOfDeletedAttributes = 0;
    for (let i = 0; i < numOfAttributes; i++) {
        if (element.attributes[i - numOfDeletedAttributes] !== options.except) {
            element.removeAttribute(element.attributes[i - numOfDeletedAttributes].nodeValue);
            numOfDeletedAttributes++;
        }
    }
};
Utilities.IsSet = (object) => object !== null && object !== undefined;
Utilities.PreventDragEvents = () => {
    document.addEventListener("drag", (e) => e.preventDefault());
    document.addEventListener("dragend", (e) => e.preventDefault());
    document.addEventListener("dragenter", (e) => e.preventDefault());
    document.addEventListener("dragexit", (e) => e.preventDefault());
    document.addEventListener("dragleave", (e) => e.preventDefault());
    document.addEventListener("dragover", (e) => e.preventDefault());
    document.addEventListener("dragstart", (e) => e.preventDefault());
    document.addEventListener("drop", (e) => e.preventDefault());
};
Utilities.IsFileContextMenuItem = (element) => Utilities.HasClass(element, "file");
Utilities.CreateErrorMessageParagraph = (message) => {
    let errorParagraph = document.createElement("p");
    errorParagraph.className = "input-error";
    errorParagraph.innerHTML = message;
    return errorParagraph;
};
Utilities.RemoveAllErrorMessagesFromInputs = () => {
    Utilities.RemoveAllElements(".input-error");
    document.querySelectorAll("input.error").forEach(element => {
        if (Utilities.HasClass(element, "error")) {
            Utilities.RemoveClass(element, "error");
            element.value = "";
        }
    });
};
Utilities.GetAppRoot = () => document.querySelector("input[name=app-root]").value;
Utilities.GetApiEndpoint = () => document.querySelector("input[name=api-endpoint]").value;
Utilities.GetCsrfToken = () => "token";
Utilities.SetCookie = (name, value, months) => {
    const d = new Date();
    d.setTime(d.getTime() + months * 30 * 24 * 60 * 60 * 1000);
    document.cookie = `${name}=${value};expires=${d.toUTCString()};path=/`;
};
Utilities.DeleteCookie = (name) => Utilities.SetCookie(name, null, -1);
Utilities.IsSetCookie = (name) => document.cookie.indexOf(name + "=") > -1;
Utilities.DispatchEvent = (name) => window.dispatchEvent(new Event(name));
//# sourceMappingURL=utilities.js.map