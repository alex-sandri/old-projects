<?php

namespace Denvelope\Models;

require(\dirname(__FILE__, 2) . "/autoload.php");

use Denvelope\API\APIData;
use Denvelope\API\APIObject;

use Denvelope\Config\Config;
use Denvelope\Config\Linguist;

use Denvelope\Database\DatabaseInfo;
use Denvelope\Database\DatabaseOperations;

use Denvelope\Interfaces\ApiInterface;

use Denvelope\Models\Folder;
use Denvelope\Models\User;
use Denvelope\Models\UserSession;

use Denvelope\Utils\AwsUtilities;
use Denvelope\Utils\Translation;
use Denvelope\Utils\Utilities;

require(\dirname(__FILE__, 3) . "/vendor/autoload.php");

use Brick\Math\BigInteger;

/**
 * @package Denvelope\Models
 */
class File implements ApiInterface
{
    public static function Create (array $params) : array
    {
        if (!\array_key_exists("name", $params) || !\array_key_exists("parent_folder_id", $params))
        {
            \http_response_code(400);
            exit();
        }

        if ($params['is_from_api'] === true && (\array_key_exists("original_name", $params) || \array_key_exists("copy_count", $params)))
        {
            \http_response_code(403);
            exit();
        }

        if (self::Exists(["name" => $params['name'], "parent_folder_id" => $params['parent_folder_id']]))
        {
            if (!\array_key_exists("original_name", $params)) $params['original_name'] = $params['name'];

            if (!\array_key_exists("copy_count", $params)) $params['copy_count'] = $copy_count = 1;
            else $copy_count = ++$params['copy_count'];

            $params['name'] = $params['original_name'] . " ($copy_count)";

            $params['is_from_api'] = false;

            return self::Create($params);
        }

        $user_id = UserSession::RetrieveUserId();

        $id_path = $params['parent_folder_id'] !== "root"
            ?   Folder::Retrieve([
                    "id" => $params['parent_folder_id']
                ])[DatabaseInfo::FOLDERS_TABLE['columns']['id_path']["column_name"]] . "/"
            :   $params['parent_folder_id'] . "/";

        if (!\array_key_exists("id", $params))
        {
            $id = Utilities::generateUniqueId(
                DatabaseInfo::FILES_TABLE['table_name'],
                DatabaseInfo::FILES_TABLE['columns']['id']["column_name"],
                Config::FILE_ID_LENGTH,
                "base62"
            );

            $key = $user_id . substr($id_path, 4) . $id;

            AwsUtilities::CreateEmptyObject($key);
        }
        else
        {
            $id = $params['id'];
        }

        $id_path .= $id;

        $result = DatabaseOperations::insert([
            "table" => DatabaseInfo::FILES_TABLE['table_name'],
            "columns" => [
                DatabaseInfo::FILES_TABLE['columns']['id']["column_name"],
                DatabaseInfo::FILES_TABLE['columns']['user_id']["column_name"],
                DatabaseInfo::FILES_TABLE['columns']['name']["column_name"],
                DatabaseInfo::FILES_TABLE['columns']['parent_folder_id']["column_name"],
                DatabaseInfo::FILES_TABLE['columns']['id_path']["column_name"],
            ],
            "values" => [
                $id,
                $user_id,
                $params['name'],
                $params['parent_folder_id'],
                $id_path,
            ]
        ]);

        return APIObject::create(
            "file_create",
            true,
            [],
            [
                "id" => APIData::create("id", $id),
                "name" => APIData::create("name", $params['name']),
                "language" => APIData::create("language", Linguist::Get(Linguist::Detect($params['name']))['icon_name']),
                "parent_folder_id" => APIData::create("parent_folder_id", $params['parent_folder_id']),
            ],
        );
    }

    public static function Retrieve (array $params) : array
    {
        if(!\array_key_exists("id", $params))
        {
            \http_response_code(400);
            exit();
        }

        $result = DatabaseOperations::select([
            "columns" => [
                "*"
            ],
            "table" => DatabaseInfo::FILES_TABLE['table_name'],
            "filters" => [
                "where" => [
                    [
                        "field" => DatabaseInfo::FILES_TABLE['columns']['id']["column_name"],
                        "value" => [
                            "identical" => $params['id']
                        ]
                    ]
                ]
            ]
        ]);

        if($result['num_rows'] === 0)
        {
            \http_response_code(404);
            exit();
        }

        if(\array_key_exists("return_type", $params) && $params['return_type'] === "info")
        {
            return APIObject::create(
                "file_retrieve",
                true,
                [],
                [
                    "info" => self::GetInfo($result['result']),
                    "title" => Translation::get("api->file->info->title"),
                ]
            );
        }

        return $result['result'];
    }

    public static function Update (array $data) : array
    {
        if(!\array_key_exists("id", $data))
        {
            \http_response_code(400);
            exit();
        }

        $id = $data['id'];

        unset($data['id']);

        if (array_key_exists("tags", $data))
        {
            $data['tags'] = json_encode($data['tags']);
        }

        $result = DatabaseOperations::update([
            "table" => DatabaseInfo::FILES_TABLE['table_name'],
            "columns" => \array_map(function ($column) {
                    return DatabaseInfo::FILES_TABLE['columns'][$column]["column_name"];
                }, \array_keys($data))
            ,
            "filters" => [
                "where" => [
                    [
                        "field" => DatabaseInfo::FILES_TABLE['columns']['id']["column_name"],
                        "value" => [
                            "identical" => $id
                        ]
                    ]
                ],
                "update_columns_values" => \array_map(function ($value) {
                        return $value;
                    }, \array_values($data))
                ,
            ]
        ]);

        $response_data = [];

        if (\array_key_exists("name", $data))
        {
            $response_data['name'] = APIData::create("name", $result['result'][DatabaseInfo::FILES_TABLE['columns']['name']["column_name"]]);
        }

        return APIObject::create(
            "file_update",
            true,
            [],
            $response_data
        );
    }

