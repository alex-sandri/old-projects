import { Utilities } from "./utilities.js";

export class Modal
{
    public element : HTMLDivElement = (<HTMLDivElement>document.querySelector(".modal").cloneNode(true));

    private spinner : HTMLSpanElement = this.element.querySelector(".spinner");

    public readonly Content : HTMLDivElement = this.element.querySelector(".content");

    public readonly CloseButton : HTMLButtonElement = this.element.querySelector(".close");
    public readonly ConfirmButton : HTMLButtonElement = this.element.querySelector(".confirm");
    public readonly UpdateButton : HTMLButtonElement = this.element.querySelector(".update");

    public OnClose : () => any;
    public OnConfirm : () => any;
    public OnUpdate : () => any;

    constructor (options ?: Object)
    {
        if (Utilities.IsSet(options))
        {
            if (options.hasOwnProperty("title")) this.Title = (<any>options).title;
            if (options.hasOwnProperty("subtitle")) this.Subtitle = (<any>options).subtitle;

            if (options.hasOwnProperty("allow"))
            {
                if ((<string[]>(<any>options).allow).includes("close")) Utilities.ShowElement(this.CloseButton, "block");
                if ((<string[]>(<any>options).allow).includes("confirm")) Utilities.ShowElement(this.ConfirmButton, "block");
                if ((<string[]>(<any>options).allow).includes("update")) Utilities.ShowElement(this.UpdateButton, "block");
            }
            
            if (options.hasOwnProperty("floating") && (<any>options).floating) Utilities.AddClass(this.element, "floating");

            if (options.hasOwnProperty("animate") && !(<any>options).animate) Utilities.AddClass(this.element, "no-animate");
        }

        this.OnClose = this.OnConfirm = this.OnUpdate = () => {};

        document.body.appendChild(this.element);
    }

    /**
     * @param unique If set to true all other modals currently shown will be removed 
     */
    public Show = (unique ?: boolean) : void =>
    {
        this.CloseButton.addEventListener("click", () => {
            this.Hide();
            this.Remove();

            this.OnClose;
        });

        this.ConfirmButton.addEventListener("click", this.OnConfirm);
        this.UpdateButton.addEventListener("click", this.OnUpdate);
        
        if (Utilities.IsSet(unique) && unique) document.querySelectorAll(".modal.show").forEach(element => element.remove());

        if (!Utilities.HasClass(this.element, "show"))
        {
            if (this.Content.innerHTML.trim() === "") Utilities.ShowElement(this.spinner, "block");

            Utilities.RemoveClass(this.element, "hide");
            Utilities.AddClass(this.element, "show");
        }
    }

    public Hide = () : void =>
    {
        Utilities.RemoveClass(this.element, "show");
        Utilities.AddClass(this.element, "hide");
    }

    public Remove = () : void =>
    {
        setTimeout(() => this.element.remove(), <number><unknown>getComputedStyle(this.element).getPropertyValue("animation-duration").replace(/[a-z]+/g, "") * 1000);
    }

    public HideAndRemove = () : void =>
    {
        this.Hide();
        this.Remove();
    }

    public set Title (title : string)
    {
        const titleElement = document.createElement("h1");

        titleElement.className = "title";
        titleElement.innerHTML = title;

        this.Content.querySelector(".heading").insertAdjacentElement("afterbegin", titleElement);
    }

    public set Subtitle (subtitle : string)
    {
        const subtitleElement = document.createElement("h4");

        subtitleElement.className = "subtitle";
        subtitleElement.innerHTML = subtitle;

        this.Content.querySelector(".heading").insertAdjacentElement("beforeend", subtitleElement);
    }

    public AppendContent = (data : any[]) : void =>
    {
        Utilities.HideElement(this.spinner);
    
        data.forEach(element => this.Content.append(element));
    }

    public RemoveContent = () : void => {
        Utilities.ShowElement(this.spinner, "block");

        this.Content.innerHTML = "";
    }
}