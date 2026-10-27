<?php
if (!defined('SMF')) die('No direct access.');

global $sourcedir;
require_once($sourcedir . '/Subs.php');

$file = '$sourcedir/BetterAds.php';

foreach (array(
    'integrate_admin_areas' => 'betterads_admin_areas',
    'integrate_actions' => 'betterads_actions',
    'integrate_current_action' => 'betterads_current_action',
    'integrate_menu_buttons' => 'betterads_menu_buttons',
    'integrate_load_theme' => 'betterads_load_theme',
    'integrate_betterportal_blocks' => 'betterads_portal_blocks',
    'integrate_bettergallery_ad_slot' => 'betterads_gallery_slot',
) as $hook => $function)
    remove_integration_function($hook, $function, true, $file);

// Clean all footer-hook variants used during dev14b/dev14c, then the final form.
remove_integration_function('integrate_buffer', 'betterads_footer_link_buffer', true);
remove_integration_function('integrate_buffer', 'betterads_footer_link_buffer', true, '$sourcedir/BetterAds.php');
remove_integration_function('integrate_buffer', 'betterads_footer_link_buffer', true, '$sourcedir/BetterAdsFooter.php');

remove_integration_function('integrate_pre_include', '$sourcedir/BetterAdsFooter.php', true);
remove_integration_function('integrate_pre_include', '$sourcedir/BetterAdsFooter.php', true, '$sourcedir/BetterAds.php');
