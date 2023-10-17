export {};

import * as loadEvents from './scripts/load-events.js';
import * as header from './scripts/header.js';
import * as genericMessage from './scripts/generic-message.js';

import { Utilities } from "./scripts/utilities.js";
import { Request, RequestMethod } from "./scripts/ajax.js";
import { Modal } from "./scripts/modal.js";
import { Auth } from './scripts/Auth.js';
import { Linguist } from './scripts/Linguist.js';
import { Component } from './scripts/Components.js';
import { Translation } from './scripts/Translation.js';

loadEvents.Init();

const db = (<any>window).firebase.firestore();
const storage = (<any>window).firebase.storage();

const sideMenu : HTMLElement = document.querySelector(".account-side-menu");
const sideMenuToggleButton : HTMLButtonElement = document.querySelector(".account-side-menu-toggle-button");

const main : HTMLElement = document.querySelector(".account-main");

const addFiles : HTMLButtonElement = document.querySelector("#add-files");
const fileInput : HTMLInputElement = document.querySelector("#files");

const addFolder : HTMLButtonElement = document.querySelector("#add-folder");
const folderInput : HTMLInputElement = document.querySelector("#folder");

const createFile : HTMLButtonElement = document.querySelector("#create-file");
const createFileNameInput : HTMLDivElement = document.querySelector("#create-file-input");

const createFolder : HTMLButtonElement = document.querySelector("#create-folder");
const createFolderNameInput : HTMLDivElement = document.querySelector("#create-folder-input");

const searchBar : HTMLInputElement = document.querySelector("#search");

const navigationBackButton : HTMLButtonElement = document.querySelector(".back-button");

const userContentLoadingSpinner : HTMLSpanElement = document.querySelector(".user-content > span");

const foldersContainer : HTMLDivElement = document.querySelector(".folders-container");
const folderSelector : string = "div.folder";
const foldersLoadMore : HTMLButtonElement = document.querySelector("#folders-load-more");

const filesContainer : HTMLDivElement = document.querySelector(".files-container");
const fileSelector : string = "div.file";
const filesLoadMore : HTMLButtonElement = document.querySelector("#files-load-more");

const showFile : HTMLDivElement = document.querySelector(".show-file");
const editorMenuSelector : string = ".show-file .editor-head .menu";
const editorMenu : HTMLButtonElement = document.querySelector(editorMenuSelector);
const editorClose : HTMLButtonElement = showFile.querySelector(".close");
const editorElement : HTMLDivElement = document.querySelector("#editor");
var editor : any;

const contextMenu : HTMLDivElement = document.querySelector(".context-menu");

const contextMenuContent : HTMLDivElement = document.querySelector("#cm-content");
const contextMenuView : HTMLButtonElement = document.querySelector("#cm-view");
const contextMenuShare : HTMLButtonElement = document.querySelector("#cm-share");
const contextMenuEdit : HTMLButtonElement = document.querySelector("#cm-edit");
const contextMenuInfo : HTMLButtonElement = document.querySelector("#cm-info");
const contextMenuDownload : HTMLButtonElement = document.querySelector("#cm-download");
const contextMenuDelete : HTMLButtonElement = document.querySelector("#cm-delete");

const contextMenuGeneric : HTMLDivElement = document.querySelector("#cm-generic");
const contextMenuAddFiles : HTMLButtonElement = document.querySelector("#cm-add-files");
const contextMenuAddFolder : HTMLButtonElement = document.querySelector("#cm-add-folder");
const contextMenuCreateFile : HTMLButtonElement = document.querySelector("#cm-create-file");
const contextMenuCreateFolder : HTMLButtonElement = document.querySelector("#cm-create-folder");

var contextMenuItem : HTMLElement;

const emptyFolder : HTMLDivElement = document.querySelector(".empty-folder");

const userContentEditInfo : HTMLDivElement = document.querySelector(".user-content .edit-info");

const ApiEndpoint : string = Utilities.GetApiEndpoint();
const CsrfToken : string = Utilities.GetCsrfToken();

const folderLimit = 25;
const fileLimit = 25;

