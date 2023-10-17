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

use Denvelope\Models\User;
use Denvelope\Models\UserSession;

use Denvelope\Utils\Translation;
use Denvelope\Utils\Utilities;

require(\dirname(__FILE__, 3) . "/vendor/autoload.php");

use Brick\Math\BigInteger;
use Denvelope\Utils\AwsUtilities;

/**
 * @package Denvelope\Models
 */
class Folder implements ApiInterface
{
    public static function Create (array $params) : array
    {
        if (!\array_key_exists("name", $params) || !\array_key_exists("parent_folder_id", $params))
        {
            \http_response_code(400);
            exit();
        }

        if (!\array_key_exists("id", $params))
        {
            $id = Utilities::generateUniqueId(
                DatabaseInfo::FOLDERS_TABLE['table_name'],
                DatabaseInfo::FOLDERS_TABLE['columns']['id']["column_name"],
                Config::FOLDER_ID_LENGTH,
                "base62"
            );
        }
        else
        {
            $id = $params['id'];
        }

        $result = DatabaseOperations::insert([
            "table" => DatabaseInfo::FOLDERS_TABLE['table_name'],
            "columns" => [
                DatabaseInfo::FOLDERS_TABLE['columns']['id']["column_name"],
                DatabaseInfo::FOLDERS_TABLE['columns']['user_id']["column_name"],
                DatabaseInfo::FOLDERS_TABLE['columns']['name']["column_name"],
                DatabaseInfo::FOLDERS_TABLE['columns']['created']["column_name"],
                DatabaseInfo::FOLDERS_TABLE['columns']['parent_folder_id']["column_name"],
                DatabaseInfo::FOLDERS_TABLE['columns']['id_path']["column_name"],
            ],
            "values" => [
                $id,
                UserSession::getUser()[DatabaseInfo::USERS_TABLE['columns']['id']["column_name"]],
                $params['name'],
                \time(),
                $params['parent_folder_id'],
                $params['parent_folder_id'] !== "root"
                    ?   self::Retrieve([
                            "id" => $params['parent_folder_id']
                        ])[DatabaseInfo::FOLDERS_TABLE['columns']['id_path']["column_name"]] . "/" . $id
                    :   $params['parent_folder_id'] . "/" . $id
            ]
        ]);

        return APIObject::create(
            "folder_create",
            true,
            [],
            [
                "id" => APIData::create("id", $id),
                "name" => APIData::create("name", $params['name']),
                "language" => APIData::create("language", Linguist::Get("folder")['icon_name']),
                "parent_folder_id" => APIData::create("parent_folder_id", $params['parent_folder_id']),
            ],
        );
    }

