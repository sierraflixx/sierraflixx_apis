<?php
require_once "utils/authenticate.php";

require "controllers/common/delete.php";

sendDetails($deletes, 200, ['secret']);
