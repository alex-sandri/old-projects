<?php

namespace Denvelope\Models;

require dirname(__DIR__) . "/autoload.php";

use Denvelope\Api\Api;
use Denvelope\Api\ApiObject;
use Denvelope\Api\ApiResponse;
use Denvelope\Api\ApiStatus;

use Denvelope\Utils\AwsUtilities;
use Denvelope\Utils\Utilities;

require dirname(__DIR__, 2) . "/vendor/autoload.php";

use Brick\Math\BigInteger;

class Storage
{
    public static function Used (array $data) : array
    {
        if (!\array_key_exists("prefix", $data)) Api::SetStatus(ApiStatus::BAD_REQUEST);

        $storage_used = BigInteger::of(0);

        $result = AwsUtilities::ListAllObjects($data['prefix']);

        foreach ($result as $object) $storage_used = $storage_used->plus($object["Size"]);

        $response = new ApiResponse(ApiObject::STORAGE, ApiStatus::OK, [
            "storage" => array_key_exists("raw", $data) && $data['raw'] ? $storage_used->__toString() : Utilities::formatStorage($storage_used->__toString(), 1000, 2)
        ]);

        return $response->__serialize();
    }

    public static function RetrieveObjectCreationDate (string $key) : string
    {
        return AwsUtilities::RetrieveObjectMetadata($key)["Metadata"]["created"];
    }

    public static function RetrieveObjectLastModifiedDate (string $key) : string
    {
        return AwsUtilities::RetrieveObjectMetadata($key)["LastModified"]->getTimeStamp();
    }
}