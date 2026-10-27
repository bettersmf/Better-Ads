<?php
$txt['betterads_admin_title'] = 'Better Ads';
$txt['betterads_admin_intro'] = 'Create reusable ad units and place them without editing theme templates. dev16 adds smart per-package inquiry routing: use Better Contact\'s visible Contact Staff link when present, otherwise use the configured fallback inquiry URL.';
$txt['betterads_enabled'] = 'Enable Better Ads';
$txt['betterads_enabled_help'] = 'Render enabled Better Ads placements on the public site.';
$txt['betterads_show_label'] = 'Advertisement label';
$txt['betterads_show_label_help'] = 'Show a small muted label above every rendered ad, including Better Portal blocks.';
$txt['betterads_label_text'] = 'Label text';
$txt['betterads_label_text_help'] = 'Defaults to ADVERTISEMENT. You can change this to SPONSORED or another short disclosure label.';
$txt['betterads_add_unit'] = 'Add Ad Unit';
$txt['betterads_edit_unit'] = 'Edit Ad Unit';
$txt['betterads_units'] = 'Ad Units';
$txt['betterads_name'] = 'Name';
$txt['betterads_type'] = 'Type';
$txt['betterads_type_code'] = 'Raw ad code / HTML';
$txt['betterads_type_image'] = 'Image / graphic';
$txt['betterads_code'] = 'Ad code';
$txt['betterads_code_help'] = 'Admin-only raw HTML/JavaScript. Use this for AdSense or other network code.';
$txt['betterads_image_upload'] = 'Upload graphic';
$txt['betterads_image_url'] = 'Image URL';
$txt['betterads_target_url'] = 'Click-through URL';
$txt['betterads_alt_text'] = 'Alt / hover title text';
$txt['betterads_alt_text_help'] = 'Used as the image alt attribute for accessibility and as the browser hover title.';
$txt['betterads_placement'] = 'Placement';
$txt['betterads_placement_help'] = 'Choose no automatic placement when this unit will be selected inside a Better Portal block.';
$txt['betterads_placement_none'] = 'No automatic placement / Portal only';
$txt['betterads_placement_global_header'] = 'Below site header — all public pages';
$txt['betterads_placement_global_footer'] = 'Above site footer — all public pages';
$txt['betterads_placement_board_index_top'] = 'Board index — top';
$txt['betterads_placement_board_index_bottom'] = 'Board index — bottom';
$txt['betterads_placement_topic_list_top'] = 'Topic list — top';
$txt['betterads_placement_topic_list_bottom'] = 'Topic list — bottom';
$txt['betterads_placement_topic_list_repeat'] = 'Topic list — between topics';
$txt['betterads_placement_topic_view_top'] = 'Topic view — top';
$txt['betterads_placement_topic_view_bottom'] = 'Topic view — bottom';
$txt['betterads_placement_topic_view_repeat'] = 'Topic view — between replies';
$txt['betterads_audience'] = 'Audience';
$txt['betterads_everyone'] = 'Everyone';
$txt['betterads_guests'] = 'Guests only';
$txt['betterads_members'] = 'Members only';
$txt['betterads_status'] = 'Status';
$txt['betterads_unit_enabled'] = 'Enabled';
$txt['betterads_on'] = 'On';
$txt['betterads_off'] = 'Off';
$txt['betterads_none'] = 'No ad units have been created yet.';
$txt['betterads_delete_confirm'] = 'Delete this ad unit?';
$txt['betterads_error_name'] = 'Please enter a name for this ad unit.';
$txt['betterads_error_upload'] = 'The uploaded graphic could not be accepted. Use JPG, PNG, GIF, or WEBP up to 8 MB.';
$txt['betterads_portal_block'] = 'Better Ads';
$txt['betterads_portal_block_desc'] = 'Display one enabled Better Ads unit inside this Portal block.';
$txt['betterads_portal_choose'] = 'Ad unit';
$txt['betterads_portal_choose_none'] = 'Choose an ad unit';

$txt['betterads_image_size_mode'] = 'Graphic size';
$txt['betterads_size_responsive'] = 'Automatic / responsive';
$txt['betterads_size_original'] = 'Original size (shrink only if needed)';
$txt['betterads_size_custom'] = 'Custom size';
$txt['betterads_image_size_help'] = 'Custom sizing uses the pixel fields below. All modes remain constrained to the available theme width.';
$txt['betterads_image_width'] = 'Custom width';
$txt['betterads_image_height'] = 'Custom height';
$txt['betterads_zero_auto'] = '0 = automatic';
$txt['betterads_maintain_ratio'] = 'Aspect ratio';
$txt['betterads_maintain_ratio_help'] = "Maintain the graphic's aspect ratio";
$txt['betterads_image_align'] = 'Graphic alignment';
$txt['betterads_align_left'] = 'Left';
$txt['betterads_align_center'] = 'Center';
$txt['betterads_align_right'] = 'Right';

