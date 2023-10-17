export class Request {
    constructor(url, method, data, encode) {
        this.url = url;
        this.method = method;
        this.data = data;
        this.encode = encode;
        this.Send = () => {
            this.xhr = new XMLHttpRequest();
            this.xhr.onreadystatechange = (e) => {
                if (this.Success())
                    this.OnSuccess.call(this.xhr, e);
                this.OnReadyStateChange.call(this.xhr, e);
            };
            this.xhr.onprogress = (e) => {
                this.progress = (e.loaded / e.total) * 100;
                this.OnProgress.call(this.xhr, e);
            };
            this.xhr.onload = this.OnLoad;
            this.xhr.onerror = this.OnError;
            this.xhr.onabort = this.OnAbort;
            this.xhr.open(RequestMethod[this.method], this.url);
            if (this.data !== null)
                this.xhr.send(this.CreateFormData(this.data, this.encode));
            else
                this.xhr.send();
        };
        this.Abort = () => this.xhr.abort();
        this.Success = () => this.xhr.readyState === XMLHttpRequest.DONE && this.xhr.status === 200;
        this.IsComplete = (e) => this.Progress === 100;
        this.GetResponse = (parse) => (parse !== null && parse) ? JSON.parse(this.xhr.response) : this.xhr.response;
        this.CreateFormData = (data, encode) => {
            let fd = new FormData;
            if (encode)
                fd.append("data", JSON.stringify(data));
            else
                for (let i = 0; i < Object.keys(data).length; i++)
                    fd.append(Object.keys(data)[i], Object.values(data)[i]);
            return fd;
        };
        this.progress = 0;
        this.OnReadyStateChange = this.OnSuccess = this.OnProgress = this.OnLoad = this.OnError = this.OnAbort = () => { };
    }
    get Progress() { return this.progress; }
}
export class SyncRequest extends Request {
    constructor(url, method, data, encode, timeout, abortEvent) {
        super(url, method, data, encode);
        this.url = url;
        this.method = method;
        this.data = data;
        this.encode = encode;
        this.timeout = timeout;
        this.abortEvent = abortEvent;
        this.Abort = () => clearInterval(this.interval);
        this.interval = setInterval(() => {
            this.Send();
        }, timeout);
        window.addEventListener("popstate", () => this.Abort());
        window.addEventListener(abortEvent, () => this.Abort());
    }
}
export var RequestMethod;
(function (RequestMethod) {
    RequestMethod[RequestMethod["GET"] = 0] = "GET";
    RequestMethod[RequestMethod["HEAD"] = 1] = "HEAD";
    RequestMethod[RequestMethod["POST"] = 2] = "POST";
    RequestMethod[RequestMethod["PUT"] = 3] = "PUT";
    RequestMethod[RequestMethod["DELETE"] = 4] = "DELETE";
    RequestMethod[RequestMethod["CONNECT"] = 5] = "CONNECT";
    RequestMethod[RequestMethod["OPTIONS"] = 6] = "OPTIONS";
    RequestMethod[RequestMethod["TRACE"] = 7] = "TRACE";
    RequestMethod[RequestMethod["PATCH"] = 8] = "PATCH";
})(RequestMethod || (RequestMethod = {}));
//# sourceMappingURL=ajax.js.map