export class Request
{
    private xhr : XMLHttpRequest;

    private progress : number;

    public OnReadyStateChange : (this : XMLHttpRequest, e : Event) => any;
    public OnSuccess : (this : XMLHttpRequest, e : Event) => any;
    public OnProgress : (this : XMLHttpRequest, e : ProgressEvent<EventTarget>) => any;
    public OnLoad : (this : XMLHttpRequest, e : ProgressEvent<EventTarget>) => any;
    public OnError : (this : XMLHttpRequest, e : ProgressEvent<EventTarget>) => any;
    public OnAbort : (this : XMLHttpRequest, e : ProgressEvent<EventTarget>) => any;

    constructor (protected url : string, protected method : RequestMethod, protected data : Object, protected encode : boolean)
    {
        this.progress = 0;

        this.OnReadyStateChange = this.OnSuccess = this.OnProgress = this.OnLoad = this.OnError = this.OnAbort = () => {};
    }

    public Send = () : void => {
        this.xhr = new XMLHttpRequest();

        this.xhr.onreadystatechange = (e) => {
            if (this.Success()) this.OnSuccess.call(this.xhr, e);

            this.OnReadyStateChange.call(this.xhr, e);
        }

        this.xhr.onprogress = (e) => {
            this.progress = (e.loaded / e.total) * 100;

            this.OnProgress.call(this.xhr, e);
        }

        this.xhr.onload = this.OnLoad;
        this.xhr.onerror = this.OnError;
        this.xhr.onabort = this.OnAbort;

        this.xhr.open(RequestMethod[this.method], this.url);

        if (this.data !== null) this.xhr.send(this.CreateFormData(this.data, this.encode));
        else this.xhr.send();
    }

    public Abort = () : void => this.xhr.abort();

    public Success = () : boolean => this.xhr.readyState === XMLHttpRequest.DONE && this.xhr.status === 200;

    public get Progress () : number {return this.progress}

    public IsComplete = (e : ProgressEvent<EventTarget>) : boolean => this.Progress === 100;

    public GetResponse = (parse ?: boolean) : any => (parse !== null && parse) ? JSON.parse(this.xhr.response) : this.xhr.response;

    private CreateFormData = (data : Object, encode : boolean) : FormData =>
    {
        let fd : FormData = new FormData;

        if (encode) fd.append("data", JSON.stringify(data));
        else for (let i = 0; i < Object.keys(data).length; i++) fd.append(Object.keys(data)[i], Object.values(data)[i]);

        return fd;
    }
}

/**
 * @class Used to send multiple requests in the background at a specific interval
 */
export class SyncRequest extends Request
{
    private interval : number;

    constructor (protected url : string, protected method : RequestMethod, protected data : Object, protected encode : boolean, private timeout : number, private abortEvent : string)
    {
        super(url, method, data, encode);

        this.interval = setInterval(() => {
            this.Send();
        }, timeout);

        window.addEventListener("popstate", () => this.Abort());
        window.addEventListener(abortEvent, () => this.Abort());
    }

    public Abort = () : void => clearInterval(this.interval);
}

export enum RequestMethod
{
    GET,
    HEAD,
    POST,
    PUT,
    DELETE,
    CONNECT,
    OPTIONS,
    TRACE,
    PATCH,
}