<?php
    require("../vendor/autoload.php");

    //Automatically determines the best way to upload the file
    use Aws\S3\S3Client;
    use Aws\Exception\AwsException;
    use Aws\S3\ObjectUploader;

    function uploadFileToS3($pathToFile, $objectKey){
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
        $key = $objectKey;
        
        $source = fopen($pathToFile, 'rb');

        $acl = "public-read";

        $options = [
            'params' => [
                'ServerSideEncryption' => 'AES256',
            ]
        ];
        
        $uploader = new ObjectUploader(
            $s3Client,
            $bucket,
            $key,
            $source,
            $acl,
            $options
        );
        
        do {
            try {
                $result = $uploader->upload();
            } catch (MultipartUploadException $e) {
                rewind($source);
                $uploader = new MultipartUploader($s3Client, $source, [
                    'acl' => $acl,
                    'state' => $e->getState(),
                ] + $options);
            }
        } while (!isset($result));

        unlink($pathToFile);
    }
?>