$txt['betterads_group_portal'] = 'Portal / manual';
$txt['betterads_group_global'] = 'Global placements';
$txt['betterads_group_forum'] = 'Forum placements';
$txt['betterads_preview'] = 'Preview';
$txt['betterads_preview_title'] = 'Ad Preview';
$txt['betterads_preview_help'] = 'This uses the same Better Ads renderer as the public site. Disabled and audience-restricted units are temporarily shown here so the administrator can inspect them.';
$txt['betterads_close_preview'] = 'Close Preview';
$txt['betterads_cancel_edit'] = 'Cancel Edit';


$txt['betterads_repeat_start'] = 'Start after';
$txt['betterads_repeat_start_help'] = 'Place the first ad after this numbered topic or reply. Example: 1 means immediately after item #1. This schedule is shared by every ad in the same repeated placement. Default: 3.';
$txt['betterads_repeat_every'] = 'Repeat every';
$txt['betterads_repeat_every_help'] = 'After the first ad, wait this many additional topics or replies before the next placement slot. When multiple ads share this placement, Better Ads cycles through them. Example: start 1 + every 2 inserts after items 1, 3, 5, 7... Default: 5.';
$txt['betterads_repeat_max'] = 'Maximum per page';
$txt['betterads_repeat_max_help'] = 'Safety limit for this ad unit on one page. Default: 2.';
$txt['betterads_repeat_summary'] = 'after %1$d · every %2$d · max %3$d/page';

$txt['betterads_repeat_live_preview'] = 'Expected insertion points';
$txt['betterads_repeat_live_help'] = 'Based on the current values, ads will be inserted after items: %1$s (subject to the number of topics/replies actually on the page).';
$txt['betterads_needs_content'] = 'Needs content';
$txt['betterads_shared_placement'] = '%1$d enabled ad units share this placement. Rotation is active: static slots show one eligible ad per page load, while repeated slots cycle through the pool.';
$txt['betterads_content_help'] = 'Enabled units with no ad code or image are kept as drafts and do not render publicly.';


$txt['betterads_schedule'] = 'Schedule';
$txt['betterads_schedule_help'] = 'Control when this ad is eligible to render. Times use the administrator\'s current forum timezone.';
$txt['betterads_schedule_always'] = 'Always active';
$txt['betterads_schedule_window'] = 'Start and end at specific times';
$txt['betterads_schedule_start_only'] = 'Start at a specific time, then stay active';
$txt['betterads_schedule_end_only'] = 'Active now, end at a specific time';
$txt['betterads_start_at'] = 'Start date / time';
$txt['betterads_start_at_help'] = 'Before this time, the ad is automatically excluded from its placement or rotation pool.';
$txt['betterads_end_at'] = 'End date / time';
$txt['betterads_end_at_help'] = 'At this time, the ad is automatically excluded without deleting or disabling it.';
$txt['betterads_scheduled'] = 'Scheduled';
$txt['betterads_schedule_ended'] = 'Ended';
$txt['betterads_starts_short'] = 'starts %s';
$txt['betterads_ends_short'] = 'ends %s';
$txt['betterads_error_schedule_window'] = 'For a scheduled window, enter both a valid start and end time, with the end later than the start.';
$txt['betterads_error_schedule_start'] = 'Please enter a valid start date and time.';
$txt['betterads_error_schedule_end'] = 'Please enter a valid end date and time.';


$txt['betterads_target_boards'] = 'Only show in these boards';
$txt['betterads_target_boards_help'] = 'Optional. Leave empty for all boards. When boards are selected, this ad is eligible only while viewing one of those boards or its topics. Use Ctrl/Cmd-click to select multiple boards.';
$txt['betterads_excluded_boards'] = 'Exclude these boards';
$txt['betterads_excluded_boards_help'] = 'Optional. The ad will not render while viewing any selected board or its topics. Exclusions win if a board appears in both lists.';
$txt['betterads_target_groups'] = 'Only show to these membergroups';
$txt['betterads_target_groups_help'] = 'Optional. Leave empty to use the Audience setting alone. When selected, a logged-in member must belong to at least one selected group. Guests never match membergroup targeting.';
$txt['betterads_error_guest_groups'] = 'Membergroup targeting cannot be combined with Guests only. Choose Everyone or Members only, or clear the membergroup selection.';
$txt['betterads_target_summary_boards'] = '%1$d included board(s)';
$txt['betterads_target_summary_excluded'] = '%1$d excluded board(s)';
$txt['betterads_target_summary_groups'] = '%1$d membergroup(s)';