    public static function Delete (array $params) : array
    {
        if(!\array_key_exists("id", $params))
        {
            \http_response_code(400);
            exit();
        }

        $result = DatabaseOperations::delete([
            "table" => DatabaseInfo::FILES_TABLE['table_name'],
            "filters" => [
                "where" => [
                    [
                        "field" => DatabaseInfo::FILES_TABLE['columns']['id']["column_name"],
                        "value" => [
                            "identical" => $params['id']
                        ]
                    ]
                ]
            ]
        ]);

        AwsUtilities::DeleteObject(UserSession::RetrieveUserId() . "/" . $params['id']);

        return APIObject::create(
            "file_delete",
            true,
            [],
            [
                "id" => APIData::create("id", $params['id']),
                "message" => APIData::create("message", Translation::get("api->messages->file->deleted")),
            ],
        );
    }

    public static function Exists (array $data) : bool
    {
        if(!\array_key_exists("id", $data) && (\array_key_exists("name", $data) && !\array_key_exists("parent_folder_id", $data)))
        {
            \http_response_code(400);
            exit();
        }

        if (\array_key_exists("id", $data))
        {
            $where_filters = [
                [
                    "field" => DatabaseInfo::FILES_TABLE['columns']['id']["column_name"],
                    "value" => [
                        "identical" => $data['id']
                    ]
                ]
            ];
        }
        else if (\array_key_exists("name", $data) && \array_key_exists("parent_folder_id", $data))
        {
            $where_filters = [
                [
                    "field" => DatabaseInfo::FILES_TABLE['columns']['name']["column_name"],
                    "value" => [
                        "identical" => $data['name']
                    ],
                    "logic_op" => "and"
                ],
                [
                    "field" => DatabaseInfo::FILES_TABLE['columns']['parent_folder_id']["column_name"],
                    "value" => [
                        "identical" => $data['parent_folder_id']
                    ]
                ],
            ];
        }

        $result = DatabaseOperations::select([
            "columns" => [
                "*"
            ],
            "table" => DatabaseInfo::FILES_TABLE['table_name'],
            "filters" => [
                "where" => $where_filters,
            ]
        ]);
        
        return !($result['num_rows'] === 0);
    }

    public static function Body (array $data) : array
    {
        if(!\array_key_exists("id", $data))
        {
            \http_response_code(400);
            exit();
        }

        $file_id_path = substr(self::Retrieve(["id" => $data["id"]])[DatabaseInfo::FILES_TABLE['columns']['id_path']["column_name"]], 4);

        return APIObject::create("file", true, [], [
            "body" => APIData::create("body", AwsUtilities::GetObjectBody(UserSession::RetrieveUserId() . $file_id_path)),
        ]);
    }

    public static function Download (array $data) : void
    {
        if(!\array_key_exists("id", $data))
        {
            \http_response_code(400);
            exit();
        }

        $result = self::Retrieve(["id" => $data['id']]);

        $body = self::Body(["id" => $data['id']])['data']['body']['value'];

        header("Content-Type: " . $result[DatabaseInfo::FILES_TABLE['columns']['mime_type']["column_name"]]);
        header("Content-Length: " . $result[DatabaseInfo::FILES_TABLE['columns']['size']["column_name"]]);
        header("Content-Disposition: attachment; filename=" . $result[DatabaseInfo::FILES_TABLE['columns']['name']["column_name"]]);
        
        echo $body;
        
        exit();
    }

    private static function GetInfo (array $file_data) : array
    {
        $file_id_path = substr($file_data[DatabaseInfo::FILES_TABLE['columns']['id_path']["column_name"]], 5);

        $file_info = [
            "id" => APIData::create(Translation::get("api->file->info->id"), $file_data[DatabaseInfo::FILES_TABLE['columns']['id']["column_name"]]),
            "name" => APIData::create(Translation::get("api->file->info->name"), $file_data[DatabaseInfo::FILES_TABLE['columns']['name']["column_name"]]),
            "created" => APIData::create(Translation::get("api->file->info->created"), Storage::RetrieveObjectCreationDate(UserSession::RetrieveUserId() . "/" . $file_id_path), "date"),
            "last_modified" => APIData::create(Translation::get("api->file->info->last_modified"), Storage::RetrieveObjectLastModifiedDate(UserSession::RetrieveUserId() . "/" . $file_id_path), "date"),
            "language" => APIData::create(Translation::get("api->file->info->language"), Linguist::GetDisplayName(Linguist::Detect($file_data[DatabaseInfo::FILES_TABLE['columns']['name']["column_name"]]))),
            "size" => APIData::create(
                Translation::get("api->file->info->size"),
                Utilities::formatStorage(AwsUtilities::RetrieveObjectSize(UserSession::RetrieveUserId() . "/" . $file_id_path), 1000, 2)
            ),
            "tags" => APIData::create(
                Translation::get("api->file->info->tags"),
                (
                    array_map(
                        function (string $tag) {
                            return Utilities::getLanguage($tag)['display_name'] . "--" . Linguist::Get($tag)['icon_name'];
                        },
                        Linguist::GetTags($file_data[DatabaseInfo::FOLDERS_TABLE['columns']['name']["column_name"]], true)
                    )
                ),
                "tags"
            ),
        ];

        return $file_info;
    }
}