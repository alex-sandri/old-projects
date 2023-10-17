<?php
    require("../vendor/autoload.php");

    use Aws\S3\S3Client;
    use Aws\S3\Exception\S3Exception;

    function createZipFolder($folderID, $folderName, $objectKeys, $folderPath){
        require("global-vars.php");
        require("base62-only-uuid.php");

        $s3Client = new S3Client([
            'region' => 'us-east-1',
            'version' => '2006-03-01',
            'credentials' => [
                'key' => $AWS_ACCESS_KEY_ID,
                'secret' => $AWS_SECRET_ACCESS_KEY,
            ],
        ]);
        
        $bucket = $AWS_S3_BUCKET;

        $randomFolderUUID = base62UUID(64);

        if(!file_exists("../user-content-tmp/".$folderID)){
            mkdir("../user-content-tmp/".$folderID);
        }

        $path = "../user-content-tmp/".$folderID."/".$randomFolderUUID;

        if(!file_exists($path)){
            mkdir($path);
        }

        if(!file_exists($path."-tmp-files")){
            mkdir($path."-tmp-files");
        }

        $zip = new ZipArchive();

        $zip->open($path."/".$folderName.".zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($objectKeys as $key) {
            $fileName = $key['name'];
            $filePath = $path."-tmp-files"."/".$fileName;

            try {
                // Get the object.
                $result = $s3Client->getObject([
                    'Bucket' => $bucket,
                    'Key'    => substr($key['pathToThis'], 3)
                ]);
    
                $file = fopen($filePath, "w") or die("Cannot create file!");
                fwrite($file, $result['Body']);
                fclose($file);

                $zip->addFile($filePath, substr($key['pathToThis'], strlen($folderPath) + 1));
            } catch (S3Exception $e) {
                echo $e->getMessage() . PHP_EOL;
            }
        }

        $zip->close();

        foreach ($objectKeys as $key) {
            $fileName = $key['name'];
            $filePath = $path."-tmp-files"."/".$fileName;

            if(file_exists($filePath)){
                unlink($filePath);
            }
        }
        
        rmdir($path."-tmp-files");

        return array(
            "path" => $path."/".$folderName.".zip",
            "name" => $folderName.".zip",
        );
    }
?>