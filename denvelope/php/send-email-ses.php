<?php
    require_once("../vendor/autoload.php");

    use Aws\Ses\SesClient;
    use Aws\Exception\AwsException;

    function sendEmailSES($to, $subject, $plaintext_body, $html_body){
        require("global-vars.php");

        $SesClient = new SesClient([
            'version' => '2010-12-01',
            'region'  => 'us-east-1',
            'credentials' => [
                'key' => $AWS_ACCESS_KEY_ID,
                'secret' => $AWS_SECRET_ACCESS_KEY,
            ],
        ]);
        
        // This address must be verified with Amazon SES.
        if($to == "support@denvelope.com"){
            $sender_email = 'Denvelope Support Requests <no-reply@denvelope.com>';
        }
        else if($to == "errors@denvelope.com"){
            $sender_email = 'Denvelope Errors <no-reply@denvelope.com>';
        }
        else if(strpos($subject, "Reply To: ")){
            $sender_email = 'Denvelope Account Support <support@denvelope.com>';
        }
        else{
            $sender_email = 'Denvelope Account <account@denvelope.com>';
        }
        
        $recipient_emails = [
            $to,
        ];
        
        // Specify a configuration set. If you do not want to use a configuration
        // set, comment the following variable, and the
        // 'ConfigurationSetName' => $configuration_set argument below.
        //$configuration_set = 'ConfigSet';
        
        $char_set = 'UTF-8';
        
        try {
            $result = $SesClient->sendEmail([
                'Destination' => [
                    'ToAddresses' => $recipient_emails,
                ],
                //'ReplyToAddresses' => [$sender_email],
                'Source' => $sender_email,
                'Message' => [
                  'Body' => [
                      /*
                      'Html' => [
                          'Charset' => $char_set,
                          'Data' => $html_body,
                      ],
                      */
                      'Text' => [
                          'Charset' => $char_set,
                          'Data' => $plaintext_body,
                      ],
                  ],
                  'Subject' => [
                      'Charset' => $char_set,
                      'Data' => $subject,
                  ],
                ],
                // If you aren't using a configuration set, comment or delete the
                // following line
                //'ConfigurationSetName' => $configuration_set,
            ]);
        } catch (AwsException $e) {
            // output error message if fails
            echo $e->getMessage();
            echo("The email was not sent. Error message: ".$e->getAwsErrorMessage()."\n");
            echo "\n";
        }
    }
?>