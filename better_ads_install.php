<?php
if (!defined('SMF'))
    die('No direct access.');

global $sourcedir, $smcFunc, $boarddir;
require_once($sourcedir . '/Subs.php');
db_extend('packages');

$file = '$sourcedir/BetterAds.php';
foreach (array(
    'integrate_admin_areas' => 'betterads_admin_areas',
    'integrate_actions' => 'betterads_actions',
    'integrate_load_theme' => 'betterads_load_theme',
    'integrate_betterportal_blocks' => 'betterads_portal_blocks',
    'integrate_bettergallery_ad_slot' => 'betterads_gallery_slot',
) as $hook => $function)
    add_integration_function($hook, $function, true, $file);

// Keep the footer callback self-loading. SMF's hook loader will require this
// file before trying to call betterads_footer_link_buffer.
add_integration_function(
    'integrate_buffer',
    'betterads_footer_link_buffer',
    true,
    '$sourcedir/BetterAdsFooter.php'
);

$smcFunc['db_create_table']('{db_prefix}betterads_units', array(
    array('name'=>'id_unit','type'=>'int','size'=>10,'unsigned'=>true,'auto'=>true),
    array('name'=>'name','type'=>'varchar','size'=>160,'default'=>''),
    array('name'=>'unit_type','type'=>'varchar','size'=>20,'default'=>'code'),
    array('name'=>'ad_code','type'=>'text'),
    array('name'=>'image_url','type'=>'varchar','size'=>255,'default'=>''),
    array('name'=>'target_url','type'=>'varchar','size'=>255,'default'=>''),
    array('name'=>'alt_text','type'=>'varchar','size'=>255,'default'=>''),
    array('name'=>'image_size_mode','type'=>'varchar','size'=>20,'default'=>'responsive'),
    array('name'=>'image_width','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array('name'=>'image_height','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array('name'=>'image_align','type'=>'varchar','size'=>12,'default'=>'center'),
    array('name'=>'maintain_ratio','type'=>'tinyint','size'=>1,'unsigned'=>true,'default'=>1),
    array('name'=>'label_position','type'=>'varchar','size'=>12,'default'=>'above'),
    array('name'=>'label_align','type'=>'varchar','size'=>12,'default'=>'match'),
    array('name'=>'audience','type'=>'varchar','size'=>20,'default'=>'everyone'),
    array('name'=>'target_boards','type'=>'text'),
    array('name'=>'excluded_boards','type'=>'text'),
    array('name'=>'target_groups','type'=>'text'),
    array('name'=>'enabled','type'=>'tinyint','size'=>1,'unsigned'=>true,'default'=>1),
    array('name'=>'schedule_mode','type'=>'varchar','size'=>20,'default'=>'always'),
    array('name'=>'start_at','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array('name'=>'end_at','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array('name'=>'archived','type'=>'tinyint','size'=>1,'unsigned'=>true,'default'=>0),
    array('name'=>'archived_at','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array('name'=>'created_at','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array('name'=>'updated_at','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
), array(
    array('type'=>'primary','columns'=>array('id_unit')),
    array('type'=>'index','columns'=>array('enabled','unit_type')),
), array(), 'ignore');

$smcFunc['db_create_table']('{db_prefix}betterads_placements', array(
    array('name'=>'id_placement','type'=>'int','size'=>10,'unsigned'=>true,'auto'=>true),
    array('name'=>'placement_key','type'=>'varchar','size'=>60,'default'=>''),
    array('name'=>'id_unit','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array('name'=>'enabled','type'=>'tinyint','size'=>1,'unsigned'=>true,'default'=>1),
    array('name'=>'sort_order','type'=>'smallint','size'=>5,'unsigned'=>true,'default'=>0),
    array('name'=>'repeat_every','type'=>'smallint','size'=>5,'unsigned'=>true,'default'=>5),
    array('name'=>'repeat_start','type'=>'smallint','size'=>5,'unsigned'=>true,'default'=>3),
    array('name'=>'repeat_max','type'=>'smallint','size'=>5,'unsigned'=>true,'default'=>2),
), array(
    array('type'=>'primary','columns'=>array('id_placement')),
    array('type'=>'index','columns'=>array('placement_key','enabled','sort_order')),
    array('type'=>'index','columns'=>array('id_unit')),
), array(), 'ignore');


$smcFunc['db_create_table']('{db_prefix}betterads_stats_daily', array(
    array('name'=>'id_unit','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array('name'=>'stat_day','type'=>'int','size'=>8,'unsigned'=>true,'default'=>0),
    array('name'=>'impressions','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array('name'=>'clicks','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
), array(
    array('type'=>'primary','columns'=>array('id_unit','stat_day')),
    array('type'=>'index','columns'=>array('stat_day')),
), array(), 'ignore');



$smcFunc['db_create_table']('{db_prefix}betterads_offers', array(
    array('name'=>'id_offer','type'=>'int','size'=>10,'unsigned'=>true,'auto'=>true),
    array('name'=>'enabled','type'=>'tinyint','size'=>1,'unsigned'=>true,'default'=>0),
    array('name'=>'placement_key','type'=>'varchar','size'=>60,'default'=>''),
    array('name'=>'title','type'=>'varchar','size'=>160,'default'=>''),
    array('name'=>'price_text','type'=>'varchar','size'=>80,'default'=>''),
    array('name'=>'description','type'=>'text'),
    array('name'=>'payment_required','type'=>'tinyint','size'=>1,'unsigned'=>true,'default'=>1),
    array('name'=>'payment_url','type'=>'varchar','size'=>500,'default'=>''),
    array('name'=>'payment_instructions','type'=>'text'),
    array('name'=>'campaign_days','type'=>'smallint','size'=>5,'unsigned'=>true,'default'=>30),
    array('name'=>'sort_order','type'=>'smallint','size'=>5,'unsigned'=>true,'default'=>0),
    array('name'=>'created_at','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array('name'=>'updated_at','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
), array(
    array('type'=>'primary','columns'=>array('id_offer')),
    array('type'=>'index','columns'=>array('enabled','sort_order')),
), array(), 'ignore');


$smcFunc['db_create_table']('{db_prefix}betterads_requests', array(
    array('name'=>'id_request','type'=>'int','size'=>10,'unsigned'=>true,'auto'=>true),
    array('name'=>'id_member','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array('name'=>'id_unit','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array('name'=>'offer_slot','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array('name'=>'offer_title','type'=>'varchar','size'=>160,'default'=>''),
    array('name'=>'placement_key','type'=>'varchar','size'=>60,'default'=>''),
    array('name'=>'price_text','type'=>'varchar','size'=>80,'default'=>''),
    array('name'=>'payment_required','type'=>'tinyint','size'=>1,'unsigned'=>true,'default'=>1),
    array('name'=>'payment_url','type'=>'varchar','size'=>500,'default'=>''),
    array('name'=>'payment_instructions','type'=>'text'),
    array('name'=>'payment_status','type'=>'varchar','size'=>20,'default'=>'unpaid'),
    array('name'=>'payment_reference','type'=>'varchar','size'=>160,'default'=>''),
    array('name'=>'paid_at','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array('name'=>'order_token','type'=>'varchar','size'=>64,'default'=>''),
    array('name'=>'campaign_days','type'=>'smallint','size'=>5,'unsigned'=>true,'default'=>30),
    array('name'=>'renewal_of_request','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array('name'=>'contact_name','type'=>'varchar','size'=>160,'default'=>''),
    array('name'=>'contact_email','type'=>'varchar','size'=>255,'default'=>''),
    array('name'=>'company','type'=>'varchar','size'=>160,'default'=>''),
    array('name'=>'website_url','type'=>'varchar','size'=>500,'default'=>''),
    array('name'=>'creative_url','type'=>'varchar','size'=>500,'default'=>''),
    array('name'=>'message','type'=>'text'),
    array('name'=>'status','type'=>'varchar','size'=>20,'default'=>'pending'),
    array('name'=>'created_at','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
    array('name'=>'updated_at','type'=>'int','size'=>10,'unsigned'=>true,'default'=>0),
), array(
    array('type'=>'primary','columns'=>array('id_request')),
    array('type'=>'index','columns'=>array('status','created_at')),
    array('type'=>'index','columns'=>array('id_member')),
    array('type'=>'index','columns'=>array('id_unit')),
    array('type'=>'unique','columns'=>array('order_token')),
    array('type'=>'index','columns'=>array('payment_status')),
    array('type'=>'index','columns'=>array('renewal_of_request')),
), array(), 'ignore');

$defaults = array(
    'betterads_enabled' => 1,
    'betterads_show_label' => 1,
    'betterads_label_text' => 'ADVERTISEMENT',
    'betterads_advertise_enabled' => 0,
    'betterads_advertise_link_text' => 'Advertise With Us',
    'betterads_advertise_title' => 'Advertise With Us',
    'betterads_advertise_body' => 'Interested in advertising with us? Contact us to discuss available placements and campaign options.',
    'betterads_advertise_cta_text' => 'Contact Us',
    'betterads_advertise_cta_url' => '',
    'betterads_notify_admin_email' => 1,
    'betterads_version' => '1.1.0',
    'betterads_lifecycle_schema_version' => 1,
);
foreach ($defaults as $key => $value)
    updateSettings(array($key => $value));

$storage = rtrim($boarddir, '/\\') . '/better_ads_storage';
if (!is_dir($storage)) @mkdir($storage, 0755, true);
@file_put_contents($storage . '/index.php', "<?php http_response_code(403); exit;\n");