var folderOffset = 0;
var fileOffset = 0;

var orderBy = "name";
var orderDir = "asc";

window.addEventListener("userready", () => {
    [addFiles, contextMenuAddFiles].forEach(element => element.addEventListener("click", () => fileInput.click()));

    [addFolder, contextMenuAddFolder].forEach(element => element.addEventListener("click", () => folderInput.click()));

    [createFile, contextMenuCreateFile].forEach(element => {
        element.addEventListener("click", () => {
            const modal = new Modal({
                "title": createFile.querySelector("p").innerHTML,
                "allow": [
                    "close",
                    "confirm"
                ]
            });
        
            createFileNameInput.querySelector("input").value = "";

            modal.AppendContent([createFileNameInput]);

            modal.OnConfirm = () => CreateUserContent("file", createFileNameInput.querySelector("input").value);

            modal.Show(true);
        });
    });

    [createFolder, contextMenuCreateFolder].forEach(element => {
        element.addEventListener("click", () => {
            const modal = new Modal({
                "title": createFolder.querySelector("p").innerHTML,
                "allow": [
                    "close",
                    "confirm"
                ]
            });
        
            createFolderNameInput.querySelector("input").value = "";

            modal.AppendContent([createFolderNameInput]);

            modal.OnConfirm = () => CreateUserContent("folder", createFolderNameInput.querySelector("input").value);
        
            modal.Show(true);
        });
    });

    searchBar.addEventListener("input", () => {
        
    });

    fileInput.addEventListener("change", (e) => {
        let files : FileList = (<HTMLInputElement>e.target).files;

        UploadFiles(Array.from(files), GetCurrentFolderId());

        fileInput.value = null;
    });

    folderInput.addEventListener("change", (e) => {
        let files : FileList = (<HTMLInputElement>e.target).files;
        let folderName : string = (<any>files[0]).webkitRelativePath.split("/")[0];

        UploadFolder(Array.from(files), folderName, folderName + "/", GetCurrentFolderId(), 0);

        folderInput.value = null;
    });

    contextMenuView.addEventListener("click", () => contextMenuItem.click());

    contextMenuShare.addEventListener("click", () => {
        storage.ref(Auth.UserId + "/" + contextMenuItem.id).getDownloadURL().then((url : any) => {
            if ((<any>navigator).share)
            {
                (<any>navigator).share({
                    title: contextMenuItem.querySelector(".name p"),
                    text: contextMenuItem.querySelector(".name p"),
                    url: url,
                });
            }
            else navigator.clipboard.writeText(url);
        });
    });

    contextMenuEdit.addEventListener("click", () => {
        const modal = new Modal({
            "allow": [
                "close",
                "update"
            ]
        });

        (<HTMLInputElement>userContentEditInfo.querySelector("#name")).value = contextMenuItem.querySelector(".name p").innerHTML;
        modal.AppendContent([userContentEditInfo]);

        modal.UpdateButton.setAttribute("data-id", contextMenuItem.id);

        modal.OnUpdate = () => {
            const id = modal.UpdateButton.getAttribute("data-id");
            const name = (<HTMLInputElement>userContentEditInfo.querySelector("#name")).value;

            const request = new Request(ApiEndpoint, RequestMethod.POST, {
                "object": "file",
                "action": "update",
                "data": {
                    "id": id,
                    "name": name,
                },
                "csrf_token": CsrfToken,
            }, true);

            request.OnSuccess = () => {
                const response = request.GetResponse(true);

                if (response.action.success)
                {
                    modal.Remove();

                    document.getElementById(id).querySelector(".name p").innerHTML = name;
                }
            }

            request.Send();
        }

        modal.Show(true);
    });

    contextMenuInfo.addEventListener("click", () => {
        const modal = new Modal({
            "allow": [
                "close"
            ]
        });

        modal.Show(true);

        db.collection("users/" + Auth.UserId + "/files").doc(contextMenuItem.id).get().then((doc : any) => {
            const name = doc.data().name;

            storage.ref(Auth.UserId + "/" + contextMenuItem.id).getMetadata().then((metadata : any) => {
                modal.AppendContent([
                    new Component("p", {
                        innerHTML: `
                            <span data-translation="api->file->info->id"></span>
                            <span>${metadata.name}</span>
                        `
                    }).element,
                    new Component("p", {
                        innerHTML:  `
                            <span data-translation="api->file->info->name"></span>
                            <span>${name}</span>
                        `
                    }).element,
                    new Component("p", {
                        innerHTML: `
                            <span data-translation="api->file->info->created"></span>
                            <span>${GetDateFromUnixTimestamp(Math.round(Date.parse(metadata.timeCreated) / 1000))}</span>
                        `
                    }).element,
                    new Component("p", {
                        innerHTML: `
                            <span data-translation="api->file->info->last_modified"></span>
                            <span>${GetDateFromUnixTimestamp(Math.round(Date.parse(metadata.updated) / 1000))}</span>
                        `
                    }).element,
                    new Component("p", {
                        innerHTML: `
                            <span data-translation="api->file->info->language"></span>
                            <span>${Linguist.GetDisplayName(<string>Linguist.Detect(name, false))}</span>
                        `
                    }).element,
                    new Component("p", {
                        innerHTML: `
                            <span data-translation="api->file->info->size"></span>
                            <span>${Utilities.FormatStorage(metadata.size)}</span>
                        `
                    }).element,
                    new Component("p", {
                        innerHTML: `
                            <span data-translation="api->file->info->tags"></span>
                            <span>${GetUserContentTags(Linguist.GetTags(metadata.name, true))}</span>
                        `
                    }).element,
                ]);

                Translation.Init(modal.Content)
            });
        });
    });

    contextMenuDownload.addEventListener("click", () => {
        storage.ref(Auth.UserId + "/" + contextMenuItem.id).getDownloadURL().then((url : any) => {
            const element = document.createElement("a");

            element.setAttribute("download", "");
            element.href = url;
            
            element.click();
        });
    });

    contextMenuDelete.addEventListener("click", () => {
        const id = contextMenuItem.id;

        db.collection("users/" + Auth.UserId + "/files").doc(id).delete().then(() => {
            document.getElementById(id).remove();

            Utilities.InsertTranslation(genericMessage.content, "api->messages->file->deleted");

            genericMessage.show();

            if (foldersContainer.innerHTML.trim() === "" && filesContainer.innerHTML.trim() === "")
            {
                Utilities.InsertTranslation(emptyFolder.querySelector("h2"), "api->messages->folder->empty");
                Utilities.ShowElement(emptyFolder, "flex");
            }
        });
    });

    sideMenuToggleButton.addEventListener("click", () => {
        Utilities.ShowElement(sideMenu, "block");

        if (sideMenu.classList.contains("show"))
        {
            Utilities.RemoveClass(sideMenu, "show");
            Utilities.AddClass(sideMenu, "hide");

            if (window.innerWidth > 700)
            {
                main.style.width = "calc(100% - (var(--standard-spacing) * 2))";
            }
        }
        else
        {
            Utilities.RemoveClass(sideMenu, "hide");
            Utilities.AddClass(sideMenu, "show");

            if (window.innerWidth > 700)
            {
                main.style.width = "calc(100% - " + sideMenu.clientWidth + "px - (var(--standard-spacing) * 2))";
            }
        }
    });

    navigationBackButton.addEventListener("click", () => {
        const request = new Request(ApiEndpoint, RequestMethod.POST, {
            "object": "folder",
            "action": "retrieve",
            "data": {
                "id": GetCurrentFolderId(),
            },
            "csrf_token": CsrfToken,
        }, true);

        request.OnSuccess = () => {
            window.dispatchEvent(new Event("pushstate"));

            const response = request.GetResponse(true);

            SetCurrentFolderId(response.parent_folder_id);
                        
            GetUserContent();
        }

        request.Send();
    });

    editorClose.addEventListener("click", () => {
        editorElement.innerHTML = "";

        Utilities.RemoveAllClasses(editorElement, {
            "except": "editor"
        });

        Utilities.RemoveAllAttributes(editorElement, {
            "except": [
                "class",
                "id",
            ]
        });

        showFile.id = "";

        Utilities.HideElement(showFile)

        history.pushState(null, "", GetFolderUrl(GetCurrentFolderId()));
    });

    foldersLoadMore.addEventListener("click", () => {
        folderOffset += folderLimit;

        GetUserContent();
    });

    filesLoadMore.addEventListener("click", () => {
        fileOffset += fileLimit;

        GetUserContent();
    });

    document.addEventListener("click", (e) => {
        if(!isUserContentElement((<HTMLElement>e.target)) && (<HTMLElement>e.target).closest(editorMenuSelector) === null) Utilities.HideElement(contextMenu);
    });

    document.addEventListener("scroll", () => Utilities.HideElement(contextMenu));

    document.addEventListener("contextmenu", (e) => {
        e.preventDefault();

        showContextMenu(e);
    });

    document.addEventListener("drop", (e) => {
        const items : DataTransferItemList = e.dataTransfer.items;

        Array.from(items).map(item => item.webkitGetAsEntry()).forEach((item : any) => {
            if (item.isFile) item.file((file : File) => UploadFile(file, GetCurrentFolderId()));
            else if (item.isDirectory)
            {
                let entries : File[] = [];

                GetFolderEntries(item, item.name + "/", entries);

                UploadFolder(entries, item.name, item.name + "/", GetCurrentFolderId(), 0);
            }
        });
    });

    Utilities.AddClass(header.element, "no-padding-bottom");

    if (window.innerWidth > 700) Utilities.AddClass(sideMenu, "show");

    if (getComputedStyle(sideMenu).getPropertyValue("display") !== "none") sideMenuToggleButton.setAttribute("aria-label", sideMenuToggleButton.getAttribute("data-hide"));
    else sideMenuToggleButton.setAttribute("aria-label", sideMenuToggleButton.getAttribute("data-show"));

    if (location.href.indexOf("file") > -1)
    {
        let id = location.href.substr(location.href.indexOf("file") + 5);

        if (id.indexOf("/") > -1) id = id.substr(0, id.indexOf("/"));

        const request = new Request(ApiEndpoint, RequestMethod.POST, {
            "object": "file",
            "action": "retrieve",
            "data": {
                "id": id,
                "return_type": "info",
            },
            "csrf_token": CsrfToken,
        }, true);

        request.OnSuccess = () => {
            const response = request.GetResponse(true);
                            
            ShowFile(id, response.data.info.name.value, response.data.info.language.value);
        }

        request.Send();
    }

    GetUserContent()

    db.collection("users").doc(Auth.UserId)
        .onSnapshot((doc : any) => document.querySelector("[data-update-field=storage_used]").innerHTML = Utilities.FormatStorage(doc.data().usedStorage));
});