$txt['betterads_stats'] = 'Statistics';
$txt['betterads_impressions_short'] = 'impressions';
$txt['betterads_clicks_short'] = 'clicks';
$txt['betterads_ctr'] = 'CTR';
$txt['betterads_stats_help'] = 'Better Ads stores daily aggregate counts. Image/link ads use the built-in click redirect; raw third-party HTML/JavaScript clicks are not intercepted or counted by Better Ads.';


$txt['betterads_stats_button'] = 'Stats';
$txt['betterads_stats_detail_title'] = 'Statistics';
$txt['betterads_stats_detail_help'] = 'Summary ranges are calculated from Better Ads daily aggregate statistics. Today and rolling day ranges use UTC, matching the stored statistics day.';
$txt['betterads_stats_period'] = 'Period';
$txt['betterads_stats_today'] = 'Today';
$txt['betterads_stats_7_days'] = 'Last 7 Days';
$txt['betterads_stats_30_days'] = 'Last 30 Days';
$txt['betterads_stats_lifetime'] = 'Lifetime';
$txt['betterads_close_stats'] = 'Close Statistics';


$txt['betterads_archive'] = 'Archive';
$txt['betterads_restore'] = 'Restore';
$txt['betterads_archived'] = 'Archived';
$txt['betterads_archived_short'] = 'archived %s';
$txt['betterads_expired'] = 'Expired';
$txt['betterads_filter_current'] = 'Current';
$txt['betterads_filter_active'] = 'Active';
$txt['betterads_filter_scheduled'] = 'Scheduled';
$txt['betterads_filter_expired'] = 'Expired';
$txt['betterads_filter_disabled'] = 'Disabled';
$txt['betterads_filter_archived'] = 'Archived';
$txt['betterads_filter_all'] = 'All';
$txt['betterads_none_filter'] = 'No ad units match this status filter.';


$txt['betterads_label_position'] = 'Disclosure position';
$txt['betterads_label_position_help'] = 'Choose whether the advertisement disclosure appears above or below this ad.';
$txt['betterads_label_above'] = 'Above ad';
$txt['betterads_label_below'] = 'Below ad';
$txt['betterads_label_align'] = 'Disclosure alignment';
$txt['betterads_label_align_help'] = 'Match ad alignment follows an image ad\'s left, center, or right setting. Raw code ads fall back to centered.';
$txt['betterads_label_match'] = 'Match ad alignment';


$txt['betterads_advertise_admin_title'] = 'Advertise With Us';
$txt['betterads_advertise_admin_help'] = 'Optional advertiser-facing entry point. This first pass provides a public information page and contact/action button only; it does not take payments or automatically create ads.';
$txt['betterads_advertise_enabled'] = 'Public advertising page';
$txt['betterads_advertise_enabled_help'] = 'Enable the Advertise With Us page and add its link to the footer with Help, Terms, and Go Up.';
$txt['betterads_advertise_link_text'] = 'Menu link text';
$txt['betterads_advertise_link_text_help'] = 'Text shown in the footer when the public advertising page is enabled.';
$txt['betterads_advertise_page_title'] = 'Page title';
$txt['betterads_advertise_body'] = 'Page message';
$txt['betterads_advertise_body_help'] = 'Plain text introduction for potential advertisers. Line breaks are preserved.';
$txt['betterads_advertise_cta_text'] = 'Choose button text';
$txt['betterads_advertise_cta_url'] = 'Fallback inquiry URL';
$txt['betterads_advertise_cta_url_help'] = 'Used only when Better Contact\'s Contact Staff link is not visible. Use a full http(s) URL, a site-relative path, or an SMF query. Leave blank if you only want inquiry buttons when Contact Staff is available.';
$txt['betterads_advertise_preview_page'] = 'Preview Public Page';
$txt['betterads_advertise_error_url'] = 'Please enter a valid http(s), site-relative, or SMF query destination URL.';


