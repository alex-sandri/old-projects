import { Utilities } from './utilities.js';

const genericMessage : HTMLDivElement = document.querySelector(".generic-message");
export const content : HTMLDivElement = genericMessage.querySelector("p");

export const show = (message ?: string) : void => {
    if (Utilities.IsSet(message)) content.innerHTML = message;

    Utilities.ShowElement(genericMessage, "block");

    setTimeout(() => hide(), 2000);
}

export const hide = () : void => {
    Utilities.HideElement(genericMessage);
    
    removeContent();
}

export const removeContent = () : void => {content.innerHTML = "";}