window.addEventListener("resize", () => Utilities.HideElement(contextMenu));

window.addEventListener("popstate", () => {
    let id = location.href.substr(location.href.indexOf("folder") + 7);

    if (id.indexOf("/") > -1) id = id.substr(0, id.indexOf("/"));

    SetCurrentFolderId(id);

    GetUserContent();
});

window.addEventListener("keydown", (e) => {
    if (!e.ctrlKey)
    {
        if (document.querySelector("input:focus") === null && getComputedStyle(showFile).getPropertyValue("display") === "none") searchBar.focus();

        return;
    }

    if (e.key === "o") // Open
    {
        e.preventDefault();

        if (e.shiftKey) addFolder.click(); else addFiles.click();
    }
    else if (e.key === "n") // New
    {
        e.preventDefault();

        if (e.shiftKey) createFolder.click(); else createFile.click();
    }
    else if (e.key === "m") // Menu
    {
        e.preventDefault();

        if (e.shiftKey) header.loggedInMenuToggle.click(); else sideMenuToggleButton.click();
    }
    else if (e.key === "a") // Account (Main account page)
    {
        e.preventDefault();

        (<HTMLAnchorElement>header.loggedInMenuToggle.querySelector("a[data-page=account]")).click();
    }
    else if (e.key === "e") // Exit (Log Out)
    {
        e.preventDefault();

        (<HTMLAnchorElement>header.loggedInMenuToggle.querySelector("a[data-page=logout]")).click();
    }
    else if (e.key === "s") // Settings / Search / Save
    {
            e.preventDefault();

        if (e.shiftKey) (<HTMLAnchorElement>header.loggedInMenuToggle.querySelector("a[data-page=settings]")).click();
        else if (getComputedStyle(showFile).getPropertyValue("display") === "none") searchBar.focus();
        else UploadFile(editor.getValue());
    }
});