$txt['betterads_advertise_options_title'] = 'Advertising options';
$txt['betterads_advertise_options_help'] = 'Advertising tiers are stored as Better Ads database records. Add as many tiers as you need, enable only the ones you want shown publicly, and use display order to control their sequence.';
$txt['betterads_advertise_option_number'] = 'Advertising option %d';
$txt['betterads_advertise_option_placement'] = 'Placement';
$txt['betterads_advertise_option_no_placement'] = 'Custom / not tied to a placement';
$txt['betterads_advertise_option_title'] = 'Public title';
$txt['betterads_advertise_option_price'] = 'Price / rate text';
$txt['betterads_advertise_option_price_example'] = 'Example: $25 / month';
$txt['betterads_advertise_option_description'] = 'Description';


$txt['betterads_inquiry_routing_help'] = '<strong>Inquiry routing:</strong> On the public page, Better Ads looks for a visible footer link to <em>Contact Staff</em> (<code>action=contactstaff</code>). If found, each Choose button uses that exact link. If not found, Better Ads uses the fallback inquiry URL below. The selected package title, placement, and price are appended as query parameters for future prefill/integration.';
$txt['betterads_inquiry_using_contact_staff'] = 'Advertising inquiries are handled through Contact Staff.';
$txt['betterads_inquiry_using_fallback'] = 'Advertising inquiries are handled through the configured inquiry destination.';
$txt['betterads_inquiry_unavailable'] = 'Advertising inquiry contact is currently unavailable. Please use the site contact information.';


// dev17 - structured advertising request workflow.
$txt['betterads_request_package_button'] = 'Request This Package';
$txt['betterads_request_page_title'] = 'Advertising Request';
$txt['betterads_request_intro'] = 'Tell us a little about your campaign. Submitting this form is a request only; it does not create an ad or take payment.';
$txt['betterads_request_name'] = 'Name';
$txt['betterads_request_email'] = 'Email';
$txt['betterads_request_company'] = 'Company / organization';
$txt['betterads_request_website'] = 'Destination website';
$txt['betterads_request_creative_url'] = 'Banner / creative URL';
$txt['betterads_request_creative_help'] = 'Optional fallback. If your artwork is already hosted online, paste its direct URL here. An uploaded graphic above takes priority.';
$txt['betterads_request_message'] = 'Campaign notes / questions';
$txt['betterads_request_submit'] = 'Submit Advertising Request';
$txt['betterads_request_cancel'] = 'Back to Advertising Options';
$txt['betterads_request_fix_errors'] = 'Please correct the following:';
$txt['betterads_request_error_name'] = 'Please enter your name.';
$txt['betterads_request_error_email'] = 'Please enter a valid email address.';
$txt['betterads_request_error_website'] = 'Destination website must be a valid http(s) URL.';
$txt['betterads_request_error_creative'] = 'Banner / creative URL must be a valid http(s) URL.';
$txt['betterads_request_success_title'] = 'Advertising request received.';
$txt['betterads_request_success_body'] = 'Your request has been added to the Better Ads review queue. An administrator can review the package, campaign details, and contact information before anything is approved or published.';
$txt['betterads_request_back_to_options'] = 'Back to Advertising Options';

$txt['betterads_requests_admin_title'] = 'Advertising Requests';
$txt['betterads_requests_admin_help'] = 'Review structured advertising requests submitted from the public package page. This pass intentionally keeps approval manual and does not take payment or create ad units automatically.';
$txt['betterads_requests_none'] = 'No advertising requests have been submitted yet.';
$txt['betterads_request_status_pending'] = 'Pending';
$txt['betterads_request_status_approved'] = 'Approved';
$txt['betterads_request_status_declined'] = 'Declined';
$txt['betterads_request_status_completed'] = 'Completed';
$txt['betterads_request_id'] = 'Request';
$txt['betterads_request_package'] = 'Package';
$txt['betterads_request_contact'] = 'Contact';
$txt['betterads_request_details'] = 'Campaign details';
$txt['betterads_request_status'] = 'Status';
$txt['betterads_request_open_website'] = 'Open destination website';
$txt['betterads_request_open_creative'] = 'Open creative';
$txt['betterads_request_view_notes'] = 'View notes';
$txt['betterads_request_update'] = 'Update';


