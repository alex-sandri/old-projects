import { Utilities } from "./utilities.js";
import { Auth } from "./Auth.js";
import { loggedInMenuToggle, loggedInNavMenu, logoutButton, HideHeaderMenu } from "./header.js";
import { ServiceWorker } from "../service_workers/ServiceWorker.class.js";
import { Translation } from "./Translation.js";
export const Init = () => {
    const cookieBanner = document.querySelector(".cookie-banner");
    new ServiceWorker(Utilities.GetAppRoot());
    if (!Utilities.IsSetCookie("cookie_consent")) {
        Utilities.ShowElement(cookieBanner, "flex");
        cookieBanner.querySelector("i:last-child").addEventListener("click", () => Utilities.HideElement(document.querySelector(".cookie-banner")));
    }
    Utilities.PreventDragEvents();
    loggedInMenuToggle.addEventListener("click", () => {
        if (loggedInNavMenu.style.display === "flex")
            HideHeaderMenu();
        else
            Utilities.ShowElement(loggedInNavMenu, "flex");
    });
    logoutButton.addEventListener("click", () => Auth.SignOut());
    document.addEventListener("DOMContentLoaded", () => {
        Utilities.SetCookie("cookie_consent", "true", 60);
        HandlePreferredColorSchemeChange();
        Translation.Init();
        Auth.Init();
    });
    document.addEventListener("click", (e) => {
        if (!loggedInNavMenu.contains(e.target) && !loggedInMenuToggle.contains(e.target))
            HideHeaderMenu();
    });
    document.addEventListener("scroll", () => HideHeaderMenu());
    document.addEventListener("contextmenu", () => HideHeaderMenu());
    window.addEventListener("resize", () => HideHeaderMenu());
    window.addEventListener("load", () => Utilities.RemoveClass(document.body, "preload"));
    window.matchMedia("(prefers-color-scheme: light)").addEventListener("change", HandlePreferredColorSchemeChange);
};
const HandlePreferredColorSchemeChange = () => {
    document.querySelectorAll("meta[name=theme-color], meta[name=msapplication-navbutton-color]")
        .forEach(element => element.content = getComputedStyle(document.documentElement).getPropertyValue("--primary-color").trim());
    if (window.matchMedia("(prefers-color-scheme: light)").matches) {
    }
    else {
    }
};
//# sourceMappingURL=load-events.js.map