const UploadFile = (file : File, parentId : string) : void => {
    db.collection("users/" + Auth.UserId + "/files").add({
        name : file.name,
    }).then((ref : any) => storage.ref(Auth.UserId + "/" + ref.id).put(file).then((snapshot : any) => console.log(snapshot)));
}

const createUserContentElement = (type: string, id: string, name : string, icon_name : string) : string =>
`<div class="` + type + `" id="` + id + `">
    <div class="icon"><i class="` + icon_name + `"></i></div>
    <div class="name"><p>` + name + `</p></div>
    <div class="menu-button"><button aria-label="Open folder menu"><i class="fas fa-ellipsis-v"></i></button></div>
</div>`;

const GetUserContent = () : void => {
    db.collection("users/" + Auth.UserId + "/folders").orderBy(orderBy, orderDir).limit(folderLimit).startAt(folderOffset).get()
        .then((query : any) => {
            Utilities.HideElement(userContentLoadingSpinner);

            query.forEach((doc : any) => filesContainer.insertAdjacentHTML("beforeend", createUserContentElement("folder", doc.id, doc.data().name, "folder")));

            db.collection("users/" + Auth.UserId + "/files").orderBy(orderBy, orderDir).limit(fileLimit).startAt(fileOffset).get()
                .then((query : any) => {
                    query.forEach((doc : any) => filesContainer.insertAdjacentHTML(
                        "beforeend",
                        createUserContentElement("file", doc.id, doc.data().name, Linguist.Get(<string>Linguist.Detect(doc.data().name, false)).iconName))
                    );

                    addUserContentEvents();

                    if (foldersContainer.innerHTML.trim() === "" && filesContainer.innerHTML.trim() === "")
                    {
                        Utilities.InsertTranslation(emptyFolder.querySelector("h2"), "api->messages->folder->empty");
                        Utilities.ShowElement(emptyFolder, "flex");
                    }
                });
        });
}

