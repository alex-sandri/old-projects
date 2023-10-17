import { Utilities } from './utilities.js';
const genericMessage = document.querySelector(".generic-message");
export const content = genericMessage.querySelector("p");
export const show = (message) => {
    if (Utilities.IsSet(message))
        content.innerHTML = message;
    Utilities.ShowElement(genericMessage, "block");
    setTimeout(() => hide(), 2000);
};
export const hide = () => {
    Utilities.HideElement(genericMessage);
    removeContent();
};
export const removeContent = () => { content.innerHTML = ""; };
//# sourceMappingURL=generic-message.js.map