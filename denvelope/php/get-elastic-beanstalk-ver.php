<?php
    require("../../vendor/autoload.php"); //required from settings, so ../../ was necessary and ../ only will throw an error

    use Aws\ElasticBeanstalk\ElasticBeanstalkClient;
    use Aws\ElasticBeanstalk\Exception;

    function getEBVer(){
        require("global-vars.php");
        
        $eb = new ElasticBeanstalkClient([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => [
                'key' => $AWS_ACCESS_KEY_ID,
                'secret' => $AWS_SECRET_ACCESS_KEY,
            ],
        ]);
        
        try {
            $result = $eb->describeApplicationVersions([
                'ApplicationName' => 'denvelope',
            ]);
        
            $ver = $result['ApplicationVersions'][0]['VersionLabel'];

            /* removed because the denvelope- prefix has been removed from the release number
            $ver = substr($ver, strpos($ver, "-") + 2); //extract the actual version number (denvelope-v1.0a -> 1.0a)
            */

            return $ver;
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
?>