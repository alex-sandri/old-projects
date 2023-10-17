import { Utilities } from "./utilities.js";

export const element : HTMLElement = document.querySelector("header");

export const loggedInNav : HTMLButtonElement = element.querySelector("nav.logged-in");
export const loggedInMenuToggle : HTMLButtonElement = document.querySelector("nav.logged-in + .menu-toggle button");
export const loggedInNavMenu : HTMLDivElement = loggedInNav.querySelector(".menu");
export const logoutButton : HTMLButtonElement = loggedInNavMenu.querySelector(".logout");

export const menuImgContainer : HTMLDivElement = loggedInNavMenu.querySelector(".img-container");
export const userPhoto : HTMLImageElement = menuImgContainer.querySelector("[data-update-field=photo]");
export const providerLogo : HTMLImageElement = menuImgContainer.querySelector("[data-update-field=provider-logo]");
export const userName : HTMLParagraphElement = element.querySelector("[data-update-field=name]");
export const userEmail : HTMLParagraphElement = element.querySelector("[data-update-field=email]");

export const HideHeaderMenu = () : void => Utilities.HideElement(loggedInNavMenu);