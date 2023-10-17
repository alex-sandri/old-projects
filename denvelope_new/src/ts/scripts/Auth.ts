import { Utilities } from "./utilities.js";
import { userEmail, userName, userPhoto, providerLogo, menuImgContainer } from "./header.js";

export class Auth
{
    private static readonly auth = (<any>window).firebase.auth();

    public static SignOut = () : void => Auth.auth.signOut();

    public static Init = () : void =>
    {
        Auth.auth.useDeviceLanguage();

        Auth.auth.onAuthStateChanged((user : any) => Auth.AuthStateChanged(user));

        if (Utilities.IsSet(document.querySelector("#firebaseui-auth-container")))
        {
            const script = document.createElement("script");

            script.src = `https://www.gstatic.com/firebasejs/ui/4.3.0/firebase-ui-auth__${navigator.language.substr(0, 2)}.js`;
            
            script.onload = () => {
                const uiConfig = {
                    signInSuccessUrl: location.href + "/account",
                    signInOptions: [
                        {
                            provider: (<any>window).firebase.auth.GoogleAuthProvider.PROVIDER_ID,
                            authMethod: "https://accounts.google.com",
                            clientId: "1023448327269-h54u9u95f2cqs7m1bceqh9h0p1dskcmk.apps.googleusercontent.com",
                        },
                        //(<any>window).firebase.auth.FacebookAuthProvider.PROVIDER_ID,
                        //(<any>window).firebase.auth.TwitterAuthProvider.PROVIDER_ID,
                        //(<any>window).firebase.auth.GithubAuthProvider.PROVIDER_ID,
                        //"yahoo.com",
                        //"microsoft.com",
                        //"apple.com",
                    ],
                    credentialHelper: (<any>window).firebaseui.auth.CredentialHelper.GOOGLE_YOLO,
                    tosUrl: "terms",
                    privacyPolicyUrl: () => window.location.assign("privacy")
                };

                const ui = new (<any>window).firebaseui.auth.AuthUI(Auth.auth);

                ui.disableAutoSignIn();

                ui.start("#firebaseui-auth-container", uiConfig);
            }

            document.body.append(script);
        }
    }
    
    public static get UserId () : string {return Auth.auth.currentUser.uid;}

    private static AuthStateChanged = (user : any) : void =>
    {
        if (user)
        {
            Utilities.RemoveClass(document.documentElement, "logged-out");
            Utilities.AddClass(document.documentElement, "logged-in");

            if (user.photoURL) userPhoto.src = user.photoURL;
            if (user.displayName) userName.innerHTML = user.displayName;

            const providerId = user.providerData[0].providerId;

            // If the provider id is equal to password the user has logged in by email and password
            if (providerId !== "password") providerLogo.src = `assets/img/${providerId.split(".")[0]}.svg`;

            if (providerId === "password" && !user.displayName) Utilities.HideElement(menuImgContainer);

            userEmail.innerHTML = user.email;

            Utilities.SetCookie("is_logged_in", "true", 1);

            if (location.href.indexOf("account") === -1) location.href = "account";

            Utilities.DispatchEvent("userready");
        }
        else
        {
            Utilities.RemoveClass(document.documentElement, "logged-in");
            Utilities.AddClass(document.documentElement, "logged-out");

            Utilities.SetCookie("is_logged_in", "false", 1);

            if (location.href !== document.querySelector("base").href) location.href = document.querySelector("base").href;
        }
    }
}