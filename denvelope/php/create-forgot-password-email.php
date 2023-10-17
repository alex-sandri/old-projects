<?php
    function createForgotPasswordEmail($selector, $validator){
        $email = '<html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                    <meta http-equiv="X-UA-Compatible" content="ie=edge">
                    <title>Reset Password</title>
                    <link rel="shortcut icon" href="https://denvelope.com/img/favicon.ico" type="image/x-icon">
                    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,900" rel="stylesheet">
                </head>
                <body style="background-color: #000411; color: #EFCB68; width: 100%; margin: 0; padding: 0; font-family: \'Montserrat\', \'Arial\', sans-serif;">
                    <table style="width: 600px; margin: 0 auto; text-align: center; background-color: #160C28; border-radius: 20px; padding: 20px; box-shadow: 0px 0px 40px 20px #EFCB68; margin-top: 5%; margin-bottom: 5%; border: 5px solid #EFCB68; max-width: calc(100% - 20px - 5px);">
                        <td>
                            <img src="https://denvelope.com/img/extended-logo-alt.png" alt="Denvelope Logo" width="600px" style="max-width: 100%;">
                            <h1 style="font-weight: 900; font-size: 45px;">Reset your password</h1>
                            <h2 style="font-weight: 700;">We received a password reset request for your account</h2>
                            <h3 style="font-weight: 400;">Click this button to reset your password:</h3>
                            <br>
                            <a href="https://denvelope.com/reset/?s='.$selector.'&v='.$validator.'" style="color: #160C28; font-weight: 700; background-color: #EFCB68; padding: 15px; font-size: 20px; text-decoration: none;">Reset Password</a>
                            <br>
                            <br>
                            <h4>If you have any question feel free to shoot us an email at:</h4>
                            <a href="mailto:support@denvelope.com" style="color: #EFCB68; font-size: 20px;">support@denvelope.com</a>
                            <h4>or if you prefer to use the contact form on our website:</h4>
                            <br>
                            <a href="https://denvelope.com/contact" style="color: #160C28; font-weight: 700; background-color: #EFCB68; padding: 15px; font-size: 20px; text-decoration: none;">Contact Us</a>
                            <br>
                            <br>
                            <h5>If you didn\'t request a password reset ignore this email and the password will not change</h5>
                            <br>
                            <h3 style="text-align: left;"><span style="font-size: 20px;">All the best,</span><br><span style="font-size: 25px;">The Denvelope Team</span></h3>
                        </td>
                    </table>
                </body>
                </html>
        ';

        return $email;
    }
?>