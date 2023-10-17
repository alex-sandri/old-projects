export class Utilities
{
    public static ShowElement = (element : HTMLElement, displayType : string) : void => {element.style.display = displayType;}

    public static ShowElements = (elements : Array<HTMLElement>, displayType : string) : void => elements.forEach(element => Utilities.ShowElement(element, displayType));

    public static HideElement = (element : HTMLElement) : void => {element.style.display = "none";}

    public static HideElements = (elements : Array<HTMLElement>) : void => elements.forEach(element => Utilities.HideElement(element));

    public static RemoveAllElements = (selector : string) : void => document.querySelectorAll(selector).forEach(element => element.remove());

    public static AddClass = (element : HTMLElement, className : string) : void => element.classList.add(className);

    public static RemoveClass = (element : HTMLElement, className : string) : void => element.classList.remove(className);

    public static RemoveAllClasses = (element : HTMLElement, options : Object = {except: ""}) : void =>
    {
        let numOfClasses = element.classList.length;
        let numOfDeletedClasses = 0;
    
        for(let i = 0; i < numOfClasses; i++){
            if(element.classList[i - numOfDeletedClasses] !== (<any>options).except){
                Utilities.RemoveClass(element, element.classList[i - numOfDeletedClasses]);
                numOfDeletedClasses++;
            }
        }
    }

    public static HasClass = (element : HTMLElement, className : string) : boolean => element.classList.contains(className);

    public static RemoveAllAttributes = (element : HTMLElement, options : Object = {except: ""}) : void =>
    {
        let numOfAttributes = element.attributes.length;
        let numOfDeletedAttributes = 0;
    
        for(let i = 0; i < numOfAttributes; i++){
            if(element.attributes[i - numOfDeletedAttributes] !== (<any>options).except){
                element.removeAttribute(element.attributes[i - numOfDeletedAttributes].nodeValue);
                numOfDeletedAttributes++;
            }
        }
    }

    public static IsSet = (object : any) : boolean => object !== null && object !== undefined;

    public static PreventDragEvents = () : void =>
    {
        document.addEventListener("drag", (e) => e.preventDefault());
        document.addEventListener("dragend", (e) => e.preventDefault());
        document.addEventListener("dragenter", (e) => e.preventDefault());
        document.addEventListener("dragexit", (e) => e.preventDefault());
        document.addEventListener("dragleave", (e) => e.preventDefault());
        document.addEventListener("dragover", (e) => e.preventDefault());
        document.addEventListener("dragstart", (e) => e.preventDefault());
        document.addEventListener("drop", (e) => e.preventDefault());
    }

    public static IsFileContextMenuItem = (element : HTMLElement) : boolean => Utilities.HasClass(element, "file");

    public static CreateErrorMessageParagraph = (message : string) : HTMLParagraphElement =>
    {
        let errorParagraph = document.createElement("p");
        
        errorParagraph.className = "input-error";
        errorParagraph.innerHTML = message;
    
        return errorParagraph;
    }
    
    public static RemoveAllErrorMessagesFromInputs = () : void =>
    {
        Utilities.RemoveAllElements(".input-error");
    
        (<NodeListOf<HTMLInputElement>>document.querySelectorAll("input.error")).forEach(element => {
            if (Utilities.HasClass(element, "error"))
            {
                Utilities.RemoveClass(element, "error");
                element.value = "";
            }
        });
    }

    public static GetAppRoot = () : string => (<HTMLInputElement>document.querySelector("input[name=app-root]")).value;
    public static GetApiEndpoint = () : string => (<HTMLInputElement>document.querySelector("input[name=api-endpoint]")).value;
    public static GetCsrfToken = () : string => "token";

    public static SetCookie = (name : string, value : string, months : number) =>
    {
        const d = new Date();

        d.setTime(d.getTime() + months * 30 * 24 * 60 * 60 * 1000);

        document.cookie = `${name}=${value};expires=${d.toUTCString()};path=/`;
    }

    public static DeleteCookie = (name : string) => Utilities.SetCookie(name, null, -1);

    public static IsSetCookie = (name : string) : boolean => document.cookie.indexOf(name + "=") > -1;

    public static DispatchEvent = (name : string) => window.dispatchEvent(new Event(name));

    public static FormatStorage (bytes : number) : string
    {
        let unit = "";

        for (var i = 0; bytes >= 1000; i++) bytes /= 1000;

        switch (i)
        {
            case 0:
                unit = "B"; // Byte 
            break;
            case 1:
                unit = "KB"; // KiloByte
            break;
            case 2:
                unit = "MB"; // MegaByte
            break;
            case 3:
                unit = "GB"; //GigaByte
            break;
            case 4:
                unit = "TB"; // TeraByte
            break;
            case 5:
                unit = "PB"; // PetaByte
            break;
            case 6:
                unit = "EB"; // ExaByte
            break;
            case 7:
                unit = "ZB"; // ZettaByte
            break;
            case 8:
                unit = "YB"; // YottaByte
            break;
            default:
            break;
        }

        return +bytes.toFixed(2) + unit;
    }
}