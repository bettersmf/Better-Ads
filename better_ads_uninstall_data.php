<?php
if (!defined('SMF')) die('No direct access.');
global $smcFunc, $sourcedir, $boarddir;
require_once($sourcedir . '/Subs.php');
db_extend('packages');
$smcFunc['db_drop_table']('{db_prefix}betterads_offers');
$smcFunc['db_drop_table']('{db_prefix}betterads_requests');
$smcFunc['db_drop_table']('{db_prefix}betterads_stats_daily');
$smcFunc['db_drop_table']('{db_prefix}betterads_placements');
$smcFunc['db_drop_table']('{db_prefix}betterads_units');
$smcFunc['db_query']('', 'DELETE FROM {db_prefix}settings WHERE variable LIKE {string:prefix}', array('prefix'=>'betterads_%'));
$storage = rtrim($boarddir, '/\\') . '/better_ads_storage';
if (is_dir($storage))
{
    foreach ((array) glob($storage . '/*') as $file)
        if (is_file($file)) @unlink($file);
    @rmdir($storage);
}
