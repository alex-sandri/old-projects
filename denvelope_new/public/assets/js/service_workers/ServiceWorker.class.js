import { Request, RequestMethod } from '../scripts/ajax.js';
export class ServiceWorker {
    constructor(root) {
        if (this.isSupported()) {
            this.register(root);
        }
    }
    isSupported() {
        return "serviceWorker" in navigator;
    }
    register(root) {
        window.addEventListener("load", () => {
            navigator.serviceWorker.register(root + "sw.js");
            navigator.serviceWorker.ready.then(reg => {
                return reg.sync.register("send-form-data");
            });
        });
        let deferredPrompt;
        let installPWABanner = document.querySelector(".pwa-banner");
        self.addEventListener("beforeinstallprompt", (e) => {
            e.preventDefault();
            deferredPrompt = e;
            if (installPWABanner) {
                installPWABanner.style.display = "flex";
            }
        });
        if (installPWABanner) {
            installPWABanner.querySelector(".install-pwa").addEventListener("click", () => {
                installPWABanner.style.display = "none";
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    deferredPrompt = null;
                });
            });
            installPWABanner.querySelector(".dismiss-banner").addEventListener("click", () => {
                installPWABanner.style.display = "none";
                const request = new Request(document.querySelector("input[name=api-endpoint]").value + "cookie/set/pwa_dismissed_banner/", RequestMethod.POST, {
                    "data": {
                        "csrf_token": document.querySelector("input[name=csrf-token]").value
                    }
                }, true);
                request.Send();
            });
        }
    }
}
//# sourceMappingURL=ServiceWorker.class.js.map