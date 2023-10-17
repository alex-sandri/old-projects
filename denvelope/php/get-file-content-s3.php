<?php
    require("../vendor/autoload.php");

    use Aws\S3\S3Client;
    use Aws\S3\Exception\S3Exception;

    function getFileContent($objectKey){
        $_SESSION['requiredFromFileIgnoreCookie'] = true;
        
        require("global-vars.php");

        $bucket = $AWS_S3_BUCKET;
        $keyname = $objectKey;
        
        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => [
                'key' => $AWS_ACCESS_KEY_ID,
                'secret' => $AWS_SECRET_ACCESS_KEY,
            ],
        ]);
        
        try {
            // Get the object.
            $result = $s3->getObject([
                'Bucket' => $bucket,
                'Key'    => $keyname
            ]);
        
            return $result['Body'];
        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;

            return "An error occurred while processing the request";
        }
    }
?>