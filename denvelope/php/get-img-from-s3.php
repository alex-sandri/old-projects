<?php
    require("../vendor/autoload.php");

    use Aws\S3\S3Client;
    use Aws\S3\Exception\S3Exception;

    function getImgFromS3($objectKey, $type){
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

        try {
            // Get the object.
            $result = $s3->getObject([
                'Bucket' => $bucket,
                'Key' => $keyname,
            ]);

            header("Content-Type: ".$type);
            header("Cache-Control: no-cache");
            
            echo $result['Body'];
        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
?>