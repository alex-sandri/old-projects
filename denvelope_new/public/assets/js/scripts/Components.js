import { Utilities } from "./utilities.js";
export class Component {
    constructor(type, options) {
        this.type = type;
        this.options = options;
        this.element = document.createElement(type);
        if (Utilities.IsSet(options)) {
            if (options.hasOwnProperty("aria")) {
                Array.from(Object.keys(options.aria)).forEach((option, i) => this.element.setAttribute("aria-" + option, Object.values(options)[i]));
                delete options.aria;
            }
            if (options.hasOwnProperty("children")) {
                options.children.forEach(element => this.element.appendChild(element));
                delete options.children;
            }
            if (options.hasOwnProperty("innerHTML")) {
                this.element.innerHTML = options.innerHTML;
                delete options.innerHTML;
            }
            Array.from(Object.keys(options)).forEach((option, i) => this.element.setAttribute(option, Object.values(options)[i]));
        }
    }
}
export class Input extends Component {
    constructor(options) {
        super("div", {
            class: Utilities.IsSet(options.class) ? options.class : "input",
            children: [
                new Component("input", {
                    ...options.attributes
                }).element,
                ...options.children
            ]
        });
        this.options = options;
    }
}
export class InputWithIcon extends Input {
    constructor(options) {
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
                            class: options.iconClassName,
                        }).element
                    ]
                }).element
            ],
            attributes: options.attributes
        });
        this.options = options;
    }
}
export class UsernameInput extends InputWithIcon {
    constructor(options) {
        super({
            iconClassName: "fas fa-fw fa-user",
            attributes: {
                type: "text",
                name: "username",
                autocomplete: "username",
                placeholder: "Username",
                id: "username",
                ...options
            }
        });
        this.options = options;
    }
}
export class EmailInput extends InputWithIcon {
    constructor(options) {
        super({
            iconClassName: "fas fa-fw fa-envelope",
            attributes: {
                type: "email",
                name: "email",
                autocomplete: "email",
                placeholder: "Email",
                id: "email",
                ...options
            }
        });
        this.options = options;
    }
}
export class PasswordInput extends InputWithIcon {
    constructor(options) {
        super({
            iconClassName: "fas fa-fw fa-eye",
            attributes: {
                type: "password",
                name: "password",
                autocomplete: "current-password",
                placeholder: "Password",
                id: "password",
                ...options
            }
        });
        this.options = options;
        const visibilityButton = this.element.querySelector(".input-icon");
        visibilityButton.addEventListener("click", () => {
            const input = this.element.querySelector("input");
            const icon = visibilityButton.querySelector("i");
            if (input.type === "password") {
                input.type = "text";
                Utilities.RemoveClass(icon, "fa-eye");
                Utilities.AddClass(icon, "fa-eye-slash");
            }
            else {
                input.type = "password";
                Utilities.RemoveClass(icon, "fa-eye-slash");
                Utilities.AddClass(icon, "fa-eye");
            }
        });
    }
}
export class PasswordStrengthComponent extends Component {
    constructor() {
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
//# sourceMappingURL=Components.js.map