// dev18 - DB-backed dynamic advertising tiers.
$txt['betterads_advertise_add_tier'] = 'Add Advertising Tier';
$txt['betterads_advertise_no_tiers'] = 'No advertising tiers exist yet. Add one to begin building the public package list.';
$txt['betterads_advertise_tier_number'] = 'Advertising Tier #%d';
$txt['betterads_advertise_sort_order'] = 'Display order';
$txt['betterads_advertise_sort_order_help'] = 'Lower numbers appear first on the public Advertise With Us page.';
$txt['betterads_delete_tier'] = 'Delete Tier';
$txt['betterads_advertise_delete_confirm'] = 'Delete this advertising tier? Existing advertising requests keep their snapshotted package details.';


// dev19 - Create Ad From Approved Request.
$txt['betterads_request_create_ad'] = 'Create Ad';
$txt['betterads_request_open_ad'] = 'Open Ad';
$txt['betterads_creating_from_request'] = 'Creating ad from Advertising Request #%d';
$txt['betterads_create_from_request_help'] = 'Better Ads has prefilled the package placement, campaign destination and supplied creative where possible. The new unit is deliberately disabled so you can review creative, targeting, schedule and disclosure settings before making it live. Saving this new ad marks the request Completed and links the request to the created ad unit.';


// dev20 - advertiser creative uploads.
$txt['betterads_request_creative_upload'] = 'Upload banner / creative';
$txt['betterads_request_creative_upload_help'] = 'Optional. Upload the actual ad graphic directly. Accepted formats: JPG, PNG, GIF, or WEBP up to 8 MB. If you also enter a creative URL, the uploaded graphic is used.';
$txt['betterads_request_error_creative_upload'] = 'The uploaded banner could not be accepted. Use a valid JPG, PNG, GIF, or WEBP image no larger than 8 MB.';


// dev21 - native themed ad cells.
$txt['betterads_native_sponsored'] = 'Sponsored';
$txt['betterads_native_advertisement'] = 'Advertisement';
$txt['betterads_native_support_forum'] = 'Support the forum';


// dev22 - Order & Payment foundation.
$txt['betterads_order_page_title'] = 'Advertising Order #%d';
$txt['betterads_order_heading'] = 'Advertising Order #%d';
$txt['betterads_order_request_status'] = 'Request status';
$txt['betterads_order_payment_status'] = 'Payment status';
$txt['betterads_order_placement'] = 'Placement';
$txt['betterads_order_advertiser'] = 'Advertiser';
$txt['betterads_order_destination'] = 'Open destination website';
$txt['betterads_order_creative'] = 'Creative';
$txt['betterads_order_no_creative'] = 'No creative has been supplied yet.';
$txt['betterads_order_waiting_approval_title'] = 'Awaiting approval';
$txt['betterads_order_waiting_approval'] = 'Your advertising request has been received and is waiting for an administrator to approve the campaign before payment is requested.';
$txt['betterads_order_declined_title'] = 'Request not approved';
$txt['betterads_order_declined'] = 'This advertising request was not approved. Use Contact Staff if you would like to discuss another option.';
$txt['betterads_order_paid_title'] = 'Payment received';
$txt['betterads_order_paid'] = 'This advertising order is marked paid.';
$txt['betterads_order_no_payment_title'] = 'No payment required';
$txt['betterads_order_no_payment'] = 'This order does not require payment.';
$txt['betterads_order_refunded_title'] = 'Payment refunded';
$txt['betterads_order_refunded'] = 'This order is marked refunded. Contact staff if you have questions about the campaign.';
$txt['betterads_order_payment_heading'] = 'Payment';
$txt['betterads_order_payment_intro'] = 'Once the campaign is approved, use the secure payment option below to complete the order.';
$txt['betterads_order_pay_now'] = 'Pay Securely';
$txt['betterads_order_payment_not_configured'] = 'Online payment is not configured for this package yet. Follow the payment instructions above or contact staff.';
$txt['betterads_order_payment_safety'] = 'Better Ads does not collect or store card details. Online payment opens the secure payment provider configured by the forum administrator.';
$txt['betterads_order_reference'] = 'Payment reference';
$txt['betterads_order_notes'] = 'Campaign notes';
$txt['betterads_order_view'] = 'View Order';

$txt['betterads_payment_status_unpaid'] = 'Unpaid';
$txt['betterads_payment_status_pending'] = 'Payment Pending';
$txt['betterads_payment_status_paid'] = 'Paid';
$txt['betterads_payment_status_waived'] = 'Waived';
$txt['betterads_payment_status_refunded'] = 'Refunded';
$txt['betterads_payment_reference_placeholder'] = 'Payment reference / transaction ID';

