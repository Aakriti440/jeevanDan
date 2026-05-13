<?php
/**
 * JeevanDaan - Entry Point
 */

// Load config
require_once '../app/config/config.php';
require_once '../app/config/Database.php';

// Load core
require_once '../app/core/App.php';
require_once '../app/core/Controller.php';

// Initialize
$app = new App();