const showContextMenu = (e : MouseEvent) : void => {
    let contentTarget : HTMLDivElement = (<HTMLElement>e.target).closest(folderSelector + "," + fileSelector);

    if (isUserContentElement(contentTarget) || (<HTMLElement>e.target).closest(editorMenuSelector) !== null)
    {
        Utilities.HideElement(contextMenuGeneric);
        Utilities.ShowElement(contextMenuContent, "block");

        if (isUserContentElement(contentTarget))
        {
            contextMenuItem = contentTarget;
        }
        else
        {
            contextMenuItem = document.getElementById(showFile.id.substr(1));
        }
    }
    else
    {
        Utilities.HideElement(contextMenuContent);
        Utilities.ShowElement(contextMenuGeneric, "block");
    }

    Utilities.ShowElement(contextMenu, "block");

    let top : number = e.pageY - scrollY;
    let left : number = e.pageX;

    if (e.pageX + contextMenu.offsetWidth > window.innerWidth)
    {
        left -= contextMenu.offsetWidth;
    }

    if (e.pageY + contextMenu.offsetHeight - scrollY > window.innerHeight)
    {
        top -= contextMenu.offsetHeight;
    }

    Object.assign(
        contextMenu.style,
        {
            top: top + "px",
            left: left + "px",
        }
    );
}

