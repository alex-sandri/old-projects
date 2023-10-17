<?php

header("Content-Type: application/json");

require(dirname(__FILE__, 2) . "/src/autoload.php");

use Denvelope\Models\CSRFToken;
use Denvelope\Models\File;
use Denvelope\Models\Folder;
use Denvelope\Models\Storage;
use Denvelope\Models\User;

use Denvelope\Utils\AwsUtilities;
use Denvelope\Utils\Translation;

if (isset($_POST['data']))
{
    $data = json_decode($_POST['data'], true);

    $data['data']['is_from_api'] = true;

    $object = is_array($data['action']) && array_key_exists("object", $data['action']) ? $data['action']['object'] : $data['object'];
    $action = is_array($data['action']) && array_key_exists("type", $data['action']) ? $data['action']['type'] : $data['action'];

    $data = $data['data'];

    switch ($object) {
        case "aws":
            switch ($action) {
                case "create_presigned_request":
                    $result = AwsUtilities::CreatePresignedRequest($data);
                break;
                default:
                    http_response_code(404);
                    exit();
                break;
            }
        break;
        case "file":
            switch ($action) {
                case "create":
                    $result = File::Create($data);
                break;
                case "retrieve":
                    $result = File::Retrieve($data);
                break;
                case "update":
                    $result = File::Update($data);
                break;
                case "delete":
                    $result = File::Delete($data);
                break;
                case "body":
                    $result = File::Body($data);
                break;
                case "download":
                    $result = File::Download($data);
                break;
                default:
                    http_response_code(404);
                    exit();
                break;
            }
        break;
        case "folder":
            switch ($action) {
                case "create":
                    $result = Folder::Create($data);
                break;
                case "retrieve":
                    $result = Folder::Retrieve($data);
                break;
                case "update":
                    $result = Folder::Update($data);
                break;
                case "delete":
                    $result = Folder::Delete($data);
                break;
                case "get_content":
                    $result = Folder::GetContent($data);
                break;
                default:
                    http_response_code(404);
                    exit();
                break;
            }
        break;
        case "storage":
            switch ($action) {
                case "used":
                    $result = Storage::Used($data);
                break;
                default:
                    http_response_code(404);
                    exit();
                break;
            }
        break;
        case "translation":
            switch ($action) {
                case "retrieve":
                    $result = Translation::Retrieve($data);
                break;
                case "bulk_retrieve":
                    $result = Translation::BulkRetrieve($data);
                break;
                default:
                    http_response_code(404);
                    exit();
                break;
            }
        break;
        case "user":
            switch ($action) {
                case "create":
                    $result = User::Create($data);
                break;
                case "retrieve":
                    $result = User::Retrieve($data);
                break;
                /* THIS MUSTN'T BE ACCESSIBLE BY ANY USER
                case "update":
                    $result = User::Update($data);
                break;
                */
                case "delete":
                    $result = User::Delete($data);
                break;
                case "authenticate":
                    $result = User::Authenticate($data);
                break;
                case "change_password":
                    $result = User::ChangePassword($data);
                break;
                case "change_username":
                    $result = User::ChangeUsername($data);
                break;
                case "forgot_password":
                    $result = User::ForgotPassword($data);
                break;
                case "logout":
                    $result = User::LogOut($data);
                break;
                default:
                    http_response_code(404);
                    exit();
                break;
            }
        break;
        default:
            http_response_code(404);
            exit();
        break;
    }

    echo json_encode($result);

    exit();
} else {
    http_response_code(403);
    exit();
}