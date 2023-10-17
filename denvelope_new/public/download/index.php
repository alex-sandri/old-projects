<?php
    
require(dirname(__FILE__, 3) . "/src/autoload.php");

use Denvelope\Models\File;
use Denvelope\Models\Folder;

if (isset($_GET['file']) && File::Exists(["id" => $_GET['file']]))
{
    File::Download(["id" => $_GET['file']]);
}
else if (isset($_GET['folder']) && Folder::Exists(["id" => $_GET['folder']]))
{
    /*
    Folder::Download(["id" => $_GET['folder']]);
    */
}
else 
{
    http_response_code(400);
    exit();
}