const addUserContentEvents = () : void => {
    let userContentMenuButtons = (<NodeListOf<HTMLButtonElement>>document.querySelectorAll(folderSelector + " .menu-button button," + fileSelector + " .menu-button button"));
    let userContentElements = (<NodeListOf<HTMLDivElement>>document.querySelectorAll(folderSelector + "," + fileSelector));

    userContentMenuButtons.forEach(element => {
        element.addEventListener("click", (e) => {
            showContextMenu(e);
        });
    });

    editorMenu.addEventListener("click", (e) => {
        showContextMenu(e);
    });

    userContentElements.forEach(element => {
        // Removed before readding to avoid the same event firing multiple times for the same click
        element.removeEventListener("click", HandlePageChangeAndLoadUserContent);
        element.addEventListener("click", HandlePageChangeAndLoadUserContent);
    });
}

const HandlePageChangeAndLoadUserContent = (e : Event) => {
    if ((<HTMLElement>e.target).closest(".menu-button") === null)
    {
        Utilities.DispatchEvent("pushstate");

        let closestFile = (<HTMLElement>e.target).closest(fileSelector);

        if (closestFile === null)
        {
            (<HTMLInputElement>document.querySelector("input[name=folder-id]")).value = (<HTMLElement>e.target).closest(folderSelector).id;

            folderOffset = fileOffset = 0;

            GetUserContent();
        }
        else ShowFile(closestFile.id, closestFile.querySelector(".name p").innerHTML, closestFile.querySelector(".icon i").classList[0]);

        history.pushState(null, "", getUserContentURL((<HTMLElement>e.target).closest(folderSelector + "," + fileSelector)));
    }
}

const isUserContentElement = (element : HTMLElement) : boolean => {
    if (element !== null)
    {
        const contentTarget = element.closest(folderSelector + "," + fileSelector);

        return !(contentTarget === null)
    }
    else return false;
}

const getUserContentURL = (element : HTMLElement) : string => Utilities.GetAppRoot() + element.classList[0] + "/" + element.id;

const GetDateFromUnixTimestamp = (unixTimestamp : number) : string => {
    const date : Date = new Date(unixTimestamp * 1000);
    const options : Object = {
        weekday: undefined,
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "numeric",
        minute: "numeric",
        second: "numeric",
        timeZoneName: "short",
    };

    //undefined will use the browser's default locale
    return date.toLocaleDateString(undefined, options);
}

const GetUserContentTags = (tags : string[]) : string => {
    let tagsContainer = "&thinsp;<div class=\"tag-list\">";

    tags.forEach(tag => tagsContainer += "<div class=\"tag icon\"><i class=" + Linguist.Get(tag).iconName + "></i><span> " + Linguist.GetDisplayName(tag) + "</span></div>");

    tagsContainer += "</div>";

    return tagsContainer;
}

const GetCurrentFolderId = () : string => (<HTMLInputElement>document.querySelector("input[name=folder-id]")).value;

const SetCurrentFolderId = (id : string) : void => {(<HTMLInputElement>document.querySelector("input[name=folder-id]")).value = id;}

const CreateUserContent = (type : string, name : string, id ?: string, params ?: Object) => {
    if (type !== "file" && type !== "folder") return;

    const parentFolderId = (Utilities.IsSet(params) && params.hasOwnProperty("parent_folder_id")) ? (<any>params).parent_folder_id : GetCurrentFolderId();

    let data : any = {
        "name": name,
        "parent_folder_id": parentFolderId
    };

    if (Utilities.IsSet(id)) data.data["id"] = id;

    const request = new Request(ApiEndpoint, RequestMethod.POST, {
        "object": type,
        "action": "create",
        "data": data,
        "csrf_token": CsrfToken,
    }, true);

    request.OnSuccess = (e) => {
        let response = request.GetResponse(true);

        let element;

        if (response.data.parent_folder_id.value === GetCurrentFolderId())
        {
            element = createUserContentElement(type, response.data.id.value, response.data.name.value, response.data.language.value);

            if (type === "file") filesContainer.insertAdjacentHTML("beforeend", element);
            else foldersContainer.insertAdjacentHTML("beforeend", element);

            addUserContentEvents();
        }

        if (Utilities.IsSet(params))
        {
            if (params.hasOwnProperty("callback"))
            {
                let args = {"id": response.data.id.value};

                if (params.hasOwnProperty("callback_args")) Object.assign(args, (<any>params).callback_args);

                (<any>params).callback(args);
            }
        }

        Utilities.HideElement(emptyFolder);

        if (response.data.storage_used !== undefined)
            document.querySelector("[data-update-field=" + response.data.storage_used.field + "]").innerHTML = response.data.storage_used.value;
    }

    const span : HTMLSpanElement = document.createElement("span");

    span.className = "upload-progress-bar";

    const uploadModal = new Modal({
        "subtitle": name,
        "floating": true,
        "animate": false,
    });

    uploadModal.AppendContent([span]);

    uploadModal.Show(true);

    request.OnProgress = (e) => {
        span.style.width = request.Progress + "%";

        if (request.IsComplete(e)) uploadModal.Remove();
    }

    request.Send();
}

