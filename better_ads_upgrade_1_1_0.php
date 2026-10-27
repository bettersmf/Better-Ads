<?php
if (!defined('SMF')) die('No direct access.');

global $sourcedir, $smcFunc;
require_once($sourcedir . '/Subs.php');
db_extend('packages');

// 1.1 lifecycle schema. Idempotent so any 1.0.0/1.1-dev build can upgrade directly.
$smcFunc['db_add_column']('{db_prefix}betterads_offers',
    array('name'=>'campaign_days','type'=>'smallint','size'=>5,'unsigned'=>true,'default'=>30),
    array(), 'ignore'
);
$smcFunc['db_add_column']('{db_prefix}betterads_requests',
    array('name'=>'campaign_days','type'=>'smallint','size'=>5,'unsigned'=>true,'default'=>30),
    array(), 'ignore'
);
$smcFunc['db_add_column']('{db_prefix}betterads_requests',
    array('name'=>'renewal_of_request','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array(), 'ignore'
);

// 1.1 Gallery integration.
add_integration_function(
    'integrate_bettergallery_ad_slot',
    'betterads_gallery_slot',
    true,
    '$sourcedir/BetterAds.php'
);

// Remove notification-hook experiments from development builds. Stable uses
// the final rendered native SMF .amt badge through the existing buffer hook.
remove_integration_function('integrate_menu_buttons', 'betterads_menu_buttons', true, '$sourcedir/BetterAds.php');
remove_integration_function('integrate_current_action', 'betterads_current_action', true, '$sourcedir/BetterAds.php');

// Preserve the proven self-loading footer/output-buffer architecture.
remove_integration_function('integrate_buffer', 'betterads_footer_link_buffer', true);
remove_integration_function('integrate_buffer', 'betterads_footer_link_buffer', true, '$sourcedir/BetterAds.php');
add_integration_function('integrate_buffer', 'betterads_footer_link_buffer', true, '$sourcedir/BetterAdsFooter.php');

updateSettings(array(
    'betterads_version' => '1.1.0',
    'betterads_lifecycle_schema_version' => 1,
));
