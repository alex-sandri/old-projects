<?php
    require("../vendor/autoload.php");

    use Aws\S3\S3Client;

    function deleteFileFromS3($objectKey){
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

        // Delete an object from the bucket.
        $s3->deleteObject([
            'Bucket' => $bucket,
            'Key'    => $keyname
        ]);
    }
?>