$txt['betterads_tier_payment_required'] = 'Payment required';
$txt['betterads_tier_payment_required_help'] = 'If unchecked, requests for this tier are created with payment waived.';
$txt['betterads_tier_payment_url'] = 'Secure payment URL';
$txt['betterads_tier_payment_url_help'] = 'Optional. Paste a hosted checkout/payment link from Stripe, PayPal, Square, or another provider. Better Ads never handles card details.';
$txt['betterads_tier_payment_instructions'] = 'Payment instructions';
$txt['betterads_tier_payment_instructions_help'] = 'Optional instructions shown on the order page, such as invoicing details or what happens after payment.';
$txt['betterads_payment_error_url'] = 'The payment URL must be a valid http(s), site-relative, or SMF query destination.';


// dev23 - admin request notifications.
$txt['betterads_notify_admin_email'] = 'New request email notifications';
$txt['betterads_notify_admin_email_help'] = 'Email active forum administrators whenever a new advertising request is submitted.';
$txt['betterads_pending_request_notice'] = '%d advertising request(s) are waiting for review.';


// dev23b - order page access hardening.
$txt['betterads_order_not_found'] = 'This advertising order could not be found. The order link may be invalid or no longer available.';
$txt['betterads_order_unavailable'] = 'Advertising orders are temporarily unavailable because Better Ads is disabled.';

// dev23c - approved payment call-to-action.
$txt['betterads_order_payment_approved'] = 'Your ad campaign has been approved, please pay now.';


// 1.1.0-dev1 - Better Gallery integration.
$txt['betterads_group_gallery'] = 'Better Gallery';
$txt['betterads_placement_gallery_index_top'] = 'Gallery Index — Top';
$txt['betterads_placement_gallery_index_bottom'] = 'Gallery Index — Bottom';
$txt['betterads_placement_gallery_album_top'] = 'Gallery Album — Top';
$txt['betterads_placement_gallery_album_bottom'] = 'Gallery Album — Bottom';
$txt['betterads_placement_gallery_album_repeat'] = 'Gallery Album — Repeat Between Items';


// 1.1.0-dev2 - Sponsored Gallery Card presentation.
$txt['betterads_gallery_repeat_mode'] = 'Gallery repeat presentation';
$txt['betterads_gallery_repeat_mode_row'] = 'Full-width sponsored row';
$txt['betterads_gallery_repeat_mode_card'] = 'Sponsored gallery card';
$txt['betterads_gallery_repeat_mode_help'] = 'For Gallery Album — Repeat Between Items, choose a full-width banner row or one native gallery-grid card.';
$txt['betterads_gallery_sponsored_card_title'] = 'Sponsored';
$txt['betterads_gallery_sponsored_card_note'] = 'Advertisement';


// 1.1.0-dev3 - Split Better Ads admin workspace.
$txt['betterads_admin_split_intro'] = 'Better Ads is now organized by job: sell advertising with Tiers, process advertiser Requests & Orders, and manage the Ads that actually run on the site.';
$txt['betterads_nav_dashboard'] = 'Dashboard';
$txt['betterads_nav_ads'] = 'Ads';
$txt['betterads_nav_tiers'] = 'Advertising Tiers';
$txt['betterads_nav_requests'] = 'Requests & Orders';
$txt['betterads_nav_stats'] = 'Statistics';
$txt['betterads_nav_settings'] = 'Settings';

$txt['betterads_dashboard_active_ads'] = 'Active ads';
$txt['betterads_dashboard_total_ads'] = 'Total ad units';
$txt['betterads_dashboard_tiers'] = 'Advertising tiers';
$txt['betterads_dashboard_pending'] = 'Pending requests';
$txt['betterads_dashboard_impressions'] = 'Lifetime impressions';
$txt['betterads_dashboard_ctr'] = 'Lifetime CTR';
$txt['betterads_dashboard_workflow'] = 'Advertising workflow';
$txt['betterads_dashboard_tier_label'] = 'Tier';
$txt['betterads_dashboard_tier_help'] = 'What you sell';
$txt['betterads_dashboard_request_label'] = 'Request / Order';
$txt['betterads_dashboard_request_help'] = 'What the advertiser buys';
$txt['betterads_dashboard_ad_label'] = 'Ad';
$txt['betterads_dashboard_ad_help'] = 'What actually runs';

$txt['betterads_tiers_page_help'] = 'Create and manage the advertising packages shown to potential advertisers. A tier defines the placement, price, payment details, and public description; it does not create a live ad.';
$txt['betterads_stats_page_help'] = 'Campaign performance at a glance. Open Details for the existing Today, 7 day, 30 day, and Lifetime breakdown.';
$txt['betterads_stats_detail_button'] = 'Details';


