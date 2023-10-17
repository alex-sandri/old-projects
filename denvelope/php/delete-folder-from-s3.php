<?php
    require("../vendor/autoload.php");

    use Aws\S3\S3Client;

    function deleteFolderFromS3($objects){
        require("global-vars.php");

        $bucket = $AWS_S3_BUCKET;

        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => [
                'key' => $AWS_ACCESS_KEY_ID,
                'secret' => $AWS_SECRET_ACCESS_KEY,
            ],
        ]);

        $keys = array();
        
        $i = 0;

        foreach ($objects as $object) {
            $keys[$i]['Key'] = htmlspecialchars(substr($object['pathToThis'], 3), ENT_XML1 | ENT_QUOTES);
            $i++;
        }
        
        $s3->deleteObjects([
            'Bucket'  => $bucket,
            'Delete' => [
                'Objects' => $keys,
            ],
        ]);
    }
?>