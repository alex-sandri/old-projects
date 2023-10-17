export {};

const cacheName : string = "cache";

/*
var db : IDBDatabase;
const dbName : string = "db";
const dbVersion : number = 1;

var messageFormData : Object;
*/

/*
self.addEventListener("install", () => {
    console.log("Service Worker: Installed");
});
*/

self.addEventListener("activate", (e : any) => {
    //console.log("Service Worker: Activated");
    e.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cache => {
                    if(cache !== cacheName){
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
});

self.addEventListener("fetch", (e : any) => {
    //console.log("Service Worker: Fetching");
    if(e.request.method === "GET"){
        e.respondWith(
            fetch(e.request).then(res => {
                const resClone = res.clone();
    
                caches.open(cacheName).then(cache => {
                    cache.put(e.request, resClone);
                });
    
                return res;
            }).catch(err => caches.match(e.request).then(res => res))
        );
    }
    /*
    else if(e.request.method === "POST"){
        //console.log("Service Worker: Sending Form Data");
        e.respondWith(
            fetch(e.request).then(res => {
                //console.log("Service Worker: Fetching POST Request");
                return res;
            }).catch(err => {
                //console.log("Service Worker: Error Fetching POST Request");
                savePostRequest(e.request.url, messageFormData);

                return err;
            })
        );
    }
    */
});

/*
self.addEventListener("message", (e) => {
    //console.log("Service Worker: Message");
    if(e.data.hasOwnProperty("form-data")){
        //console.log("Service Worker: Received Form Data Message");
        messageFormData = e.data['form-data'];
    }
});

self.addEventListener("sync", (e : any) => {
    //console.log("Service Worker: Online");  
    if(e.tag === "send-form-data"){ //This tag must be the same as the one specified during the service worker registration
        e.waitUntil(
            sendPOSTRequests()
        );
    }  
});

function openDatabase(){
    if(!("indexedDB" in self)){
        //console.log("IndexedDB: Not Supported");
        return;
    }

    let request : IDBOpenDBRequest = self.indexedDB.open(dbName, dbVersion);

    request.onerror = (e : any) => {
        //console.log("IndexedDB: Error: " + e.target.errorCode);
        return;
    }

    request.onupgradeneeded = (e) => {
        db = (<IDBRequest>e.target).result;
        let objectStore = db.createObjectStore(dbName, {
            autoIncrement: true,
            keyPath: "id",
        });

        
        //objectStore.transaction.oncomplete = (e) => {
        //    console.log("IndexedDB: Completed Transaction");
        //}
    }

    request.onsuccess = (e) => {
        db = (<IDBRequest>e.target).result;
    }
}

function getObjectStore(name : string, mode : IDBTransactionMode){
    return db.transaction(name, mode).objectStore(name);
}

function savePostRequest(url : string, payload : Object){
    let request = getObjectStore(dbName, "readwrite").add({
        url: url,
        payload: payload,
        method: "POST",
    });

    request.onerror = (e : any) => {
        //console.log("IndexedDB: Error: " + e.target.errorCode);
        return;
    }

    //request.onsuccess = (e : any) => {
    //    console.log("IndexedDB: New POST Request Added");
    //}
}

function sendPOSTRequests(){
    let savedRequests : Array<any> = [];
    let request : IDBRequest = getObjectStore(dbName, "readonly").openCursor();

    request.onsuccess = async (e : any) => {
        let cursor : IDBCursorWithValue = e.target.cursor;

        if(cursor){
            savedRequests.push(cursor.value);
            cursor.continue();
        }
        else{
            for(let savedRequest of savedRequests){
                console.log("Service Worker: Sending POST Request");
                let requestURL = savedRequest.url;
                let payload = savedRequest.payload;
                let method = savedRequest.method;
                let headers = {

                };

                fetch(
                    requestURL,
                    {
                        headers: headers,
                        method: method,
                        body: payload,
                    }
                ).then(res => {
                    console.log("Service Worker: Received Server Response");
                    if(res.status === 200){
                        getObjectStore(dbName, "readwrite").delete(savedRequest.id);
                    }
                }).catch(err => {
                    //console.log("Service Worker: Error: " + err);
                    throw err;
                });
            }
        }
    }
}

openDatabase();

*/