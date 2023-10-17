import { Utilities } from "./utilities.js";

export class Component
{
    public element : HTMLElement;

    constructor (protected type : string, protected options ?: Object)
    {
        this.element = document.createElement(type);

        if (Utilities.IsSet(options))
        {
            if (options.hasOwnProperty("aria"))
            {
                Array.from(Object.keys((<any>options).aria)).forEach((option, i) => this.element.setAttribute("aria-" + option, Object.values(options)[i]));

                delete (<any>options).aria;
            }

            if (options.hasOwnProperty("children"))
            {
                (<HTMLElement[]>(<any>options).children).forEach(element => this.element.appendChild(element));

                delete (<any>options).children;
            }

            if (options.hasOwnProperty("innerHTML"))
            {
                this.element.innerHTML = (<any>options).innerHTML;

                delete (<any>options).innerHTML;
            }
            
            Array.from(Object.keys(options)).forEach((option, i) => this.element.setAttribute(option, Object.values(options)[i]));
        }
    }
}

export class Input extends Component
{
    constructor (protected options : Object)
    {
        super("div", {
            class: Utilities.IsSet((<any>options).class) ? (<any>options).class : "input",
            children: [
                new Component("input", {
                    ...(<Component[]>(<any>options).attributes)
                }).element,
                ...(<Component[]>(<any>options).children)
            ]
        });
    }
}

export class InputWithIcon extends Input
{
    constructor (protected options : Object)
    {
        super({
            class: "input-with-icon",
            children: [
                new Component("button", {
                    type: "button",
                    class: "input-icon",
                    aria: {
                        hidden: true
                    },
                    tabindex: -1,
                    children: [
                        new Component("i", {
                            class: (<any>options).iconClassName,
                        }).element
                    ]
                }).element
            ],
            attributes: (<any>options).attributes
        });
    }
}

export class UsernameInput extends InputWithIcon
{
    constructor (protected options : Object)
    {
        super({
            iconClassName : "fas fa-fw fa-user",
            attributes: {
                type: "text",
                name: "username",
                autocomplete: "username",
                placeholder: "Username",
                id: "username",
                ...options
            }
        });
    }
}

export class EmailInput extends InputWithIcon
{
    constructor (protected options : Object)
    {
        super({
            iconClassName : "fas fa-fw fa-envelope",
            attributes: {
                type: "email",
                name: "email",
                autocomplete: "email",
                placeholder: "Email",
                id: "email",
                ...options
            }
        });
    }
}

export class PasswordInput extends InputWithIcon
{
    constructor (protected options : Object)
    {
        super({
            iconClassName : "fas fa-fw fa-eye",
            attributes: {
                type: "password",
                name: "password",
                autocomplete: "current-password",
                placeholder: "Password",
                id: "password",
                ...options
            }
        });

        const visibilityButton : HTMLButtonElement = this.element.querySelector(".input-icon");

        visibilityButton.addEventListener("click", () => {
            const input : HTMLInputElement = this.element.querySelector("input");
            const icon : HTMLElement = visibilityButton.querySelector("i");

            if (input.type === "password")
            {
                input.type = "text";

                Utilities.RemoveClass(icon, "fa-eye");
                Utilities.AddClass(icon, "fa-eye-slash");
            }
            else
            {
                input.type = "password";

                Utilities.RemoveClass(icon, "fa-eye-slash");
                Utilities.AddClass(icon, "fa-eye");
            }
        });
    }
}

export class PasswordStrengthComponent extends Component
{
    constructor ()
    {
        super("span", {
            class: "password-strength",
            children: [
                new Component("p", {
                    class: "password-strength-text",
                    innerHTML: "Password strength"
                }).element,
                new Component("span", {}).element
            ]
        });
    }
}