    public static function Retrieve (array $params) : array
    {
        if (!\array_key_exists("id", $params))
        {
            \http_response_code(400);
            exit();
        }

        $result = DatabaseOperations::select([
            "columns" => [
                "*"
            ],
            "table" => DatabaseInfo::FOLDERS_TABLE['table_name'],
            "filters" => [
                "where" => [
                    [
                        "field" => DatabaseInfo::FOLDERS_TABLE['columns']['id']["column_name"],
                        "value" => [
                            "identical" => $params['id']
                        ]
                    ]
                ]
            ]
        ]);

        if ($result['num_rows'] === 0)
        {
            \http_response_code(404);
            exit();
        }

        if (\array_key_exists("return_type", $params) && $params['return_type'] === "info")
        {
            return APIObject::create(
                "folder_retrieve",
                true,
                [],
                [
                    "info" => self::GetInfo($result['result']),
                    "title" => Translation::get("api->folder->info->title"),
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

        $data[DatabaseInfo::FOLDERS_TABLE['columns']['last_modified']["column_name"]] = \time();

        $result = DatabaseOperations::update([
            "table" => DatabaseInfo::FOLDERS_TABLE['table_name'],
            "columns" => \array_map(function ($column) {
                    return DatabaseInfo::FOLDERS_TABLE['columns'][$column]["column_name"];
                }, \array_keys($data))
            ,
            "filters" => [
                "where" => [
                    [
                        "field" => DatabaseInfo::FOLDERS_TABLE['columns']['id']["column_name"],
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
            $response_data['name'] = APIData::create("name", $result['result'][DatabaseInfo::FOLDERS_TABLE['columns']['size']["column_name"]]);
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
        if (!\array_key_exists("id", $params))
        {
            \http_response_code(400);
            exit();
        }

        $result = DatabaseOperations::delete([
            "table" => DatabaseInfo::FOLDERS_TABLE['table_name'],
            "filters" => [
                "where" => [
                    [
                        "field" => DatabaseInfo::FOLDERS_TABLE['columns']['id_path']["column_name"],
                        "value" => [
                            "like" => Utilities::AddSqlLikeCharacters($params['id'], "both")
                        ]
                    ]
                ]
            ]
        ]);

        $objects = DatabaseOperations::select([
            "columns" => [
                DatabaseInfo::FILES_TABLE['columns']['id']["column_name"]
            ],
            "table" => DatabaseInfo::FILES_TABLE['table_name'],
            "filters" => [
                "where" => [
                    [
                        "field" => DatabaseInfo::FILES_TABLE['columns']['id_path']["column_name"],
                        "value" => [
                            "like" => Utilities::AddSqlLikeCharacters($params['id'], "both")
                        ]
                    ]
                ]
            ],
            "result" => [
                "count" => "all"
            ],
        ]);

        AwsUtilities::DeleteObjects($objects['result']);

        $result = DatabaseOperations::delete([
            "table" => DatabaseInfo::FILES_TABLE['table_name'],
            "filters" => [
                "where" => [
                    [
                        "field" => DatabaseInfo::FILES_TABLE['columns']['id_path']["column_name"],
                        "value" => [
                            "like" => Utilities::AddSqlLikeCharacters($params['id'], "both")
                        ]
                    ]
                ]
            ]
        ]);

        return APIObject::create(
            "folder_delete",
            true,
            [],
            [
                "id" => APIData::create("id", $params['id']),
                "message" => APIData::create("message", Translation::get("api->messages->folder->deleted")),
            ],
        );
    }

    public static function Exists (array $data) : bool
    {
        if(!\array_key_exists("id", $data))
        {
            \http_response_code(400);
            exit();
        }

        $result = DatabaseOperations::select([
            "columns" => [
                "*"
            ],
            "table" => DatabaseInfo::FOLDERS_TABLE['table_name'],
            "filters" => [
                "where" => [
                    [
                        "field" => DatabaseInfo::FOLDERS_TABLE['columns']['id']["column_name"],
                        "value" => [
                            "identical" => $data['id']
                        ]
                    ]
                ]
            ]
        ]);
        
        return !($result['num_rows'] === 0);
    }

    public static function GetContent (array $params) : array
    {
        if (
            !\array_key_exists("folder_limit", $params) ||
            !\array_key_exists("folder_offset", $params) ||
            !\array_key_exists("file_limit", $params) ||
            !\array_key_exists("file_offset", $params) ||
            !\array_key_exists("order_by", $params) ||
            !\array_key_exists("order_dir", $params)
        )
        {
            \http_response_code(400);
            exit();
        }

        if (\array_key_exists("user_id", $params))
        {
            $user_id = $params['user_id'];
        }
        else if (UserSession::isValid())
        {
            $user_id = UserSession::getUser()[DatabaseInfo::USERS_TABLE['columns']['id']["column_name"]];
        }
        else
        {
            \http_response_code(401);
            exit();
        }

        if (!\array_key_exists("id", $params) && !\array_key_exists("search_term", $params))
        {
            \http_response_code(400);
            exit();
        }
        else
        {
            if (\array_key_exists("id", $params))
            {
                $id = $params['id'];
            }
            else
            {
                $search_term = $params['search_term'];
            }
        }

        $folder_limit = $params['folder_limit'];
        $folder_offset = $params['folder_offset'];
        $file_limit = $params['file_limit'];
        $file_offset = $params['file_offset'];
        $order_by = $params['order_by'];
        $order_dir = $params['order_dir'];

        // Incrementing to determine if there could be more data to be fetched
        $folder_limit++;
        $file_limit++;

        $folders_result = DatabaseOperations::select([
            "columns" => [
                DatabaseInfo::FOLDERS_TABLE['columns']['id']["column_name"],
                DatabaseInfo::FOLDERS_TABLE['columns']['name']["column_name"],
            ],
            "table" => DatabaseInfo::FOLDERS_TABLE['table_name'],
            "filters" => [
                "where" => [
                    [
                        "field" => DatabaseInfo::FOLDERS_TABLE['columns']['user_id']["column_name"],
                        "value" => [
                            "identical" => $user_id
                        ],
                        "logic_op" => "and"
                    ],
                    \array_key_exists("id", $params)
                        ?
                        [
                            "field" => DatabaseInfo::FOLDERS_TABLE['columns']['parent_folder_id']["column_name"],
                            "value" => [
                                "identical" => $id
                            ]
                        ]
                        :
                        [
                            "field" => DatabaseInfo::FOLDERS_TABLE['columns']['name']["column_name"],
                            "value" => [
                                "like" => Utilities::AddSqlLikeCharacters($search_term, "both")
                            ]
                        ]
                    ,
                ],
                "limit" => $folder_limit,
                "offset" => $folder_offset,
                "order_by" => [
                    "column" => $order_by,
                    "direction" => $order_dir
                ]
            ],
            "result" => [
                "count" => "all"
            ]
        ]);

        $files_result = DatabaseOperations::select([
            "columns" => [
                DatabaseInfo::FILES_TABLE['columns']['id']["column_name"],
                DatabaseInfo::FILES_TABLE['columns']['name']["column_name"],
            ],
            "table" => DatabaseInfo::FILES_TABLE['table_name'],
            "filters" => [
                "where" => [
                    [
                        "field" => DatabaseInfo::FILES_TABLE['columns']['user_id']["column_name"],
                        "value" => [
                            "identical" => $user_id
                        ],
                        "logic_op" => "and"
                    ],
                    \array_key_exists("id", $params)
                        ?
                        [
                            "field" => DatabaseInfo::FILES_TABLE['columns']['parent_folder_id']["column_name"],
                            "value" => [
                                "identical" => $id
                            ]
                        ]
                        :
                        [
                            "field" => DatabaseInfo::FILES_TABLE['columns']['name']["column_name"],
                            "value" => [
                                "like" => Utilities::AddSqlLikeCharacters($search_term, "both")
                            ]
                        ]
                    ,
                ],
                "limit" => $file_limit,
                "offset" => $file_offset,
                "order_by" => [
                    "column" => $order_by,
                    "direction" => $order_dir
                ]
            ],
            "result" => [
                "count" => "all"
            ]
        ]);

        for ($i = 0; $i < $files_result['num_rows']; $i++)
        {
            $files_result['result'][$i]["language"] = Linguist::Get(Linguist::Detect($files_result['result'][$i][DatabaseInfo::FILES_TABLE['columns']['name']["column_name"]]))['icon_name'];
        }

        $more_files = $more_folders = $no_results = false;

        if($folders_result['num_rows'] === $folder_limit){
            $more_folders = true;

            \array_pop($folders_result['result']);
        }
        
        if($files_result['num_rows'] === $file_limit){
            $more_files = true;

            \array_pop($files_result['result']);
        }

        if ($folders_result['num_rows'] === 0 && $files_result['num_rows'] === 0) {
            $no_results = true;

            if (\array_key_exists("id", $params))
            {
                $message = "empty";
            }
            else
            {
                $message = "no_search_results";
            }
        }

        return array(
            "folders" => [
                "result" => $folders_result['result'],
                "more" => $more_folders,
            ],
            "files" => [
                "result" => $files_result['result'],
                "more" => $more_files,
            ],
            "message" => $no_results ? Translation::get("api->messages->folder->" . $message) : "",
        );
    }

    private static function GetInfo (array $folder_data) : array
    {
        $folder_info = [
            "id" => APIData::create(Translation::get("api->folder->info->id"), $folder_data[DatabaseInfo::FOLDERS_TABLE['columns']['id']["column_name"]]),
            "name" => APIData::create(Translation::get("api->folder->info->name"), $folder_data[DatabaseInfo::FOLDERS_TABLE['columns']['name']["column_name"]]),
            "created" => APIData::create(Translation::get("api->folder->info->created"), $folder_data[DatabaseInfo::FOLDERS_TABLE['columns']['created']["column_name"]], "date"),
            //"last_modified" => APIData::create(Translation::get("api->folder->info->last_modified"), $folder_data[DatabaseInfo::FOLDERS_TABLE['columns']['last_modified']["column_name"]], "date"),
            "size" => APIData::create(
                Translation::get("api->folder->info->size"),
                Storage::Used([
                    "prefix" => UserSession::RetrieveUserId() . substr($folder_data[DatabaseInfo::FOLDERS_TABLE['columns']['id_path']["column_name"]], 4),
                ])['response']['data']['storage']
            ),
            "tags" => APIData::create(
                Translation::get("api->folder->info->tags"),
                (
                    array_map(
                        function (string $tag) {
                            return Linguist::Get($tag)['display_name'] . "--" . Linguist::Get($tag)['icon_name'];
                        },
                        Linguist::GetTags($folder_data[DatabaseInfo::FOLDERS_TABLE['columns']['name']["column_name"]], false)
                    )
                ),
                "tags"
            ),
        ];

        return $folder_info;
    }
}