const CreateEditor = (name : string, language : string, value : string, id : string) : void => {
    editor = (<any>window).monaco.editor.create(editorElement, {
        value: value,
        language: language,
        theme: "vs-dark",
        automaticLayout: true,
    });

    showFile.querySelector(".name").innerHTML = name;
    showFile.id = "_" + id;

    Utilities.ShowElement(showFile, "block");

    Utilities.RemoveClass(document.documentElement, "wait");
}

const ShowFile = (id : string, name : string, language : string) : void => {
    const request = new Request(ApiEndpoint, RequestMethod.POST, {
        "object": "file",
        "action": "body",
        "data": {
            "id": id,
        },
        "csrf_token": CsrfToken,
    }, true);

    Utilities.AddClass(document.documentElement, "wait");

    request.OnSuccess = () => {
        const response = request.GetResponse(true);

        CreateEditor(name, language, response.data.body.value, id);
    }

    request.Send();
}

const GetFolderUrl = (id : string) : string => {
    (<HTMLInputElement>document.querySelector("input[name=folder-id]")).value = id;

    return id !== "root" ? Utilities.GetAppRoot() + "folder/" + id : Utilities.GetAppRoot() + "account";
}

const UploadFolder = (files : File[], name : string, path : string, parentId : string, depth : number) : void => {
    CreateUserContent("folder", name, null, {
        "parent_folder_id": parentId,
        "callback_args": {"files": files},
        "callback": (args : any) => {
            let folders : Set<string> = new Set();

            depth++;

            args.files.forEach((file : File) => {
                if (depth < (<any>file).webkitRelativePath.split("/").length - 1) folders.add((<any>file).webkitRelativePath.split("/")[depth]);
            });

            Array
                .from(folders)
                .filter(folder => folder.length > 0)
                .forEach(folder => UploadFolder(args.files.filter((file : File) =>
                    (<any>file).webkitRelativePath.indexOf(path + folder + "/") === 0), folder, path + folder + "/", args.id, depth
                ));

            UploadFiles(args.files.filter((file : File) => (<any>file).webkitRelativePath.substr(path.length) === file.name), args.id);
        }
    });
}

const UploadFiles = (files : File[], parentId : string) : void => files.forEach(file => UploadFile(file, parentId));

const GetFolderEntries = (folder : DataTransferItem, path : string, entries : File[]) : File[] => {
    var dirReader = (<any>folder).createReader();

    const ReadEntries = () => {
        dirReader.readEntries((items : any) => {
            if (items.length)
            {
                ReadEntries();

                items.forEach((entry : any) => {
                    if (entry.isDirectory) GetFolderEntries(entry, path + entry.name + "/", entries);
                    else if (entry.isFile)
                    {
                        entry.file((file : File) => {
                            // Allow overwriting the webkitRelativePath property that by default is readonly
                            Object.defineProperties(file, {
                                "webkitRelativePath": {
                                    "writable": true,
                                },
                            });

                            Object.assign(file, {
                                "webkitRelativePath": path + entry.name,
                            });

                            entries.push(file);
                        });
                    }
                });
            }
        });
    }

    ReadEntries();

    return entries;
}