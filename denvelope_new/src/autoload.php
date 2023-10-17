<?php

namespace Denvelope;

/**
 * @author Alex Sandri <me@alexsandri.com>
 */

require_once dirname(__FILE__) . "/api/Api.php";
require_once dirname(__FILE__) . "/api/ApiError.php";
require_once dirname(__FILE__) . "/api/ApiObject.php";
require_once dirname(__FILE__) . "/api/ApiResponse.php";
require_once dirname(__FILE__) . "/api/ApiStatus.php";

require_once dirname(__FILE__) . "/config/Config.php";

require_once dirname(__FILE__) . "/database/Database.php";
require_once dirname(__FILE__) . "/database/DatabaseInfo.php";
require_once dirname(__FILE__) . "/database/DatabaseOperations.php";

require_once dirname(__FILE__) . "/interfaces/ApiInterface.php";

require_once dirname(__FILE__) . "/models/Cookie.php";
require_once dirname(__FILE__) . "/models/File.php";
require_once dirname(__FILE__) . "/models/Folder.php";
require_once dirname(__FILE__) . "/models/Session.php";
require_once dirname(__FILE__) . "/models/Storage.php";
require_once dirname(__FILE__) . "/models/User.php";
require_once dirname(__FILE__) . "/models/UserSession.php";

require_once dirname(__FILE__) . "/utils/AwsUtilities.php";
require_once dirname(__FILE__) . "/utils/Crypto.php";
require_once dirname(__FILE__) . "/utils/Utilities.php";
require_once dirname(__FILE__) . "/utils/Validate.php";

use Denvelope\Models\Session;

if (!Session::exists()) Session::create();