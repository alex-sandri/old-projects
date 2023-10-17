import { Utilities } from "./utilities.js";
export class Translation {
}
Translation.Init = (element) => {
    if (!Utilities.IsSet(element))
        element = document;
    document.documentElement.lang = navigator.language;
    const ids = Array.from(new Set(Array.from(element.querySelectorAll("*"))
        .filter(element => element.hasAttribute("data-translation") || element.hasAttribute("data-placeholder-translation") || element.hasAttribute("data-content-translation"))
        .map(element => element.getAttribute("data-translation") || element.getAttribute("data-placeholder-translation") || element.getAttribute("data-content-translation"))));
    const script = document.createElement("script");
    script.src = `${Utilities.GetAppRoot()}/assets/js/translations/${navigator.language}.js`;
    script.onload = () => {
        ids.forEach(id => element.querySelectorAll(`[data-translation="${id}"]`).forEach(element => element.innerHTML += " " + Translation.Get(id)));
        ids.forEach(id => element.querySelectorAll(`[data-placeholder-translation="${id}"]`)
            .forEach(element => element.placeholder = Translation.Get(id)));
        ids.forEach(id => element.querySelectorAll(`[data-content-translation="${id}"]`).forEach(element => element.content = Translation.Get(id)));
    };
    document.body.append(script);
};
Translation.Get = (id) => {
    const keys = id.split("->");
    let array = window[navigator.language.replace("-", "_")];
    for (let i = 0; i < keys.length - 1; i++)
        array = array[keys[i]];
    return array[keys[keys.length - 1]];
};
//# sourceMappingURL=Translation.js.map