import { Utilities } from "./utilities.js";
export const element = document.querySelector("header");
export const loggedInNav = element.querySelector("nav.logged-in");
export const loggedInMenuToggle = document.querySelector("nav.logged-in + .menu-toggle button");
export const loggedInNavMenu = loggedInNav.querySelector(".menu");
export const logoutButton = loggedInNavMenu.querySelector(".logout");
export const menuImgContainer = loggedInNavMenu.querySelector(".img-container");
export const userPhoto = menuImgContainer.querySelector("[data-update-field=photo]");
export const providerLogo = menuImgContainer.querySelector("[data-update-field=provider-logo]");
export const userName = element.querySelector("[data-update-field=name]");
export const userEmail = element.querySelector("[data-update-field=email]");
export const HideHeaderMenu = () => Utilities.HideElement(loggedInNavMenu);
//# sourceMappingURL=header.js.map