// 1.1.0-dev4 - Requests & Orders workflow polish.
$txt['betterads_pipeline_pending'] = 'Pending review';
$txt['betterads_pipeline_pending_help'] = 'Needs an approval decision';
$txt['betterads_pipeline_awaiting_payment'] = 'Awaiting payment';
$txt['betterads_pipeline_awaiting_payment_help'] = 'Approved, payment not cleared';
$txt['betterads_pipeline_ready'] = 'Ready to create';
$txt['betterads_pipeline_ready_help'] = 'Approved and payment cleared';
$txt['betterads_pipeline_completed'] = 'Completed';
$txt['betterads_pipeline_completed_help'] = 'Ad created or request completed';

$txt['betterads_request_workflow_label'] = 'Workflow';
$txt['betterads_request_workflow_text'] = 'Review → Approve → Payment → Create Ad → Completed';
$txt['betterads_next_step'] = 'Next step';
$txt['betterads_next_review'] = 'Review request';
$txt['betterads_next_review_help'] = 'Approve or decline this advertising request.';
$txt['betterads_next_payment'] = 'Await payment';
$txt['betterads_next_payment_help'] = 'The request is approved. Clear payment before creating the campaign.';
$txt['betterads_next_create'] = 'Create campaign';
$txt['betterads_next_create_help'] = 'Approval and payment are ready. Create the ad from this request.';
$txt['betterads_next_complete'] = 'Campaign created';
$txt['betterads_next_complete_help'] = 'This request has reached the live-ad workflow.';
$txt['betterads_next_declined'] = 'No action required';
$txt['betterads_next_declined_help'] = 'This request is declined unless you choose to reopen it.';
$txt['betterads_request_waiting_payment'] = 'Waiting for payment';
$txt['betterads_payment_reference_label'] = 'Payment reference';


// 1.1.0-dev5 - Statistics polish.
$txt['betterads_stats_total_impressions'] = 'Lifetime impressions';
$txt['betterads_stats_total_clicks'] = 'Lifetime clicks';
$txt['betterads_stats_overall_ctr'] = 'Overall CTR';
$txt['betterads_stats_campaigns_active'] = 'Campaigns with activity';
$txt['betterads_stats_campaign_performance'] = 'Campaign performance';
$txt['betterads_stats_edit_ad'] = 'Edit Ad';
$txt['betterads_stats_no_activity'] = 'No tracked activity yet';


// 1.1.0-dev6 - Advertiser campaign dashboard.
$txt['betterads_advertiser_performance_title'] = 'Campaign Performance';
$txt['betterads_advertiser_performance_intro'] = 'Your private campaign report shows how this advertisement is performing. Statistics update automatically as impressions and tracked clicks are recorded.';
$txt['betterads_advertiser_campaign_status'] = 'Campaign status';
$txt['betterads_advertiser_campaign_placement'] = 'Placement';
$txt['betterads_advertiser_stats_updated'] = 'Stats updated through';
$txt['betterads_advertiser_no_activity_yet'] = 'No activity recorded yet';
$txt['betterads_advertiser_live_creative'] = 'Current campaign creative';
$txt['betterads_advertiser_private_note'] = 'This is a private, read-only campaign report. Keep this order link private because it provides access to your advertising status and performance statistics.';

$txt['betterads_campaign_status_active'] = 'Active';
$txt['betterads_campaign_status_scheduled'] = 'Scheduled';
$txt['betterads_campaign_status_expired'] = 'Expired';
$txt['betterads_campaign_status_disabled'] = 'Paused';
$txt['betterads_campaign_status_needs_content'] = 'Awaiting creative';
$txt['betterads_campaign_status_archived'] = 'Archived';


// 1.1.0-dev7 - Campaign lifecycle and renewals.
$txt['betterads_tier_campaign_days'] = 'Campaign duration (days)';
$txt['betterads_tier_campaign_days_help'] = 'The default campaign length sold by this tier. New campaigns use this schedule, and renewals extend the same ad by this many days.';
$txt['betterads_public_campaign_duration'] = '%d day campaign';
$txt['betterads_campaign_duration_days'] = 'Campaign duration: %d days';

$txt['betterads_lifecycle_heading'] = 'Campaign lifecycle';
$txt['betterads_lifecycle_running'] = 'Running';
$txt['betterads_lifecycle_scheduled'] = 'Scheduled';
$txt['betterads_lifecycle_ending_soon'] = 'Ending within 7 days';
$txt['betterads_lifecycle_expired'] = 'Expired';

