<?php
    function getChangelog($ver){
        $changelogs = array(
            /*
            "2.1.1" => [
                "Added Changelogs",
                "Minor bug fixes",
            ],
            "2.1.2" => [
                "Now the last modified timestamp updates as soon as the rename action is confirmed",
                "User last activity now updates after file or folder rename",
                "Added styling to the latest changelog list",
                "Minor under the hood changes",
            ],
            "2.1.3" => [
                "Solved bug that prevented the file or folder from being renamed",
            ],
            "2.1.4" => [
                "Minor bug fixes",
            ],
            "2.1.5" => [
                "Minor bug fixes",
            ],
            "2.1.6" => [
                "Optimized context menu style that prevented some of its buttons from being saw if the screen height wasn't enough",
                "Solved an issue where you weren't able to interact with files in a shared folder",
                "Changes under the hood",
            ],
            "2.1.7" => [
                "Solved an issue with files with the same name where the dot between the extension and the rest of the file was duplicated",
                "Solved another issue where the file or folder name wouldn't update with the correct one after the rename was confirmed",
            ],
            "2.1.8" => [
                "Minor bug fixes",
            ],
            "2.1.9" => [
                "Minor bug fixes",
            ],
            "2.1.10" => [
                "Minor bug fixes",
            ],
            "2.1.11" => [
                "Minor bug fixes",
            ],
            "2.1.12" => [
                "Minor bug fixes",
            ],
            "2.1.13" => [
                "Minor bug fixes",
            ],
            "2.1.14" => [
                "Added feature description to the home page",
            ],
            "2.1.15" => [
                "Fixed some major issues when renaming files or folders you were no longer able to access them",
            ],
            "2.1.16" => [
                "Minor bug fixes when renaming folders",
            ],
            "2.1.17" => [
                "Minor bug fixes when renaming folders",
            ],
            "2.1.18" => [
                "Minor bug fixes when renaming folders",
            ],
            "2.1.19" => [
                "Minor bug fixes when renaming folders",
            ],
            "2.1.20" => [
                "Now on shared folders when opening a file, in the tab title, the name of the file is displayed rather than the folder's one",
            ],
            "2.1.21" => [
                "Changed plan names",
                "Minor bug fixes",
                "Now available to the public",
            ],
            "2.1.22" => [
                "Added BETA label in the header",
            ],
            "2.1.23" => [
                "Minor bug fixes",
            ],
            "2.1.24" => [
                "Minor bug fixes",
            ],
            "2.1.25" => [
                "Minor bug fixes",
            ],
            "2.1.26" => [
                "Minor bug fixes",
            ],
            "2.1.27" => [
                "Fixed some translation issues",
            ],
            "2.1.28" => [
                "Minor bug fixes",
            ],
            "2.1.29" => [
                "Minor bug fixes",
            ],
            "2.1.30" => [
                "Minor bug fixes",
            ],
            "2.1.31" => [
                "Minor bug fixes",
            ],
            "2.1.32" => [
                "Minor bug fixes",
            ],
            "2.1.33" => [
                "Minor bug fixes",
            ],
            "2.1.34" => [
                "Minor bug fixes",
            ],
            "2.1.35" => [
                "Fixed an issue that prevented a user from signing up",
                "Minor bug fixes",
            ],
            "2.1.36" => [
                "Minor bug fixes",
            ],
            "2.1.37" => [
                "Minor bug fixes",
            ],
            "2.1.38" => [
                "Minor bug fixes",
            ],
            "2.1.39" => [
                "Added Google Play and App Store badges to download the Authy app more easily",
            ],
            "2.1.40" => [
                "Solved XSS vulnerability",
            ],
            "2.1.41" => [
                "Minor bug fixes",
            ],
            "2.1.42" => [
                "Minor bug fixes",
            ],
            "2.1.43" => [
                "Minor bug fixes",
            ],
            "2.1.44" => [
                "Minor bug fixes",
            ],
            "2.1.45" => [
                "Minor bug fixes",
            ],
            "2.1.46" => [
                "Minor bug fixes",
            ],
            "2.1.47" => [
                "Minor bug fixes",
            ],
            "2.1.48" => [
                "Minor bug fixes",
            ],
            "2.1.49" => [
                "Minor bug fixes",
            ],
            "2.1.50" => [
                "Minor bug fixes",
            ],
            "2.1.51" => [
                "Minor bug fixes",
            ],
            "2.1.52" => [
                "Minor bug fixes",
            ],
            "2.1.53" => [
                "Added resend email banner, if the account is not activated, also on the login page at https://denvelope.com/login",
                "Minor bug fixes",
            ],
            "2.1.54" => [
                "Minor bug fixes",
            ],
            "2.1.55" => [
                "Minor bug fixes",
            ],
            "2.1.56" => [
                "Minor bug fixes",
            ],
            "2.1.57" => [
                "Minor bug fixes",
            ],
            "2.1.58" => [
                "Minor bug fixes",
            ],
            "2.1.59" => [
                "Minor bug fixes",
            ],
            "2.1.60" => [
                "Minor bug fixes",
            ],
            "2.1.61" => [
                "Minor bug fixes",
            ],
            "2.1.62" => [
                "Minor bug fixes",
            ],
            "2.1.63" => [
                "Minor bug fixes",
                "<strong>UNSTABLE FEATURE:</strong> Added file drag and drop (note: only one file right now, try at your own risk)",
            ],
            "2.1.64" => [
                "Minor bug fixes",
            ],
            "2.1.65" => [
                "Minor bug fixes",
            ],
            "2.1.66" => [
                "Minor bug fixes",
            ],
            "2.2.0" => [
                "<strong>UNSTABLE FEATURE:</strong> Added file drag and drop (note: only for files, so no folders, try at your own risk)",
            ],
            "2.2.1" => [
                "<strong>UNSTABLE FEATURE:</strong> Added file and folder drag and drop (try at your own risk)",
            ],
            "2.2.2" => [
                "Minor bug fixes",
                "<strong>UNSTABLE FEATURE:</strong> Added file and folder drag and drop (try at your own risk)",
            ],
            "2.2.3" => [
                "Minor bug fixes",
                "<strong>UNSTABLE FEATURE:</strong> Added file and folder drag and drop (try at your own risk)",
            ],
            "2.2.4" => [
                "Minor bug fixes",
                "<strong>UNSTABLE FEATURE:</strong> Added file and folder drag and drop",
            ],
            "2.2.5" => [
                "Minor bug fixes",
                "Added files drag and drop",
                "Removed folder drag and drop (too unstable)",
            ],
            "2.2.6" => [
                "Minor bug fixes",
            ],
            "2.2.7" => [
                "Minor bug fixes",
            ],
            "2.2.8" => [
                "Minor bug fixes",
            ],
            "2.2.9" => [
                "Minor bug fixes",
            ],
            "2.3.0" => [
                "Now you can also upload folders by dragging and dropping them",
                "Minor bug fixes",
            ],
            "2.3.1" => [
                "Minor bug fixes",
            ],
            "2.3.2" => [
                "Minor bug fixes",
                "<strong>UNSTABLE FEATURE:</strong> Added file and folder drag and drop (try at your own risk)",
            ],
            "2.3.3" => [
                "Minor bug fixes",
                "<strong>UNSTABLE FEATURE:</strong> Added file and folder drag and drop (try at your own risk)",
            ],
            "2.3.4" => [
                "Minor bug fixes",
                "<strong>UNSTABLE FEATURE:</strong> Added file and folder drag and drop (try at your own risk)",
            ],
            "2.3.5" => [
                "Minor bug fixes",
            ],
            "2.3.6" => [
                "Minor changes under the hood",
            ],
            "2.3.7" => [
                "Minor bug fixes",
            ],
            "2.3.8" => [
                "Minor bug fixes",
            ],
            "2.3.9" => [
                "Minor bug fixes",
            ],
            "2.3.10" => [
                "Major bug fix that prevented from changing plan",
            ],
            "2.3.11" => [
                "Major bug fix that prevented from changing plan",
            ],
            "2.3.12" => [
                "Major bug fix that prevented from changing plan",
            ],
            "2.3.13" => [
                "Minor bug fixes",
            ],
            "2.4.0" => [
                "Added option to cancel an upload (right now only for folders uploaded through drag and drop)",
            ],
            "2.5.0" => [
                "Added option to cancel an upload (right now only for files or folders uploaded through drag and drop)",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again.<br>We apologize for the inconvenience."
            ],
            "2.5.1" => [
                "Minor bug fixes",
                "Added option to cancel an upload (right now only for files or folders uploaded through drag and drop)",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.5.2" => [
                "Minor bug fixes",
                "Added option to cancel an upload (right now only for files or folders uploaded through drag and drop)",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.5.3" => [
                "Minor bug fixes",
                "Added option to cancel an upload (right now only for files or folders uploaded through drag and drop)",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.5.4" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.5.5" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.5.6" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.5.7" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.5.8" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.5.9" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.5.10" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.5.11" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.5.12" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.0" => [
                "Minor bug fixes",
                "Improved page loading performance",
                "Added Support section in the settings to reply back to our support team and to see all open support cases",
                "Minor changes under the hood",
                "Minor changes to prepare for future feature release",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.1" => [
                "Minor bug fixes",
                "Minor changes to prepare for future feature release",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.2" => [
                "Minor bug fixes",
                "Minor changes to prepare for future feature release",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.3" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.4" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.5" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.6" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.7" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.8" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.9" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.10" => [
                "Minor bug fixes",
                "Improved security",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.11" => [
                "Minor bug fixes",
                "Improved security",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.12" => [
                "Minor bug fixes",
                "Improved security",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.13" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.14" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.15" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.16" => [
                "Minor bug fixes",
                "Minor changes under the hood",
                "Minor changes to prepare for future feature release",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.17" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.18" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.6.19" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.7.0" => [
                "Minor bug fixes",
                "Minor changes to improve the user experience",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.7.1" => [
                "Minor bug fixes",
                "Minor changes to improve the user experience",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.7.2" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.7.3" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.7.4" => [
                "Minor bug fixes",
                "<strong>IMPORTANT:</strong> while we are working on solving this problem please drag and drop items only once, then wait until it has finished uploading (or until you cancel it) before uploading again. We apologize for the inconvenience."
            ],
            "2.7.5" => [
                "Minor bug fixes",
                "<strong>WE ARE CURRENTLY WORKING ON V3, A MAJOR UPDATE WITH PERFORMANCE AND PRIVACY IN MIND.</strong>"
            ],
            "2.7.6" => [
                "Minor bug fixes",
                "<strong>WE ARE CURRENTLY WORKING ON V3, A MAJOR UPDATE WITH PERFORMANCE AND PRIVACY IN MIND.</strong>"
            ],
            "2.7.7" => [
                "Minor bug fixes",
                "<strong>WE ARE CURRENTLY WORKING ON V3, A MAJOR UPDATE WITH PERFORMANCE AND PRIVACY IN MIND.</strong>"
            ],
            "2.7.8" => [
                "Minor bug fixes",
                "<strong>WE ARE CURRENTLY WORKING ON V3, A MAJOR UPDATE WITH PERFORMANCE AND PRIVACY IN MIND.</strong>"
            ],
            "2.7.9" => [
                "Fixed a translation issue",
                "Plan upgrades are now (temporarily) disabled",
                "<strong>WE ARE CURRENTLY WORKING ON V3, A MAJOR UPDATE WITH PERFORMANCE AND PRIVACY IN MIND. (First roll-out is to be expected in Feb 2020)</strong>"
            ],
            "2.7.10" => [
                "Fixed a translation issue",
                "Plan upgrades and 2FA are now disabled",
                "<strong>WE ARE CURRENTLY WORKING ON V3, A MAJOR UPDATE WITH PERFORMANCE AND PRIVACY IN MIND. (First roll-out is to be expected in Jan 2020)</strong>"
            ],
            "2.7.11" => [
                "Minor bug fixes",
                "Plan upgrades and 2FA are now disabled",
                "<strong>WE ARE CURRENTLY WORKING ON V3, A MAJOR UPDATE WITH PERFORMANCE AND PRIVACY IN MIND. (First roll-out is to be expected in Jan 2020)</strong>"
            ],
            "2.7.12" => [
                "Minor bug fixes",
                "Plan upgrades and 2FA are now disabled",
                "<strong>WE ARE CURRENTLY WORKING ON V3, A MAJOR UPDATE WITH PERFORMANCE AND PRIVACY IN MIND. (First roll-out is to be expected in Jan 2020)</strong>"
            ],
            "2.7.13" => [
                "Minor changes",
            ],
            "2.7.14" => [
                "Minor changes",
            ],
            "2.7.15" => [
                "Minor changes",
            ],
            "2.7.16" => [
                "Minor changes",
            ],
            "2.7.17" => [
                "Minor changes",
            ],
            "2.7.18" => [
                "Minor changes",
            ],
            "2.7.19" => [
                "Minor changes",
            ],
            "2.7.20" => [
                "Minor changes",
            ],
            "2.7.21" => [
                "Minor changes",
            ],
            "2.7.22" => [
                "Minor changes",
            ],
            "2.7.23" => [
                "Minor changes",
            ],
            "2.7.24" => [
                "Minor changes",
            ],
            "2.7.25" => [
                "Minor changes",
            ],
            "2.7.26" => [
                "Minor changes",
            ],
            "2.7.27" => [
                "Minor changes",
            ],
            */
            "2.7.28" => [
                "Minor changes",
            ],
        );

        return $changelogs[$ver];
    }
?>