export {};

import * as loadEvents from './scripts/load-events.js';

import { Utilities } from "./scripts/utilities.js";
import { Modal } from "./scripts/modal.js";
import { Request, RequestMethod } from './scripts/ajax.js';
import { UsernameInput, EmailInput, PasswordInput, PasswordStrengthComponent } from "./scripts/Components.js";

loadEvents.Init();

const settingsRoot : string = (<HTMLInputElement>document.querySelector("input[name=settings-root]")).value;

const settingsMenu = document.querySelector(".settings-menu");
const settingsMenuButtons = settingsMenu.querySelectorAll("button");

const settingsSections : NodeListOf<HTMLElement> = document.querySelectorAll(".settings-section");

const usernameSetting : HTMLDivElement = document.querySelector("#username-setting");

const emailSetting : HTMLDivElement = document.querySelector("#email-setting");

const preferredLanguageSetting : HTMLDivElement = document.querySelector("#preferred-language-setting");
const preferredLanguageSelect : HTMLSelectElement = preferredLanguageSetting.querySelector("select");

const deleteAccountSetting : HTMLDivElement = document.querySelector("#delete-account-setting");
const deleteAccountConfirm : HTMLButtonElement = deleteAccountSetting.querySelector("button.confirm");

const changePasswordSetting : HTMLButtonElement = document.querySelector("#change-password-setting");

const ApiEndpoint : string = Utilities.GetApiEndpoint();
const CSRFToken : string = Utilities.GetCsrfToken();

settingsMenuButtons.forEach(element => {
    element.addEventListener("click", (e) => {
        let button : HTMLButtonElement;

        if((<HTMLElement>e.target).querySelector("i")){ // Clicked element is the button
            button = (<HTMLButtonElement>e.target);
        }
        else{ // Clicked element is the icon
            button = (<HTMLButtonElement>(<HTMLElement>e.target).parentNode);
        }

        settingsMenuButtons.forEach(element => {
            Utilities.RemoveClass(element, "selected");
        });

        settingsSections.forEach(element => {
            Utilities.RemoveClass(element, "selected");
        });

        Utilities.AddClass(button, "selected");

        let section = button.getAttribute("data-sect");

        Utilities.AddClass(document.querySelector("#" + section), "selected")

        history.pushState(null, "", Utilities.GetAppRoot() + settingsRoot + section + "/");
    });
});

usernameSetting.addEventListener("click", () => {
    const modal = new Modal({
        "title": usernameSetting.querySelector("h2").innerHTML,
        "allow": [
            "close",
            "update"
        ]
    });

    modal.AppendContent([
        new UsernameInput({}).element
    ]);

    (<HTMLInputElement>modal.element.querySelector("#username")).value = usernameSetting.querySelector("p").innerHTML;

    modal.OnUpdate = () =>
    {
        const username = (<HTMLInputElement>modal.element.querySelector("#username")).value;

        const request = new Request(ApiEndpoint, RequestMethod.POST, {
            object: "user",
            action: "change_username",
            data: {
                username: username
            },
            csrf_token: CSRFToken
        }, true);

        request.OnSuccess = () =>
        {
            const response = request.GetResponse(true);

            Utilities.RemoveAllErrorMessagesFromInputs();

            if (!response.success) modal.element.querySelector("#username").parentElement
                .insertAdjacentElement("beforebegin", Utilities.CreateErrorMessageParagraph(response.errors["username"].message));
            else
            {
                document.querySelectorAll("[data-update-field=username]").forEach(element => element.innerHTML = username);

                modal.HideAndRemove();
            }
        }

        request.Send();
    }

    modal.Show(true);
});

emailSetting.addEventListener("click", () => {
    const modal = new Modal({
        "title": emailSetting.querySelector("h2").innerHTML,
        "allow": [
            "close",
            "update"
        ]
    });

    modal.AppendContent([
        new EmailInput({}).element
    ]);

    (<HTMLInputElement>modal.element.querySelector("#email")).value = emailSetting.querySelector("p").innerHTML;

    modal.Show(true);
});

preferredLanguageSetting.addEventListener("click", () => {
    const modal = new Modal({
        "title": preferredLanguageSetting.querySelector("h2").innerHTML,
        "allow": [
            "close",
            "update"
        ]
    });

    preferredLanguageSelect.value = (<HTMLOptionElement>preferredLanguageSelect.querySelector("option[data-selected=true]")).value;
    modal.AppendContent([preferredLanguageSelect]);

    modal.Show(true);
});

deleteAccountSetting.addEventListener("click", () => {
    const modal = new Modal({
        "title": deleteAccountSetting.querySelector("h2").innerHTML,
        "allow": [
            "close",
            "confirm"
        ]
    });

    modal.AppendContent([deleteAccountConfirm]);

    modal.OnConfirm = () =>
    {
        const request = new Request(ApiEndpoint, RequestMethod.POST, {
            "object": "user",
            "action": "delete",
            "data": {},
            "csrf_token": CSRFToken
        }, true);
    
        request.OnSuccess = () => {
            const response = request.GetResponse(true);
    
            Utilities.RemoveAllErrorMessagesFromInputs();
    
            if(response.action.success) modal.Hide();
            else
            {
                let error = response.action.errors[0];
    
                modal.Content.insertAdjacentElement("afterbegin", Utilities.CreateErrorMessageParagraph(error.message));
            }
        }
    
        request.Send();
    }

    modal.Show(true);
});

changePasswordSetting.addEventListener("click", () => {
    const modal = new Modal({
        "title": changePasswordSetting.querySelector("h2").innerHTML,
        "allow": [
            "close",
            "update",
        ]
    });

    modal.AppendContent([
        new PasswordInput({
            id: "current-password",
            placeholder: "Current Password",
            autocomplete: "current-password",
            name: "current_password",
        }).element,
        new PasswordInput({
            id: "new-password",
            placeholder: "New Password",
            autocomplete: "new-password",
            name: "new_password",
        }).element,
        new PasswordStrengthComponent().element
    ]);

    modal.OnUpdate = () => {
        const request = new Request(ApiEndpoint, RequestMethod.POST, {
            object: "user",
            action: "change_password",
            data: {
                current_password: (<HTMLInputElement>modal.element.querySelector("#current-password")).value,
                new_password: (<HTMLInputElement>modal.element.querySelector("#new-password")).value,
            },
            csrf_token: CSRFToken
        }, true);

        request.OnSuccess = () => {
            const response = request.GetResponse(true);

            Utilities.RemoveAllErrorMessagesFromInputs();

            if (!response.success)
            {
                const errors = response.errors;

                for (let i = 0; i < Object.keys(errors).length; i++)
                {
                    const element : HTMLInputElement = modal.element.querySelector("[name=" + Object.keys(errors)[i] + "]");

                    element.parentElement.insertAdjacentElement("beforebegin", Utilities.CreateErrorMessageParagraph((<any>Object.values(errors)[i]).message));
                }
            }
            else modal.HideAndRemove();
        }

        request.Send();
    }

    modal.Show(true);
});

window.addEventListener("popstate", () => {
    let url = window.location.href;

    let section = url[url.length - 1] === "/"
        ? (url = url.substr(0, url.length - 1)).substr(url.lastIndexOf("/") + 1)
        : url.substr(url.lastIndexOf("/") + 1)
    ;

    (<HTMLButtonElement>document.querySelector("button[data-sect=" + (section === "settings" ? "general" : section) + "]")).click();
});