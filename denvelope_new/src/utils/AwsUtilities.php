<?php

namespace Denvelope\Utils;

require(\dirname(__FILE__, 2) . "/autoload.php");

use Denvelope\Config\Config;

use Denvelope\Database\DatabaseInfo;

use Denvelope\Models\File;
use Denvelope\Models\Storage;

require(\dirname(__FILE__, 3) . "/vendor/autoload.php");

use Aws\S3\S3Client;  
use Aws\S3\PostObjectV4;

class AwsUtilities
{
    public static function CreatePresignedRequest (array $data) : array
    {
        if (!\array_key_exists("size", $data))
        {
            \http_response_code(400);
            exit();
        }

        $s3_client = self::CreateS3Client();
        $bucket = self::GetS3Bucket();

        $form_inputs = [
            "acl" => "public-read",
            "success_action_status" => "200",
        ];

        if (!\array_key_exists("key", $data))
        {
            $key = Utilities::generateUniqueId(DatabaseInfo::FILES_TABLE['table_name'], DatabaseInfo::FILES_TABLE['columns']['id']["column_name"], Config::FILE_ID_LENGTH, "base62");
        }
        else
        {
            $key = $data['key'];
        }

        if (\array_key_exists("id", $data))
        {
            $file = File::Retrieve(["id" => $data['id']]);

            $key = $file[DatabaseInfo::FILES_TABLE['columns']['user_id']["column_name"]] . substr($file[DatabaseInfo::FILES_TABLE['columns']['id_path']["column_name"]], 4); // Removes the root prefix
        }

        $form_inputs = array_merge($form_inputs, [
            "X-Amz-Meta-created" => Storage::RetrieveObjectCreationDate($key),
        ]);

        $options = [
            ["acl" => "public-read"],
            ["bucket" => $bucket],
            ["content-length-range", $data['size'], $data['size']],
            ["success_action_status" => "200"],
            ["key" => $key],
            ["x-amz-meta-created" => Storage::RetrieveObjectCreationDate($key)],
        ];

        $expires = \time() + 60 * 10; // Expires after 10 minutes

        $post_object = new PostObjectV4(
            $s3_client,
            $bucket,
            $form_inputs,
            $options,
            $expires
        );

        $form_attributes = $post_object->getFormAttributes();

        $form_inputs = $post_object->getFormInputs();

        return [
            "attributes" => $form_attributes,
            "inputs" => $form_inputs,
            "key" => $key,
        ];
    }

    public static function CreateEmptyObject (string $key)
    {
        $s3_client = self::CreateS3Client();

        $result = $s3_client->putObject([
            "Bucket" => self::GetS3Bucket(),
            "Key" => $key,
            "Body" => "",
            "ACL" => "public-read",
            "Metadata" => [
                "created" => \time(),
            ],
            "ServerSideEncryption" => "AES256",
        ]);
    }

    public static function RetrieveObjectSize (string $key) : string
    {
        $result = self::RetrieveObjectMetadata($key);

        $size = $result['ContentLength'];

        return $size;
    }

    public static function GetObjectBody (string $key) : string
    {
        $s3_client = self::CreateS3Client();

        $result = $s3_client->getObject([
            "Bucket" => self::GetS3Bucket(),
            "Key" => $key,
        ]);

        return $result['Body'];
    }

    public static function DeleteObject (string $key) : void
    {
        $s3_client = self::CreateS3Client();

        $s3_client->deleteObject([
            "Bucket" => self::GetS3Bucket(),
            "Key" => $key,
        ]);
    }

    public static function DeleteObjects (array $keys) : void
    {
        $s3_client = self::CreateS3Client();

        $num_of_objects = count($keys);
        $objects = [];

        for ($i = 0; $i < $num_of_objects; $i++)
        {
            $objects[$i]['Key'] = htmlspecialchars($keys[$i][DatabaseInfo::FILES_TABLE['columns']['id']["column_name"]], ENT_XML1 | ENT_QUOTES);
        }

        $s3_client->deleteObjects([
            "Bucket" => self::GetS3Bucket(),
            "Delete" => [
                "Objects" => $objects,
            ]
        ]);
    }

    public static function ListAllObjects (string $prefix) : array
    {
        $s3_client = self::CreateS3Client();

        $continuation_token = "";

        $objects = [];

        do
        {
            $result = $s3_client->listObjectsV2([
                "Bucket" => self::GetS3Bucket(),
                "MaxKeys" => 1000,
                "Prefix" => $prefix,
                $continuation_token === "" ?: "ContinuationToken" => $continuation_token,
            ]);
    
            if ($result['KeyCount'] > 0) array_push($objects, ...$result['Contents']);

            if ($result['IsTruncated']) $continuation_token = $result['NextContinuationToken'];
        }
        while ($result['IsTruncated']);

        return $objects;
    }

    public static function RetrieveObjectMetadata (string $key) : object
    {
        $s3_client = self::CreateS3Client();

        $result = $s3_client->headObject([
            "Bucket" => self::GetS3Bucket(),
            "Key" => $key
        ]);

        return $result;
    }

    private static function CreateS3Client () : S3Client
    {
        $s3_client = new S3Client([
            "version" => "latest",
            "region"  => Config::APIS['aws']['region'],
            "credentials" => [
                "key" => Config::APIS['aws']['access_key_id'],
                "secret" => Config::APIS['aws']['secret_access_key'],
            ],
        ]);

        return $s3_client;
    }

    private static function GetS3Bucket () : string
    {
        return Config::APIS['aws']['bucket'];
    }
}