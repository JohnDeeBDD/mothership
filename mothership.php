<?php
/*
 Plugin Name: Mothership
*/

namespace Mothership;

require_once (plugin_dir_path(__FILE__). 'src/Mothership/autoloader.php');

$SettingsPage = new SettingsPage;
$SettingsPage->enable();

$ChangeAdminEmailMothership = new ChangeAdminEmailMothership;
$ChangeAdminEmailMothership->enableTestEmailRoute();
