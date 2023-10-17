<?php
    require("../vendor/autoload.php");
    require("delete-from-s3.php");

    use Aws\S3\S3Client;
    use Aws\Exception\AwsException;
    use Aws\S3\ObjectCopier;

    function renameFileS3($objectKey, $newObjectKey){
        require("global-vars.php");

        $s3Client = new S3Client([
            'region' => 'us-east-1',
            'version' => '2006-03-01',
            'credentials' => [
                'key' => $AWS_ACCESS_KEY_ID,
                'secret' => $AWS_SECRET_ACCESS_KEY,
            ],
        ]);
        
        $bucket = $AWS_S3_BUCKET;

        //removes the ../ prefix
        $objectKey = substr($objectKey, 3);
        $newObjectKey = substr($newObjectKey, 3);

        $source = array(
            "Bucket" => $bucket,
            "Key" => $objectKey,
        );

        $destination = array(
            "Bucket" => $bucket,
            "Key" => $newObjectKey,
        );

        $acl = "public-read";

        $options = [
            'params' => [
                'ServerSideEncryption' => 'AES256',
            ]
        ];
        
        $copier = new ObjectCopier(
            $s3Client,
            $source,
            $destination,
            $acl,
            $options
        );
        
        $result = $copier->copy();

        deleteFileFromS3($objectKey);
    }
?>