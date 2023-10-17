<?php
    require_once("../vendor/autoload.php");

    use Aws\S3\S3Client;  
    use Aws\Exception\AwsException;

    function getS3ObjectURL($objectKey){
        $_SESSION['requiredFromFileIgnoreCookie'] = true;
        
        require("global-vars.php");

        $bucket = $AWS_S3_BUCKET;
        $keyname = substr($objectKey, 3);

        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => [
                'key' => $AWS_ACCESS_KEY_ID,
                'secret' => $AWS_SECRET_ACCESS_KEY,
            ],
        ]);

        $objectURL = $s3->getObjectUrl($bucket, $keyname);

        return $objectURL;
    }
?>