import { Request, RequestMethod } from '../scripts/ajax.js';

export class ServiceWorker {
    constructor (root : string) {
        if(this.isSupported()){
            this.register(root)
        }
    }

    isSupported() {
        //console.log("Service Worker: Supported");
        return "serviceWorker" in navigator;
    }

    register(root : string) {
        window.addEventListener("load", () => {
            navigator.serviceWorker.register(root + "sw.js")/*.then(reg => {
                console.log("Service Worker: Registered");
            }).catch(err => {
                console.log("Service Worker: Error: " + err);
            })*/;

            navigator.serviceWorker.ready.then(reg => {
                //console.log("Service Worker: Ready");
                return reg.sync.register("send-form-data"); //This value must be the same as the one specified on the service worker sync event listener
            })/*.catch(err => {
                console.log("Service Worker: Sync Registration Failed");
            })*/;
        });

        /*********************
         *  PWA Install Banner
        *********************/

        let deferredPrompt : any;
        let installPWABanner : HTMLDivElement = document.querySelector(".pwa-banner");

        self.addEventListener("beforeinstallprompt", (e) => {
            e.preventDefault();

            deferredPrompt = e;

            if (installPWABanner)
            {
                installPWABanner.style.display = "flex"; 
            }
        });

        if(installPWABanner){ // Check if the PWA banner element is actually in the DOM
            installPWABanner.querySelector(".install-pwa").addEventListener("click", () => {
                installPWABanner.style.display = "none";

                deferredPrompt.prompt();

                deferredPrompt.userChoice.then((choiceResult : any) => {
                    /*
                    if(choiceResult.outcome === "accepted"){
                        console.log("User accepted the A2HS prompt");
                    }
                    else{
                        console.log("User dismissed the A2HS prompt");
                    }
                    */
                    deferredPrompt = null;
                });
            });

            installPWABanner.querySelector(".dismiss-banner").addEventListener("click", () => {
                installPWABanner.style.display = "none";

                const request = new Request((<HTMLInputElement>document.querySelector("input[name=api-endpoint]")).value + "cookie/set/pwa_dismissed_banner/", RequestMethod.POST, {
                    "data": {
                        "csrf_token": (<HTMLInputElement>document.querySelector("input[name=csrf-token]")).value
                    }
                }, true);
            
                request.Send();
            });
        }
    }
}