$txt['betterads_campaign_ends'] = 'Campaign ends';
$txt['betterads_campaign_time_remaining'] = 'Time remaining';
$txt['betterads_campaign_days_remaining'] = '%d days';
$txt['betterads_campaign_expired'] = 'Expired';

$txt['betterads_renew_campaign_title'] = 'Renew this campaign';
$txt['betterads_renew_campaign_help'] = 'This campaign is near its end. Request another %d days using the same package and campaign.';
$txt['betterads_renew_campaign_button'] = 'Request Renewal';
$txt['betterads_renewal_in_progress_title'] = 'Renewal in progress';
$txt['betterads_renewal_in_progress'] = 'A renewal request already exists for this campaign.';
$txt['betterads_view_renewal_order'] = 'View Renewal Order';
$txt['betterads_renewal_of_request'] = 'Renewal of request #%d';
$txt['betterads_apply_renewal'] = 'Apply Renewal';
$txt['betterads_next_apply_renewal'] = 'Apply renewal';
$txt['betterads_next_apply_renewal_help'] = 'Approval and payment are ready. Extend the existing campaign without creating a duplicate ad.';

$txt['betterads_renew_unavailable'] = 'This campaign is not currently eligible for renewal.';
$txt['betterads_renew_too_early'] = 'Renewals become available during the final 30 days of the campaign.';


// 1.1.0-dev8 - Advertiser transactional email notifications.
$txt['betterads_advertiser_email_notifications'] = 'Advertiser email notifications';
$txt['betterads_advertiser_email_notifications_help'] = 'Better Ads automatically emails the advertiser when a request is received, approved or declined, when payment is confirmed, when a campaign is created, and when a renewal is applied. Every message includes the private order/campaign link.';


// Better Ads 1.1.0 stable - public advertiser experience polish.
$txt['betterads_public_trust_updates'] = 'Email updates at every major step';
$txt['betterads_public_trust_dashboard'] = 'Private campaign performance dashboard';
$txt['betterads_public_trust_renewal'] = 'Simple renewal when your campaign is ending';
$txt['betterads_public_how_it_works'] = 'How advertising works';
$txt['betterads_public_step_choose'] = 'Choose';
$txt['betterads_public_step_choose_help'] = 'Pick the placement and campaign package that fits.';
$txt['betterads_public_step_request'] = 'Request';
$txt['betterads_public_step_request_help'] = 'Send your contact, destination and creative details.';
$txt['betterads_public_step_approve'] = 'Approve & Pay';
$txt['betterads_public_step_approve_help'] = 'We review the request and confirm payment when required.';
$txt['betterads_public_step_run'] = 'Campaign';
$txt['betterads_public_step_run_help'] = 'Your approved advertisement is prepared and scheduled.';
$txt['betterads_public_step_track'] = 'Track & Renew';
$txt['betterads_public_step_track_help'] = 'Use your private link for performance and renewal.';
$txt['betterads_public_available_packages'] = 'Available advertising packages';
$txt['betterads_public_available_packages_help'] = 'Choose a package below to start a request. You are not charged simply for submitting a request.';
$txt['betterads_public_rate'] = 'Rate';
$txt['betterads_public_duration'] = 'Duration';
$txt['betterads_public_includes_review'] = 'Request reviewed before campaign setup';
$txt['betterads_public_includes_payment'] = 'Payment requested after approval';
$txt['betterads_public_includes_no_payment'] = 'No payment required for this package';
$txt['betterads_public_includes_reporting'] = 'Private campaign status and performance reporting';
$txt['betterads_public_no_packages'] = 'Advertising is available, but there are no public packages listed right now. Use the contact option below for availability.';
$txt['betterads_public_what_to_expect'] = 'Know what to expect';
$txt['betterads_public_what_to_expect_help'] = 'Better Ads emails you as your request moves through review, payment, campaign creation and renewal. Your private order link stays with you through the entire campaign.';
$txt['betterads_request_what_happens_next'] = 'What happens next';
$txt['betterads_request_next_review'] = 'We review your advertising request and email you when its status changes.';
$txt['betterads_request_next_payment'] = 'If approved, your private order page provides the payment step.';
$txt['betterads_request_next_no_payment'] = 'If approved, the campaign can move directly into setup.';
$txt['betterads_request_next_dashboard'] = 'When the campaign is created, the same private link becomes your performance dashboard.';
