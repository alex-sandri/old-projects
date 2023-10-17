import { Utilities } from "./utilities.js";
export class Modal {
    constructor(options) {
        this.element = document.querySelector(".modal").cloneNode(true);
        this.spinner = this.element.querySelector(".spinner");
        this.Content = this.element.querySelector(".content");
        this.CloseButton = this.element.querySelector(".close");
        this.ConfirmButton = this.element.querySelector(".confirm");
        this.UpdateButton = this.element.querySelector(".update");
        this.Show = (unique) => {
            this.CloseButton.addEventListener("click", () => {
                this.Hide();
                this.Remove();
                this.OnClose;
            });
            this.ConfirmButton.addEventListener("click", this.OnConfirm);
            this.UpdateButton.addEventListener("click", this.OnUpdate);
            if (Utilities.IsSet(unique) && unique)
                document.querySelectorAll(".modal.show").forEach(element => element.remove());
            if (!Utilities.HasClass(this.element, "show")) {
                if (this.Content.innerHTML.trim() === "")
                    Utilities.ShowElement(this.spinner, "block");
                Utilities.RemoveClass(this.element, "hide");
                Utilities.AddClass(this.element, "show");
            }
        };
        this.Hide = () => {
            Utilities.RemoveClass(this.element, "show");
            Utilities.AddClass(this.element, "hide");
        };
        this.Remove = () => {
            setTimeout(() => this.element.remove(), getComputedStyle(this.element).getPropertyValue("animation-duration").replace(/[a-z]+/g, "") * 1000);
        };
        this.HideAndRemove = () => {
            this.Hide();
            this.Remove();
        };
        this.AppendContent = (data) => {
            Utilities.HideElement(this.spinner);
            data.forEach(element => this.Content.append(element));
        };
        this.RemoveContent = () => {
            Utilities.ShowElement(this.spinner, "block");
            this.Content.innerHTML = "";
        };
        if (Utilities.IsSet(options)) {
            if (options.hasOwnProperty("title"))
                this.Title = options.title;
            if (options.hasOwnProperty("subtitle"))
                this.Subtitle = options.subtitle;
            if (options.hasOwnProperty("allow")) {
                if (options.allow.includes("close"))
                    Utilities.ShowElement(this.CloseButton, "block");
                if (options.allow.includes("confirm"))
                    Utilities.ShowElement(this.ConfirmButton, "block");
                if (options.allow.includes("update"))
                    Utilities.ShowElement(this.UpdateButton, "block");
            }
            if (options.hasOwnProperty("floating") && options.floating)
                Utilities.AddClass(this.element, "floating");
            if (options.hasOwnProperty("animate") && !options.animate)
                Utilities.AddClass(this.element, "no-animate");
        }
        this.OnClose = this.OnConfirm = this.OnUpdate = () => { };
        document.body.appendChild(this.element);
    }
    set Title(title) {
        const titleElement = document.createElement("h1");
        titleElement.className = "title";
        titleElement.innerHTML = title;
        this.Content.querySelector(".heading").insertAdjacentElement("afterbegin", titleElement);
    }
    set Subtitle(subtitle) {
        const subtitleElement = document.createElement("h4");
        subtitleElement.className = "subtitle";
        subtitleElement.innerHTML = subtitle;
        this.Content.querySelector(".heading").insertAdjacentElement("beforeend", subtitleElement);
    }
}
//# sourceMappingURL=modal.js.map