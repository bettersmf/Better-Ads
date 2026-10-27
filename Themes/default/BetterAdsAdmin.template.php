<?php
if (!defined('SMF')) die('No direct access.');

function template_betterads_admin_nav()
{
    global $context, $txt, $scripturl;

    $section = $context['betterads_admin_section'] ?? 'dashboard';
    $items = array(
        'dashboard' => $txt['betterads_nav_dashboard'],
        'ads' => $txt['betterads_nav_ads'],
        'tiers' => $txt['betterads_nav_tiers'],
        'requests' => $txt['betterads_nav_requests'],
        'stats' => $txt['betterads_nav_stats'],
        'settings' => $txt['betterads_nav_settings'],
    );

    echo '<div class="betterads-admin-nav">';
    foreach ($items as $key => $label)
    {
        $badge = '';
        if ($key === 'requests' && !empty($context['betterads_request_counts']['pending']))
            $badge = '<span class="betterads-nav-badge">' . (int)$context['betterads_request_counts']['pending'] . '</span>';

        echo '<a class="button', $section === $key ? ' active' : '', '" href="', $scripturl, '?action=admin;area=betterads;sa=', $key, '">',
            htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8'), $badge, '</a>';
    }
    echo '</div>';
}

function template_betterads_admin()
{
    global $context, $txt, $scripturl;

    $edit = $context['betterads_edit'];
    $section = $context['betterads_admin_section'] ?? 'dashboard';

    echo '<div class="cat_bar"><h3 class="catbg">', $txt['betterads_admin_title'], '</h3></div>';
    echo '<div class="windowbg betterads-admin-shell"><p>', $txt['betterads_admin_split_intro'], '</p>';
    template_betterads_admin_nav();
    echo '</div>';

    if ($section === 'dashboard')
    {
        $total_ads = count($context['betterads_units'] ?? array());
        $active_ads = (int)($context['betterads_filter_counts']['active'] ?? 0);
        $pending_requests = (int)($context['betterads_request_counts']['pending'] ?? 0);
        $tiers = count($context['betterads_advertise_offers_admin'] ?? array());

        $impressions = 0;
        $clicks = 0;
        foreach (($context['betterads_stats'] ?? array()) as $row)
        {
            $impressions += (int)($row['impressions'] ?? 0);
            $clicks += (int)($row['clicks'] ?? 0);
        }
        $ctr = $impressions > 0 ? ($clicks / $impressions) * 100 : 0;

        echo '<div class="betterads-dashboard-grid">';
        $cards = array(
            array($txt['betterads_dashboard_active_ads'], $active_ads, 'ads'),
            array($txt['betterads_dashboard_total_ads'], $total_ads, 'ads'),
            array($txt['betterads_dashboard_tiers'], $tiers, 'tiers'),
            array($txt['betterads_dashboard_pending'], $pending_requests, 'requests'),
            array($txt['betterads_dashboard_impressions'], number_format($impressions), 'stats'),
            array($txt['betterads_dashboard_ctr'], number_format($ctr, 2) . '%', 'stats'),
        );
        foreach ($cards as $card)
            echo '<a class="windowbg betterads-dashboard-card" href="', $scripturl, '?action=admin;area=betterads;sa=', $card[2], '"><strong>', $card[1], '</strong><span>', htmlspecialchars((string)$card[0], ENT_QUOTES, 'UTF-8'), '</span></a>';
        echo '</div>';

        $lifecycle = $context['betterads_lifecycle_counts'] ?? array('running'=>0,'scheduled'=>0,'ending_soon'=>0,'expired'=>0);
        echo '<div class="cat_bar"><h3 class="catbg">', $txt['betterads_lifecycle_heading'], '</h3></div>';
        echo '<div class="betterads-lifecycle-grid">';
        $life_cards = array(
            array($txt['betterads_lifecycle_running'], (int)$lifecycle['running']),
            array($txt['betterads_lifecycle_scheduled'], (int)$lifecycle['scheduled']),
            array($txt['betterads_lifecycle_ending_soon'], (int)$lifecycle['ending_soon']),
            array($txt['betterads_lifecycle_expired'], (int)$lifecycle['expired']),
        );
        foreach ($life_cards as $life_card)
            echo '<div class="windowbg betterads-lifecycle-card"><strong>', $life_card[1], '</strong><span>', htmlspecialchars((string)$life_card[0], ENT_QUOTES, 'UTF-8'), '</span></div>';
        echo '</div>';

        echo '<div class="cat_bar"><h3 class="catbg">', $txt['betterads_dashboard_workflow'], '</h3></div>';
        echo '<div class="windowbg betterads-dashboard-workflow"><div><strong>', $txt['betterads_dashboard_tier_label'], '</strong><span>', $txt['betterads_dashboard_tier_help'], '</span></div><span class="betterads-workflow-arrow">→</span><div><strong>', $txt['betterads_dashboard_request_label'], '</strong><span>', $txt['betterads_dashboard_request_help'], '</span></div><span class="betterads-workflow-arrow">→</span><div><strong>', $txt['betterads_dashboard_ad_label'], '</strong><span>', $txt['betterads_dashboard_ad_help'], '</span></div></div>';
        return;
    }

    if ($section === 'settings')
    {
    echo '<div class="cat_bar"><h3 class="catbg">', $txt['betterads_admin_title'], '</h3></div>';
    echo '<div class="windowbg"><p>', $txt['betterads_admin_intro'], '</p>';
    echo '<form class="betterads-admin-form" action="', $scripturl, '?action=admin;area=betterads;sa=settings" method="post"><dl class="settings">';
    echo '<dt>', $txt['betterads_enabled'], '</dt><dd><label><input type="checkbox" name="betterads_enabled" value="1"', $context['betterads_enabled'] ? ' checked' : '', '> ', $txt['betterads_enabled_help'], '</label></dd>';
    echo '<dt>', $txt['betterads_show_label'], '</dt><dd><label><input type="checkbox" name="betterads_show_label" value="1"', $context['betterads_show_label'] ? ' checked' : '', '> ', $txt['betterads_show_label_help'], '</label></dd>';
    echo '<dt><label for="ba_label_text">', $txt['betterads_label_text'], '</label></dt><dd><input id="ba_label_text" type="text" name="betterads_label_text" maxlength="80" value="', htmlspecialchars((string)$context['betterads_label_text'], ENT_QUOTES, 'UTF-8'), '" style="width:100%;max-width:360px"><div class="smalltext">', $txt['betterads_label_text_help'], '</div></dd>';
    echo '</dl><div class="righttext"><button class="button" type="submit" name="save_settings">', $txt['save'], '</button></div><input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '"></form></div>';

    echo '<div class="cat_bar"><h3 class="catbg">', $txt['betterads_advertise_admin_title'], '</h3></div>';
    echo '<div class="windowbg"><p>', $txt['betterads_advertise_admin_help'], '</p>';
    echo '<div class="information betterads-inquiry-routing-note">', $txt['betterads_inquiry_routing_help'], '</div>';
    echo '<form class="betterads-admin-form" action="', $scripturl, '?action=admin;area=betterads;sa=settings" method="post"><dl class="settings">';

    echo '<dt>', $txt['betterads_advertise_enabled'], '</dt><dd><label><input type="checkbox" name="betterads_advertise_enabled" value="1"', $context['betterads_advertise_enabled'] ? ' checked' : '', '> ', $txt['betterads_advertise_enabled_help'], '</label></dd>';
    echo '<dt><label for="ba_adv_link_text">', $txt['betterads_advertise_link_text'], '</label></dt><dd><input id="ba_adv_link_text" type="text" name="betterads_advertise_link_text" maxlength="80" value="', htmlspecialchars((string)$context['betterads_advertise_link_text'], ENT_QUOTES, 'UTF-8'), '" style="width:100%;max-width:420px"><div class="smalltext">', $txt['betterads_advertise_link_text_help'], '</div></dd>';
    echo '<dt><label for="ba_adv_title">', $txt['betterads_advertise_page_title'], '</label></dt><dd><input id="ba_adv_title" type="text" name="betterads_advertise_title" maxlength="160" value="', htmlspecialchars((string)$context['betterads_advertise_title'], ENT_QUOTES, 'UTF-8'), '" style="width:100%;max-width:560px"></dd>';
    echo '<dt><label for="ba_adv_body">', $txt['betterads_advertise_body'], '</label></dt><dd><textarea id="ba_adv_body" name="betterads_advertise_body" rows="6" style="width:100%;max-width:760px">', htmlspecialchars((string)$context['betterads_advertise_body'], ENT_QUOTES, 'UTF-8'), '</textarea><div class="smalltext">', $txt['betterads_advertise_body_help'], '</div></dd>';
    echo '<dt><label for="ba_adv_cta_text">', $txt['betterads_advertise_cta_text'], '</label></dt><dd><input id="ba_adv_cta_text" type="text" name="betterads_advertise_cta_text" maxlength="80" value="', htmlspecialchars((string)$context['betterads_advertise_cta_text'], ENT_QUOTES, 'UTF-8'), '" style="width:100%;max-width:360px"></dd>';
    echo '<dt><label for="ba_adv_cta_url">', $txt['betterads_advertise_cta_url'], '</label></dt><dd><input id="ba_adv_cta_url" type="text" name="betterads_advertise_cta_url" maxlength="500" value="', htmlspecialchars((string)$context['betterads_advertise_cta_url'], ENT_QUOTES, 'UTF-8'), '" style="width:100%;max-width:760px"><div class="smalltext">', $txt['betterads_advertise_cta_url_help'], '</div></dd>';
    echo '<dt>', $txt['betterads_notify_admin_email'], '</dt><dd><label><input type="checkbox" name="betterads_notify_admin_email" value="1"', !empty($context['betterads_notify_admin_email']) ? ' checked' : '', '> ', $txt['betterads_notify_admin_email_help'], '</label></dd>';
    echo '</dl>';
    echo '<div class="righttext betterads-advertise-admin-actions"><a class="button" href="', $scripturl, '?action=betterads;sa=advertise" target="_blank" rel="noopener">', $txt['betterads_advertise_preview_page'], '</a><button class="button" type="submit" name="save_advertise_settings">', $txt['save'], '</button></div><input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '"></form>';


        return;
    }

    if ($section === 'tiers')
    {
        echo '<div class="cat_bar"><h3 class="catbg">', $txt['betterads_nav_tiers'], '</h3></div>';
        echo '<div class="windowbg"><p>', $txt['betterads_tiers_page_help'], '</p></div>';
    echo '<div id="betterads-advertising-tiers" class="betterads-advertise-offers-admin">';
    echo '<div class="betterads-tier-heading"><div><h4>', $txt['betterads_advertise_options_title'], '</h4><p class="smalltext">', $txt['betterads_advertise_options_help'], '</p></div>';
    echo '<form action="', $scripturl, '?action=admin;area=betterads;sa=tiers" method="post"><button class="button" type="submit" name="add_advertise_offer" value="1">', $txt['betterads_advertise_add_tier'], '</button><input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '"></form></div>';

    if (empty($context['betterads_advertise_offers_admin']))
        echo '<div class="information">', $txt['betterads_advertise_no_tiers'], '</div>';
    else
    {
        foreach ($context['betterads_advertise_offers_admin'] as $offer)
        {
            $id_offer = (int)$offer['id_offer'];
            echo '<form class="betterads-advertise-offer-admin" action="', $scripturl, '?action=admin;area=betterads;sa=tiers" method="post">';
            echo '<fieldset><legend>', sprintf($txt['betterads_advertise_tier_number'], $id_offer), '</legend>';
            echo '<label class="betterads-advertise-offer-toggle"><input type="checkbox" name="offer_enabled" value="1"', !empty($offer['enabled']) ? ' checked' : '', '> ', $txt['betterads_enabled'], '</label>';
            echo '<div class="betterads-advertise-offer-grid">';

            echo '<label>', $txt['betterads_advertise_option_placement'], '<select name="offer_placement"><option value="">', $txt['betterads_advertise_option_no_placement'], '</option>';
            foreach ($context['betterads_advertise_offer_placements'] as $key => $label)
                echo '<option value="', htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8'), '"', $offer['placement'] === $key ? ' selected' : '', '>', htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8'), '</option>';
            echo '</select></label>';

            echo '<label>', $txt['betterads_advertise_option_title'], '<input type="text" maxlength="160" name="offer_title" value="', htmlspecialchars((string)$offer['title'], ENT_QUOTES, 'UTF-8'), '"></label>';
            echo '<label>', $txt['betterads_advertise_option_price'], '<input type="text" maxlength="80" name="offer_price" value="', htmlspecialchars((string)$offer['price'], ENT_QUOTES, 'UTF-8'), '" placeholder="', htmlspecialchars($txt['betterads_advertise_option_price_example'], ENT_QUOTES, 'UTF-8'), '"></label>';
            echo '<label>', $txt['betterads_tier_campaign_days'], '<input type="number" min="1" max="3650" step="1" name="offer_campaign_days" value="', (int)($offer['campaign_days'] ?? 30), '"><span class="smalltext">', $txt['betterads_tier_campaign_days_help'], '</span></label>';
            echo '<label>', $txt['betterads_advertise_sort_order'], '<input type="number" min="0" max="65535" step="1" name="offer_sort_order" value="', (int)$offer['sort_order'], '"><span class="smalltext">', $txt['betterads_advertise_sort_order_help'], '</span></label>';
            echo '<label class="betterads-advertise-offer-description">', $txt['betterads_advertise_option_description'], '<textarea rows="3" maxlength="1000" name="offer_description">', htmlspecialchars((string)$offer['description'], ENT_QUOTES, 'UTF-8'), '</textarea></label>';

            echo '<label class="betterads-advertise-offer-toggle betterads-payment-required"><input type="checkbox" name="offer_payment_required" value="1"', !empty($offer['payment_required']) ? ' checked' : '', '> ', $txt['betterads_tier_payment_required'], '<span class="smalltext">', $txt['betterads_tier_payment_required_help'], '</span></label>';
            echo '<label class="betterads-advertise-offer-description">', $txt['betterads_tier_payment_url'], '<input type="text" maxlength="500" name="offer_payment_url" value="', htmlspecialchars((string)$offer['payment_url'], ENT_QUOTES, 'UTF-8'), '" placeholder="https://"><span class="smalltext">', $txt['betterads_tier_payment_url_help'], '</span></label>';
            echo '<label class="betterads-advertise-offer-description">', $txt['betterads_tier_payment_instructions'], '<textarea rows="3" maxlength="2000" name="offer_payment_instructions">', htmlspecialchars((string)$offer['payment_instructions'], ENT_QUOTES, 'UTF-8'), '</textarea><span class="smalltext">', $txt['betterads_tier_payment_instructions_help'], '</span></label>';

            echo '</div>';
            echo '<div class="betterads-tier-actions"><button class="button" type="submit" name="delete_advertise_offer" value="1" onclick="return confirm(', json_encode($txt['betterads_advertise_delete_confirm']), ');">', $txt['betterads_delete_tier'], '</button><button class="button" type="submit" name="save_advertise_offer" value="1">', $txt['save'], '</button></div>';
            echo '<input type="hidden" name="id_offer" value="', $id_offer, '"><input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">';
            echo '</fieldset></form>';
        }
    }

    echo '</div></div>';


        return;
    }

    if ($section === 'requests')
    {
        $request_rows = $context['betterads_requests'] ?? array();

        $pipeline = array(
            'pending' => 0,
            'awaiting_payment' => 0,
            'ready' => 0,
            'completed' => 0,
        );

        foreach ($request_rows as $request)
        {
            $status = (string)($request['status'] ?? 'pending');
            $payment_status = (string)($request['payment_status'] ?? 'unpaid');
            $payment_required = !empty($request['payment_required']);
            $is_renewal = !empty($request['renewal_of_request']);
            $has_ad = !empty($request['id_unit']) && !$is_renewal;

            if ($status === 'pending')
                $pipeline['pending']++;
            elseif ($status === 'completed' || $has_ad)
                $pipeline['completed']++;
            elseif ($status === 'approved')
            {
                if ($payment_required && !in_array($payment_status, array('paid', 'waived'), true))
                    $pipeline['awaiting_payment']++;
                else
                    $pipeline['ready']++;
            }
        }

        echo '<div class="betterads-pipeline-summary">';
        $pipeline_cards = array(
            array('pending', $txt['betterads_pipeline_pending'], $txt['betterads_pipeline_pending_help']),
            array('awaiting_payment', $txt['betterads_pipeline_awaiting_payment'], $txt['betterads_pipeline_awaiting_payment_help']),
            array('ready', $txt['betterads_pipeline_ready'], $txt['betterads_pipeline_ready_help']),
            array('completed', $txt['betterads_pipeline_completed'], $txt['betterads_pipeline_completed_help']),
        );
        foreach ($pipeline_cards as $card)
            echo '<div class="windowbg betterads-pipeline-card"><strong>', (int)$pipeline[$card[0]], '</strong><span>', htmlspecialchars((string)$card[1], ENT_QUOTES, 'UTF-8'), '</span><small>', htmlspecialchars((string)$card[2], ENT_QUOTES, 'UTF-8'), '</small></div>';
        echo '</div>';

        echo '<div class="betterads-workflow-legend windowbg"><strong>', $txt['betterads_request_workflow_label'], '</strong><span>', $txt['betterads_request_workflow_text'], '</span></div>';

        if (empty($request_rows))
        {
            echo '<div class="information">', $txt['betterads_requests_none'], '</div>';
            return;
        }

        echo '<div class="betterads-request-card-list">';

        foreach ($request_rows as $request)
        {
            $id_request = (int)$request['id_request'];
            $status = (string)($request['status'] ?? 'pending');
            $payment_status = (string)($request['payment_status'] ?? 'unpaid');
            $payment_required = !empty($request['payment_required']);
            $is_renewal = !empty($request['renewal_of_request']);
            $has_ad = !empty($request['id_unit']) && !$is_renewal;
            $payment_cleared = !$payment_required || in_array($payment_status, array('paid', 'waived'), true);

            $status_label = $context['betterads_request_statuses'][$status] ?? ucfirst($status);
            $payment_label = $context['betterads_payment_statuses'][$payment_status] ?? ucfirst($payment_status);

            if ($status === 'completed')
            {
                $next_title = $txt['betterads_next_complete'];
                $next_help = $txt['betterads_next_complete_help'];
                $next_class = 'complete';
            }
            elseif ($status === 'declined')
            {
                $next_title = $txt['betterads_next_declined'];
                $next_help = $txt['betterads_next_declined_help'];
                $next_class = 'declined';
            }
            elseif ($status === 'pending')
            {
                $next_title = $txt['betterads_next_review'];
                $next_help = $txt['betterads_next_review_help'];
                $next_class = 'review';
            }
            elseif ($status === 'approved' && !$payment_cleared)
            {
                $next_title = $txt['betterads_next_payment'];
                $next_help = $txt['betterads_next_payment_help'];
                $next_class = 'payment';
            }
            else
            {
                $next_title = $is_renewal ? $txt['betterads_next_apply_renewal'] : $txt['betterads_next_create'];
                $next_help = $is_renewal ? $txt['betterads_next_apply_renewal_help'] : $txt['betterads_next_create_help'];
                $next_class = 'ready';
            }

            echo '<article class="windowbg betterads-request-card">';
            echo '<div class="betterads-request-card-head">';
            echo '<div><strong class="betterads-request-number">#', $id_request, '</strong><span class="betterads-request-date">', timeformat((int)$request['created_at']), '</span></div>';
            echo '<div class="betterads-request-pills">';
            echo '<span class="betterads-status-pill betterads-status-', htmlspecialchars($status, ENT_QUOTES, 'UTF-8'), '">', htmlspecialchars((string)$status_label, ENT_QUOTES, 'UTF-8'), '</span>';
            echo '<span class="betterads-status-pill betterads-payment-', htmlspecialchars($payment_status, ENT_QUOTES, 'UTF-8'), '">', htmlspecialchars((string)$payment_label, ENT_QUOTES, 'UTF-8'), '</span>';
            echo '</div></div>';

            echo '<div class="betterads-request-card-grid">';

            echo '<section><h4>', $txt['betterads_request_package'], '</h4>';
            if ($is_renewal)
                echo '<div class="betterads-renewal-badge">', sprintf($txt['betterads_renewal_of_request'], (int)$request['renewal_of_request']), '</div>';
            echo '<strong>', htmlspecialchars((string)$request['offer_title'], ENT_QUOTES, 'UTF-8'), '</strong>';
            echo '<div class="smalltext">', sprintf($txt['betterads_campaign_duration_days'], (int)($request['campaign_days'] ?? 30)), '</div>';
            if (!empty($request['price_text']))
                echo '<div>', htmlspecialchars((string)$request['price_text'], ENT_QUOTES, 'UTF-8'), '</div>';
            if (!empty($request['placement_key']))
                echo '<div class="smalltext">', htmlspecialchars((string)$request['placement_key'], ENT_QUOTES, 'UTF-8'), '</div>';
            echo '</section>';

            echo '<section><h4>', $txt['betterads_request_contact'], '</h4>';
            echo '<strong>', htmlspecialchars((string)$request['contact_name'], ENT_QUOTES, 'UTF-8'), '</strong>';
            echo '<div><a href="mailto:', htmlspecialchars((string)$request['contact_email'], ENT_QUOTES, 'UTF-8'), '">', htmlspecialchars((string)$request['contact_email'], ENT_QUOTES, 'UTF-8'), '</a></div>';
            if (!empty($request['company']))
                echo '<div class="smalltext">', htmlspecialchars((string)$request['company'], ENT_QUOTES, 'UTF-8'), '</div>';
            echo '</section>';

            echo '<section><h4>', $txt['betterads_request_details'], '</h4>';
            if (!empty($request['website_url']))
                echo '<div><a href="', htmlspecialchars((string)$request['website_url'], ENT_QUOTES, 'UTF-8'), '" target="_blank" rel="noopener">', $txt['betterads_request_open_website'], '</a></div>';
            if (!empty($request['creative_src']))
            {
                echo '<div class="betterads-request-creative"><a href="', htmlspecialchars((string)$request['creative_src'], ENT_QUOTES, 'UTF-8'), '" target="_blank" rel="noopener">', $txt['betterads_request_open_creative'], '</a>';
                if (!empty($request['creative_is_local']))
                    echo '<div><a href="', htmlspecialchars((string)$request['creative_src'], ENT_QUOTES, 'UTF-8'), '" target="_blank" rel="noopener"><img src="', htmlspecialchars((string)$request['creative_src'], ENT_QUOTES, 'UTF-8'), '" alt="" loading="lazy"></a></div>';
                echo '</div>';
            }
            if (!empty($request['message']))
                echo '<details class="betterads-request-notes"><summary>', $txt['betterads_request_view_notes'], '</summary><div>', nl2br(htmlspecialchars((string)$request['message'], ENT_QUOTES, 'UTF-8')), '</div></details>';
            echo '</section>';

            echo '<section class="betterads-request-next betterads-next-', $next_class, '"><h4>', $txt['betterads_next_step'], '</h4><strong>', htmlspecialchars((string)$next_title, ENT_QUOTES, 'UTF-8'), '</strong><span>', htmlspecialchars((string)$next_help, ENT_QUOTES, 'UTF-8'), '</span></section>';

            echo '</div>';

            echo '<div class="betterads-request-controls">';
            echo '<form action="', $scripturl, '?action=admin;area=betterads;sa=requests" method="post" class="betterads-request-status-form">';
            echo '<label><span>', $txt['betterads_request_status'], '</span><select name="request_status">';
            foreach ($context['betterads_request_statuses'] as $status_key => $status_option_label)
                echo '<option value="', htmlspecialchars((string)$status_key, ENT_QUOTES, 'UTF-8'), '"', $status === $status_key ? ' selected' : '', '>', htmlspecialchars((string)$status_option_label, ENT_QUOTES, 'UTF-8'), '</option>';
            echo '</select></label>';

            echo '<label><span>', $txt['betterads_order_payment_status'], '</span><select name="payment_status">';
            foreach ($context['betterads_payment_statuses'] as $payment_key => $payment_option_label)
                echo '<option value="', htmlspecialchars((string)$payment_key, ENT_QUOTES, 'UTF-8'), '"', $payment_status === $payment_key ? ' selected' : '', '>', htmlspecialchars((string)$payment_option_label, ENT_QUOTES, 'UTF-8'), '</option>';
            echo '</select></label>';

            echo '<label class="betterads-payment-reference"><span>', $txt['betterads_payment_reference_label'], '</span><input type="text" name="payment_reference" maxlength="160" value="', htmlspecialchars((string)$request['payment_reference'], ENT_QUOTES, 'UTF-8'), '" placeholder="', htmlspecialchars($txt['betterads_payment_reference_placeholder'], ENT_QUOTES, 'UTF-8'), '"></label>';
            echo '<input type="hidden" name="id_request" value="', $id_request, '">';
            echo '<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">';
            echo '<button class="button" type="submit" name="update_ad_request" value="1">', $txt['betterads_request_update'], '</button>';
            echo '</form>';

            echo '<div class="betterads-request-actions">';
            if (!empty($request['order_token']))
                echo '<a class="button" href="', $scripturl, '?action=betterads;sa=order;token=', urlencode((string)$request['order_token']), '" target="_blank" rel="noopener">', $txt['betterads_order_view'], '</a>';

            if ($is_renewal && !empty($request['id_unit']))
                echo '<a class="button" href="', $scripturl, '?action=admin;area=betterads;sa=ads;edit=', (int)$request['id_unit'], '">', $txt['betterads_request_open_ad'], '</a>';

            if ($is_renewal && $status === 'approved' && $payment_cleared)
            {
                echo '<form action="', $scripturl, '?action=admin;area=betterads;sa=requests" method="post" class="betterads-inline-renew-form">';
                echo '<input type="hidden" name="id_request" value="', $id_request, '">';
                echo '<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">';
                echo '<button class="button" type="submit" name="apply_ad_renewal" value="1">', $txt['betterads_apply_renewal'], '</button>';
                echo '</form>';
            }
            elseif (!$is_renewal && $has_ad)
                echo '<a class="button" href="', $scripturl, '?action=admin;area=betterads;sa=ads;edit=', (int)$request['id_unit'], '">', $txt['betterads_request_open_ad'], '</a>';
            elseif (!$is_renewal && $status === 'approved' && $payment_cleared)
                echo '<a class="button" href="', $scripturl, '?action=admin;area=betterads;sa=ads;create_from_request=', $id_request, '#betterads-ad-editor">', $txt['betterads_request_create_ad'], '</a>';
            elseif ($status === 'approved' && !$payment_cleared)
                echo '<span class="button disabled betterads-disabled-action" aria-disabled="true">', $txt['betterads_request_waiting_payment'], '</span>';

            echo '</div></div>';
            echo '</article>';
        }

        echo '</div>';
        return;
    }

    if ($section === 'stats')
    {
        $stats_units = $context['betterads_stats_units'] ?? array();
        $total_impressions = 0;
        $total_clicks = 0;
        $campaigns_with_activity = 0;

        foreach ($stats_units as $stats_unit)
        {
            $row = $context['betterads_stats'][(int)$stats_unit['id_unit']] ?? array('impressions'=>0, 'clicks'=>0, 'ctr'=>0.0);
            $total_impressions += (int)$row['impressions'];
            $total_clicks += (int)$row['clicks'];
            if ((int)$row['impressions'] > 0 || (int)$row['clicks'] > 0)
                $campaigns_with_activity++;
        }

        $overall_ctr = $total_impressions > 0 ? ($total_clicks / $total_impressions) * 100 : 0.0;

        echo '<div class="cat_bar"><h3 class="catbg">', $txt['betterads_nav_stats'], '</h3></div>';
        echo '<div class="windowbg betterads-stats-intro"><p>', $txt['betterads_stats_page_help'], '</p></div>';

        echo '<div class="betterads-stats-summary">';
        $summary_cards = array(
            array(number_format($total_impressions), $txt['betterads_stats_total_impressions']),
            array(number_format($total_clicks), $txt['betterads_stats_total_clicks']),
            array(number_format($overall_ctr, 2) . '%', $txt['betterads_stats_overall_ctr']),
            array(number_format($campaigns_with_activity), $txt['betterads_stats_campaigns_active']),
        );
        foreach ($summary_cards as $summary_card)
            echo '<div class="windowbg betterads-stats-summary-card"><strong>', $summary_card[0], '</strong><span>', htmlspecialchars((string)$summary_card[1], ENT_QUOTES, 'UTF-8'), '</span></div>';
        echo '</div>';

        if (!empty($context['betterads_stats_detail']))
        {
            $detail = $context['betterads_stats_detail'];
            $detail_unit = $detail['unit'];
            $detail_place = $context['betterads_placements'][$detail_unit['placement_key']] ?? $detail_unit['placement_key'];

            echo '<div class="cat_bar"><h3 class="catbg">', $txt['betterads_stats_detail_title'], ': ', htmlspecialchars((string)$detail_unit['name'], ENT_QUOTES, 'UTF-8'), '</h3></div>';
            echo '<div class="windowbg betterads-stats-detail-panel">';
            echo '<div class="betterads-stats-detail-head"><div><strong>', htmlspecialchars((string)$detail_unit['name'], ENT_QUOTES, 'UTF-8'), '</strong><span>', htmlspecialchars((string)$detail_place, ENT_QUOTES, 'UTF-8'), '</span></div><div><a class="button" href="', $scripturl, '?action=admin;area=betterads;sa=ads;edit=', (int)$detail_unit['id_unit'], '">', $txt['betterads_stats_edit_ad'], '</a> <a class="button" href="', $scripturl, '?action=admin;area=betterads;sa=stats">', $txt['betterads_close_stats'], '</a></div></div>';
            echo '<p class="smalltext">', $txt['betterads_stats_detail_help'], '</p>';

            $range_labels = array(
                'today' => $txt['betterads_stats_today'],
                'seven' => $txt['betterads_stats_7_days'],
                'thirty' => $txt['betterads_stats_30_days'],
                'lifetime' => $txt['betterads_stats_lifetime'],
            );

            echo '<div class="betterads-stats-range-grid">';
            foreach ($range_labels as $key => $label)
            {
                $row = $detail['ranges'][$key] ?? array('impressions'=>0, 'clicks'=>0, 'ctr'=>0.0);
                echo '<div class="betterads-stats-range-card">';
                echo '<h4>', htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8'), '</h4>';
                echo '<div class="betterads-range-metrics">';
                echo '<div><strong>', number_format((int)$row['impressions']), '</strong><span>', $txt['betterads_impressions_short'], '</span></div>';
                echo '<div><strong>', number_format((int)$row['clicks']), '</strong><span>', $txt['betterads_clicks_short'], '</span></div>';
                echo '<div><strong>', number_format((float)$row['ctr'], 2), '%</strong><span>', $txt['betterads_ctr'], '</span></div>';
                echo '</div></div>';
            }
            echo '</div></div>';
        }

        echo '<div class="cat_bar"><h3 class="catbg">', $txt['betterads_stats_campaign_performance'], '</h3></div>';
        echo '<div class="windowbg betterads-stats-table-wrap">';

        if (empty($stats_units))
            echo '<div class="information">', $txt['betterads_none'], '</div>';
        else
        {
            echo '<div class="table_grid"><table class="table_grid betterads-stats-table"><thead><tr class="title_bar">';
            echo '<th>', $txt['betterads_name'], '</th>';
            echo '<th>', $txt['betterads_placement'], '</th>';
            echo '<th>', $txt['betterads_status'], '</th>';
            echo '<th class="centertext">', $txt['betterads_impressions_short'], '</th>';
            echo '<th class="centertext">', $txt['betterads_clicks_short'], '</th>';
            echo '<th class="centertext">', $txt['betterads_ctr'], '</th>';
            echo '<th></th></tr></thead><tbody>';

            foreach ($stats_units as $unit)
            {
                $stats = $context['betterads_stats'][(int)$unit['id_unit']] ?? array('impressions'=>0,'clicks'=>0,'ctr'=>0.0);
                $place = $context['betterads_placements'][$unit['placement_key']] ?? $unit['placement_key'];
                $admin_status = (string)($unit['admin_status'] ?? 'active');

                if ($admin_status === 'archived')
                    $status_label = $txt['betterads_archived'];
                elseif ($admin_status === 'disabled')
                    $status_label = $txt['betterads_off'];
                elseif ($admin_status === 'needs_content')
                    $status_label = $txt['betterads_needs_content'];
                elseif ($admin_status === 'scheduled')
                    $status_label = $txt['betterads_scheduled'];
                elseif ($admin_status === 'expired')
                    $status_label = $txt['betterads_expired'];
                else
                    $status_label = $txt['betterads_on'];

                $has_activity = (int)$stats['impressions'] > 0 || (int)$stats['clicks'] > 0;

                echo '<tr class="windowbg', $has_activity ? '' : ' betterads-stats-no-activity', '">';
                echo '<td><strong>', htmlspecialchars((string)$unit['name'], ENT_QUOTES, 'UTF-8'), '</strong>';
                if (!$has_activity)
                    echo '<div class="smalltext">', $txt['betterads_stats_no_activity'], '</div>';
                echo '</td>';
                echo '<td>', htmlspecialchars((string)$place, ENT_QUOTES, 'UTF-8'), '</td>';
                echo '<td><span class="betterads-stats-status betterads-stats-status-', htmlspecialchars($admin_status, ENT_QUOTES, 'UTF-8'), '">', htmlspecialchars((string)$status_label, ENT_QUOTES, 'UTF-8'), '</span></td>';
                echo '<td class="centertext"><strong>', number_format((int)$stats['impressions']), '</strong></td>';
                echo '<td class="centertext"><strong>', number_format((int)$stats['clicks']), '</strong></td>';
                echo '<td class="centertext"><strong>', number_format((float)$stats['ctr'], 2), '%</strong></td>';
                echo '<td class="righttext"><a class="button" href="', $scripturl, '?action=admin;area=betterads;sa=stats;stats=', (int)$unit['id_unit'], '">', $txt['betterads_stats_detail_button'], '</a></td>';
                echo '</tr>';
            }

            echo '</tbody></table></div>';
        }

        echo '</div>';
        return;
    }

    // Ads: editor, preview/detail, filters and ad-unit list.
    echo '<div id="betterads-ad-editor" class="cat_bar"><h3 class="catbg">', !empty($edit['id_unit']) ? $txt['betterads_edit_unit'] : $txt['betterads_add_unit'], '</h3></div>';

    if (!empty($context['betterads_source_request']))
    {
        $source = $context['betterads_source_request'];
        echo '<div class="information betterads-create-from-request">';
        echo '<strong>', sprintf($txt['betterads_creating_from_request'], (int)$source['id_request']), '</strong>';
        echo '<div>', htmlspecialchars((string)$source['offer_title'], ENT_QUOTES, 'UTF-8');
        if (!empty($source['price_text']))
            echo ' — ', htmlspecialchars((string)$source['price_text'], ENT_QUOTES, 'UTF-8');
        echo '</div>';
        echo '<div class="smalltext">', htmlspecialchars((string)$source['contact_name'], ENT_QUOTES, 'UTF-8'), ' &lt;', htmlspecialchars((string)$source['contact_email'], ENT_QUOTES, 'UTF-8'), '&gt;</div>';
        if (!empty($source['message']))
            echo '<details><summary>', $txt['betterads_request_view_notes'], '</summary><div>', nl2br(htmlspecialchars((string)$source['message'], ENT_QUOTES, 'UTF-8')), '</div></details>';
        echo '<div class="smalltext">', $txt['betterads_create_from_request_help'], '</div>';
        echo '</div>';
    }

    echo '<form class="windowbg betterads-admin-form" action="', $scripturl, '?action=admin;area=betterads;sa=ads" method="post" enctype="multipart/form-data">';
    echo '<input type="hidden" name="id_unit" value="', (int)$edit['id_unit'], '">';
    if (!empty($context['betterads_source_request']))
        echo '<input type="hidden" name="source_request_id" value="', (int)$context['betterads_source_request']['id_request'], '">';
    echo '<dl class="settings">';
    echo '<dt><label for="ba_name">', $txt['betterads_name'], '</label></dt><dd><input id="ba_name" type="text" name="name" value="', htmlspecialchars((string)$edit['name'], ENT_QUOTES, 'UTF-8'), '" required></dd>';
    echo '<dt><label for="ba_type">', $txt['betterads_type'], '</label></dt><dd><select id="ba_type" name="unit_type"><option value="code"', $edit['unit_type']==='code'?' selected':'', '>', $txt['betterads_type_code'], '</option><option value="image"', $edit['unit_type']==='image'?' selected':'', '>', $txt['betterads_type_image'], '</option></select></dd>';
    echo '<dt><label for="ba_place">', $txt['betterads_placement'], '</label></dt><dd><select id="ba_place" name="placement_key">';
    foreach ($context['betterads_placement_groups'] as $group=>$keys) {
        echo '<optgroup label="', htmlspecialchars($group, ENT_QUOTES, 'UTF-8'), '">';
        foreach ($keys as $key) {
            if (!array_key_exists($key, $context['betterads_placements'])) continue;
            $label = $context['betterads_placements'][$key];
            echo '<option value="', htmlspecialchars($key, ENT_QUOTES, 'UTF-8'), '"', $edit['placement_key']===$key?' selected':'', '>', htmlspecialchars($label, ENT_QUOTES, 'UTF-8'), '</option>';
        }
        echo '</optgroup>';
    }
    echo '</select><div class="smalltext">', $txt['betterads_placement_help'], '</div><div id="ba-placement-note" class="smalltext betterads-placement-note"></div></dd>';
    echo '<dt class="betterads-repeat-field"><label for="ba_repeat_start">', $txt['betterads_repeat_start'], '</label></dt><dd class="betterads-repeat-field"><input id="ba_repeat_start" type="number" min="1" max="100" name="repeat_start" value="', (int)($edit['repeat_start'] ?? 3), '"><div class="smalltext">', $txt['betterads_repeat_start_help'], '</div></dd>';
    echo '<dt class="betterads-repeat-field"><label for="ba_repeat_every">', $txt['betterads_repeat_every'], '</label></dt><dd class="betterads-repeat-field"><input id="ba_repeat_every" type="number" min="1" max="100" name="repeat_every" value="', (int)($edit['repeat_every'] ?? 5), '"><div class="smalltext">', $txt['betterads_repeat_every_help'], '</div></dd>';
    echo '<dt class="betterads-repeat-field"><label for="ba_repeat_max">', $txt['betterads_repeat_max'], '</label></dt><dd class="betterads-repeat-field"><input id="ba_repeat_max" type="number" min="1" max="20" name="repeat_max" value="', (int)($edit['repeat_max'] ?? 2), '"><div class="smalltext">', $txt['betterads_repeat_max_help'], '</div></dd>';
    $gallery_repeat_mode = betterads_gallery_repeat_mode((int)($edit['id_unit'] ?? 0));
    echo '<dt class="betterads-gallery-repeat-mode-field"><label for="ba_gallery_repeat_mode">', $txt['betterads_gallery_repeat_mode'], '</label></dt><dd class="betterads-gallery-repeat-mode-field"><select id="ba_gallery_repeat_mode" name="gallery_repeat_mode"><option value="row"', $gallery_repeat_mode === 'row' ? ' selected' : '', '>', $txt['betterads_gallery_repeat_mode_row'], '</option><option value="card"', $gallery_repeat_mode === 'card' ? ' selected' : '', '>', $txt['betterads_gallery_repeat_mode_card'], '</option></select><div class="smalltext">', $txt['betterads_gallery_repeat_mode_help'], '</div></dd>';
    echo '<dt class="betterads-repeat-field">', $txt['betterads_repeat_live_preview'], '</dt><dd class="betterads-repeat-field"><div id="ba-repeat-preview" class="smalltext betterads-repeat-preview"></div></dd>';
    echo '<dt><label for="ba_schedule_mode">', $txt['betterads_schedule'], '</label></dt><dd><select id="ba_schedule_mode" name="schedule_mode"><option value="always"', ($edit['schedule_mode'] ?? 'always')==='always'?' selected':'', '>', $txt['betterads_schedule_always'], '</option><option value="window"', ($edit['schedule_mode'] ?? '')==='window'?' selected':'', '>', $txt['betterads_schedule_window'], '</option><option value="start_only"', ($edit['schedule_mode'] ?? '')==='start_only'?' selected':'', '>', $txt['betterads_schedule_start_only'], '</option><option value="end_only"', ($edit['schedule_mode'] ?? '')==='end_only'?' selected':'', '>', $txt['betterads_schedule_end_only'], '</option></select><div class="smalltext">', $txt['betterads_schedule_help'], '</div></dd>';
    echo '<dt class="betterads-schedule-start"><label for="ba_start_at">', $txt['betterads_start_at'], '</label></dt><dd class="betterads-schedule-start"><input id="ba_start_at" type="datetime-local" name="start_at" value="', htmlspecialchars(betterads_format_admin_datetime((int)($edit['start_at'] ?? 0)), ENT_QUOTES, 'UTF-8'), '"><div class="smalltext">', $txt['betterads_start_at_help'], '</div></dd>';
    echo '<dt class="betterads-schedule-end"><label for="ba_end_at">', $txt['betterads_end_at'], '</label></dt><dd class="betterads-schedule-end"><input id="ba_end_at" type="datetime-local" name="end_at" value="', htmlspecialchars(betterads_format_admin_datetime((int)($edit['end_at'] ?? 0)), ENT_QUOTES, 'UTF-8'), '"><div class="smalltext">', $txt['betterads_end_at_help'], '</div></dd>';
    echo '<dt><label for="ba_aud">', $txt['betterads_audience'], '</label></dt><dd><select id="ba_aud" name="audience">';
    foreach (array('everyone'=>$txt['betterads_everyone'],'guests'=>$txt['betterads_guests'],'members'=>$txt['betterads_members']) as $key=>$label) echo '<option value="',$key,'"', $edit['audience']===$key?' selected':'','>',$label,'</option>';
    echo '</select></dd>';

    $selected_target_boards = array_flip(betterads_csv_ids($edit['target_boards'] ?? ''));
    $selected_excluded_boards = array_flip(betterads_csv_ids($edit['excluded_boards'] ?? ''));
    $selected_target_groups = array_flip(betterads_csv_ids($edit['target_groups'] ?? ''));

    echo '<dt><label for="ba_target_boards">', $txt['betterads_target_boards'], '</label></dt><dd><select id="ba_target_boards" name="target_boards[]" multiple size="7" class="betterads-target-select">';
    foreach ($context['betterads_board_options'] as $id_board => $label)
        echo '<option value="', (int)$id_board, '"', isset($selected_target_boards[(int)$id_board]) ? ' selected' : '', '>', htmlspecialchars($label, ENT_QUOTES, 'UTF-8'), '</option>';
    echo '</select><div class="smalltext">', $txt['betterads_target_boards_help'], '</div></dd>';

    echo '<dt><label for="ba_excluded_boards">', $txt['betterads_excluded_boards'], '</label></dt><dd><select id="ba_excluded_boards" name="excluded_boards[]" multiple size="7" class="betterads-target-select">';
    foreach ($context['betterads_board_options'] as $id_board => $label)
        echo '<option value="', (int)$id_board, '"', isset($selected_excluded_boards[(int)$id_board]) ? ' selected' : '', '>', htmlspecialchars($label, ENT_QUOTES, 'UTF-8'), '</option>';
    echo '</select><div class="smalltext">', $txt['betterads_excluded_boards_help'], '</div></dd>';

    echo '<dt><label for="ba_target_groups">', $txt['betterads_target_groups'], '</label></dt><dd><select id="ba_target_groups" name="target_groups[]" multiple size="6" class="betterads-target-select">';
    foreach ($context['betterads_group_options'] as $id_group => $label)
        echo '<option value="', (int)$id_group, '"', isset($selected_target_groups[(int)$id_group]) ? ' selected' : '', '>', htmlspecialchars($label, ENT_QUOTES, 'UTF-8'), '</option>';
    echo '</select><div class="smalltext">', $txt['betterads_target_groups_help'], '</div></dd>';

    echo '<dt class="betterads-code-field"><label for="ba_code">', $txt['betterads_code'], '</label></dt><dd class="betterads-code-field"><textarea id="ba_code" name="ad_code" rows="7" style="width:100%">', htmlspecialchars((string)$edit['ad_code'], ENT_QUOTES, 'UTF-8'), '</textarea><div class="smalltext">', $txt['betterads_code_help'], '</div></dd>';
    echo '<dt class="betterads-image-field">', $txt['betterads_image_upload'], '</dt><dd class="betterads-image-field"><input type="file" name="image_upload" accept="image/jpeg,image/png,image/gif,image/webp"></dd>';
    echo '<dt class="betterads-image-field"><label for="ba_image">', $txt['betterads_image_url'], '</label></dt><dd class="betterads-image-field"><input id="ba_image" type="url" name="image_url" value="', htmlspecialchars((string)$edit['image_url'], ENT_QUOTES, 'UTF-8'), '" style="width:100%"></dd>';
    echo '<dt class="betterads-image-field"><label for="ba_target">', $txt['betterads_target_url'], '</label></dt><dd class="betterads-image-field"><input id="ba_target" type="url" name="target_url" value="', htmlspecialchars((string)$edit['target_url'], ENT_QUOTES, 'UTF-8'), '" style="width:100%"></dd>';
    echo '<dt class="betterads-image-field"><label for="ba_alt">', $txt['betterads_alt_text'], '</label></dt><dd class="betterads-image-field"><input id="ba_alt" type="text" name="alt_text" value="', htmlspecialchars((string)$edit['alt_text'], ENT_QUOTES, 'UTF-8'), '" style="width:100%"><div class="smalltext">', $txt['betterads_alt_text_help'], '</div></dd>';
    echo '<dt class="betterads-image-field"><label for="ba_size_mode">', $txt['betterads_image_size_mode'], '</label></dt><dd class="betterads-image-field"><select id="ba_size_mode" name="image_size_mode"><option value="responsive"', ($edit['image_size_mode'] ?? 'responsive')==='responsive'?' selected':'', '>', $txt['betterads_size_responsive'], '</option><option value="original"', ($edit['image_size_mode'] ?? '')==='original'?' selected':'', '>', $txt['betterads_size_original'], '</option><option value="custom"', ($edit['image_size_mode'] ?? '')==='custom'?' selected':'', '>', $txt['betterads_size_custom'], '</option></select><div class="smalltext">', $txt['betterads_image_size_help'], '</div></dd>';
    echo '<dt class="betterads-image-field"><label for="ba_width">', $txt['betterads_image_width'], '</label></dt><dd class="betterads-image-field"><input id="ba_width" type="number" min="0" max="5000" step="1" name="image_width" value="', (int)($edit['image_width'] ?? 0), '"> <span class="smalltext">px — ', $txt['betterads_zero_auto'], '</span></dd>';
    echo '<dt class="betterads-image-field"><label for="ba_height">', $txt['betterads_image_height'], '</label></dt><dd class="betterads-image-field"><input id="ba_height" type="number" min="0" max="5000" step="1" name="image_height" value="', (int)($edit['image_height'] ?? 0), '"> <span class="smalltext">px — ', $txt['betterads_zero_auto'], '</span></dd>';
    echo '<dt class="betterads-image-field">', $txt['betterads_maintain_ratio'], '</dt><dd class="betterads-image-field"><label><input type="checkbox" name="maintain_ratio" value="1"', !isset($edit['maintain_ratio']) || !empty($edit['maintain_ratio'])?' checked':'', '> ', $txt['betterads_maintain_ratio_help'], '</label></dd>';
    echo '<dt class="betterads-image-field"><label for="ba_align">', $txt['betterads_image_align'], '</label></dt><dd class="betterads-image-field"><select id="ba_align" name="image_align"><option value="left"', ($edit['image_align'] ?? '')==='left'?' selected':'', '>', $txt['betterads_align_left'], '</option><option value="center"', ($edit['image_align'] ?? 'center')==='center'?' selected':'', '>', $txt['betterads_align_center'], '</option><option value="right"', ($edit['image_align'] ?? '')==='right'?' selected':'', '>', $txt['betterads_align_right'], '</option></select></dd>';
    echo '<dt><label for="ba_label_position">', $txt['betterads_label_position'], '</label></dt><dd><select id="ba_label_position" name="label_position"><option value="above"', ($edit['label_position'] ?? 'above')==='above'?' selected':'', '>', $txt['betterads_label_above'], '</option><option value="below"', ($edit['label_position'] ?? '')==='below'?' selected':'', '>', $txt['betterads_label_below'], '</option></select><div class="smalltext">', $txt['betterads_label_position_help'], '</div></dd>';
    echo '<dt><label for="ba_label_align">', $txt['betterads_label_align'], '</label></dt><dd><select id="ba_label_align" name="label_align"><option value="match"', ($edit['label_align'] ?? 'match')==='match'?' selected':'', '>', $txt['betterads_label_match'], '</option><option value="left"', ($edit['label_align'] ?? '')==='left'?' selected':'', '>', $txt['betterads_align_left'], '</option><option value="center"', ($edit['label_align'] ?? '')==='center'?' selected':'', '>', $txt['betterads_align_center'], '</option><option value="right"', ($edit['label_align'] ?? '')==='right'?' selected':'', '>', $txt['betterads_align_right'], '</option></select><div class="smalltext">', $txt['betterads_label_align_help'], '</div></dd>';
    echo '<dt>', $txt['betterads_status'], '</dt><dd><label><input type="checkbox" name="enabled" value="1"', !empty($edit['enabled'])?' checked':'', '> ', $txt['betterads_unit_enabled'], '</label></dd>';
    echo '</dl><div class="righttext">';
    if (!empty($edit['id_unit'])) echo '<a class="button" href="', $scripturl, '?action=admin;area=betterads;sa=ads">', $txt['betterads_cancel_edit'], '</a> ';
    echo '<button class="button" type="submit" name="save_unit">', $txt['save'], '</button></div><input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '"></form>';

    if (!empty($context['betterads_preview'])) {
        $preview = $context['betterads_preview'];
        echo '<div class="cat_bar"><h3 class="catbg">', $txt['betterads_preview_title'], ': ', htmlspecialchars((string)$preview['unit']['name'], ENT_QUOTES, 'UTF-8'), '</h3></div>';
        echo '<div class="windowbg betterads-admin-preview"><p class="smalltext">', $txt['betterads_preview_help'], '</p>', $preview['html'], '<div class="righttext"><a class="button" href="', $scripturl, '?action=admin;area=betterads;sa=ads">', $txt['betterads_close_preview'], '</a></div></div>';
    }

    echo '<div class="cat_bar"><h3 class="catbg">', $txt['betterads_units'], '</h3></div><div class="windowbg">';
    echo '<div class="betterads-status-filters">';
    $betterads_filter_labels = array(
        'current' => $txt['betterads_filter_current'],
        'active' => $txt['betterads_filter_active'],
        'scheduled' => $txt['betterads_filter_scheduled'],
        'expired' => $txt['betterads_filter_expired'],
        'disabled' => $txt['betterads_filter_disabled'],
        'archived' => $txt['betterads_filter_archived'],
        'all' => $txt['betterads_filter_all'],
    );
    foreach ($betterads_filter_labels as $filter_key => $filter_label) {
        $count = (int)($context['betterads_filter_counts'][$filter_key] ?? 0);
        echo '<a class="button', $context['betterads_filter'] === $filter_key ? ' active' : '', '" href="', $scripturl, '?action=admin;area=betterads;sa=ads;filter=', urlencode($filter_key), '"><span class="betterads-filter-label">', htmlspecialchars($filter_label, ENT_QUOTES, 'UTF-8'), '</span><span class="betterads-filter-badge">', $count, '</span></a>';
    }
    echo '</div>';
    if (empty($context['betterads_units'])) echo '<p>', $txt['betterads_none_filter'], '</p>';
    else {
        echo '<div class="table_grid"><table class="table_grid"><thead><tr class="title_bar"><th>', $txt['betterads_name'], '</th><th>', $txt['betterads_type'], '</th><th>', $txt['betterads_placement'], '</th><th>', $txt['betterads_audience'], '</th><th>', $txt['betterads_status'], '</th><th>', $txt['betterads_stats'], '</th><th></th></tr></thead><tbody>';
        foreach ($context['betterads_units'] as $unit) {
            $place = $context['betterads_placements'][$unit['placement_key']] ?? $unit['placement_key'];
            $audience_label = $unit['audience'] === 'guests' ? $txt['betterads_guests'] : ($unit['audience'] === 'members' ? $txt['betterads_members'] : $txt['betterads_everyone']);
            $targeting_parts = array();
            $target_board_count = count(betterads_csv_ids($unit['target_boards'] ?? ''));
            $excluded_board_count = count(betterads_csv_ids($unit['excluded_boards'] ?? ''));
            $target_group_count = count(betterads_csv_ids($unit['target_groups'] ?? ''));
            if ($target_board_count) $targeting_parts[] = sprintf($txt['betterads_target_summary_boards'], $target_board_count);
            if ($excluded_board_count) $targeting_parts[] = sprintf($txt['betterads_target_summary_excluded'], $excluded_board_count);
            if ($target_group_count) $targeting_parts[] = sprintf($txt['betterads_target_summary_groups'], $target_group_count);
            $targeting_extra = $targeting_parts ? '<div class="smalltext betterads-target-summary">' . implode(' · ', $targeting_parts) . '</div>' : '';
            $admin_status = (string)($unit['admin_status'] ?? 'active');
            if ($admin_status === 'archived') $status_label = $txt['betterads_archived'];
            elseif ($admin_status === 'disabled') $status_label = $txt['betterads_off'];
            elseif ($admin_status === 'needs_content') $status_label = $txt['betterads_needs_content'];
            elseif ($admin_status === 'scheduled') $status_label = $txt['betterads_scheduled'];
            elseif ($admin_status === 'expired') $status_label = $txt['betterads_expired'];
            else $status_label = $txt['betterads_on'];
            $placement_extra = '';
            if (in_array($unit['placement_key'], array('topic_list_repeat', 'topic_view_repeat', 'gallery_album_repeat'), true))
                $placement_extra .= '<div class="smalltext">' . sprintf($txt['betterads_repeat_summary'], (int)$unit['repeat_start'], (int)$unit['repeat_every'], (int)$unit['repeat_max']) . '</div>';
            $shared_count = (int)($context['betterads_placement_counts'][$unit['placement_key']] ?? 0);
            if ($unit['placement_key'] !== '' && $shared_count > 1)
                $placement_extra .= '<div class="smalltext betterads-shared-placement">' . sprintf($txt['betterads_shared_placement'], $shared_count) . '</div>';
            
            $schedule_extra = '';
            if ($admin_status === 'archived' && !empty($unit['archived_at'])) {
                $schedule_extra = '<div class="smalltext">' . sprintf($txt['betterads_archived_short'], htmlspecialchars(betterads_format_admin_datetime((int)$unit['archived_at']), ENT_QUOTES, 'UTF-8')) . '</div>';
            }
            elseif (($unit['schedule_mode'] ?? 'always') !== 'always') {
                $parts = array();
                if (!empty($unit['start_at'])) $parts[] = sprintf($txt['betterads_starts_short'], htmlspecialchars(betterads_format_admin_datetime((int)$unit['start_at']), ENT_QUOTES, 'UTF-8'));
                if (!empty($unit['end_at'])) $parts[] = sprintf($txt['betterads_ends_short'], htmlspecialchars(betterads_format_admin_datetime((int)$unit['end_at']), ENT_QUOTES, 'UTF-8'));
                if ($parts) $schedule_extra = '<div class="smalltext">' . implode(' · ', $parts) . '</div>';
            }
            $stats = $context['betterads_stats'][(int)$unit['id_unit']] ?? array('impressions'=>0, 'clicks'=>0, 'ctr'=>0.0);
            $stats_html = '<div class="betterads-stats-cell"><strong>' . number_format((int)$stats['impressions']) . '</strong> ' . htmlspecialchars($txt['betterads_impressions_short'], ENT_QUOTES, 'UTF-8')
                . '<br><strong>' . number_format((int)$stats['clicks']) . '</strong> ' . htmlspecialchars($txt['betterads_clicks_short'], ENT_QUOTES, 'UTF-8')
                . '<br><strong>' . number_format((float)$stats['ctr'], 2) . '%</strong> ' . htmlspecialchars($txt['betterads_ctr'], ENT_QUOTES, 'UTF-8') . '</div>';
            $filter_qs = ';filter=' . urlencode($context['betterads_filter']);
            $archive_button = !empty($unit['archived'])
                ? '<a class="button" href="' . $scripturl . '?action=admin;area=betterads;sa=ads;restore=' . (int)$unit['id_unit'] . $filter_qs . ';' . $context['session_var'] . '=' . $context['session_id'] . '">' . htmlspecialchars($txt['betterads_restore'], ENT_QUOTES, 'UTF-8') . '</a>'
                : '<a class="button" href="' . $scripturl . '?action=admin;area=betterads;sa=ads;archive=' . (int)$unit['id_unit'] . $filter_qs . ';' . $context['session_var'] . '=' . $context['session_id'] . '">' . htmlspecialchars($txt['betterads_archive'], ENT_QUOTES, 'UTF-8') . '</a>';
            $row_class = !empty($unit['archived']) ? 'windowbg betterads-row-archived' : 'windowbg';
            echo '<tr class="', $row_class, '"><td><strong>', htmlspecialchars($unit['name'], ENT_QUOTES, 'UTF-8'), '</strong></td><td>', htmlspecialchars($unit['unit_type'], ENT_QUOTES, 'UTF-8'), '</td><td>', htmlspecialchars($place, ENT_QUOTES, 'UTF-8'), $placement_extra, '</td><td>', htmlspecialchars($audience_label, ENT_QUOTES, 'UTF-8'), $targeting_extra, '</td><td>', htmlspecialchars($status_label, ENT_QUOTES, 'UTF-8'), $schedule_extra, '</td><td>', $stats_html, '</td><td class="righttext"><span class="betterads-unit-actions"><a class="button" href="', $scripturl, '?action=admin;area=betterads;sa=ads;preview=', (int)$unit['id_unit'], $filter_qs, '">', $txt['betterads_preview'], '</a><a class="button" href="', $scripturl, '?action=admin;area=betterads;sa=stats;stats=', (int)$unit['id_unit'], $filter_qs, '">', $txt['betterads_stats_button'], '</a><a class="button" href="', $scripturl, '?action=admin;area=betterads;sa=ads;edit=', (int)$unit['id_unit'], $filter_qs, '">', $txt['modify'], '</a>', $archive_button, '<a class="button" href="', $scripturl, '?action=admin;area=betterads;sa=ads;delete=', (int)$unit['id_unit'], $filter_qs, ';', $context['session_var'], '=', $context['session_id'], '" onclick="return confirm(', JavaScriptEscape($txt['betterads_delete_confirm']), ');">', $txt['remove'], '</a></span></td></tr>';
        }
        echo '</tbody></table></div>';
    }
    echo '</div>';

    $betterads_placement_counts_json = json_encode($context['betterads_placement_counts'] ?? array());
    $betterads_shared_template_json = json_encode($txt['betterads_shared_placement']);
    $betterads_repeat_template_json = json_encode($txt['betterads_repeat_live_help']);

    echo '<script>
    (function(){
        var type = document.getElementById("ba_type");
        if (!type) return;
        var place = document.getElementById("ba_place");
        var repeatStart = document.getElementById("ba_repeat_start");
        var repeatEvery = document.getElementById("ba_repeat_every");
        var repeatMax = document.getElementById("ba_repeat_max");
        var repeatPreview = document.getElementById("ba-repeat-preview");
        var scheduleMode = document.getElementById("ba_schedule_mode");
        var placementNote = document.getElementById("ba-placement-note");
        var placementCounts = ', $betterads_placement_counts_json, ';
        var sharedTemplate = ', $betterads_shared_template_json, ';
        var repeatTemplate = ', $betterads_repeat_template_json, ';
        function formatTemplate(tpl, value){ return String(tpl || "").replace("%1$d", value).replace("%1$s", value); }
        function updateRepeatPreview(){
            if (!repeatPreview) return;
            var start = Math.max(1, parseInt(repeatStart && repeatStart.value || "3", 10));
            var every = Math.max(1, parseInt(repeatEvery && repeatEvery.value || "5", 10));
            var max = Math.max(1, parseInt(repeatMax && repeatMax.value || "2", 10));
            var points = [];
            for (var n = 0; n < max; n++) points.push(start + (n * every));
            repeatPreview.textContent = formatTemplate(repeatTemplate, points.join(", "));
        }
        function updatePlacementNote(){
            if (!placementNote || !place) return;
            var count = parseInt((placementCounts && placementCounts[place.value]) || 0, 10);
            placementNote.textContent = count > 1 ? formatTemplate(sharedTemplate, count) : "";
        }
        function toggleFields(){
            var isImage = type.value === "image";
            document.querySelectorAll(".betterads-code-field").forEach(function(el){ el.style.display = isImage ? "none" : ""; });
            document.querySelectorAll(".betterads-image-field").forEach(function(el){ el.style.display = isImage ? "" : "none"; });
            var isRepeat = place && (place.value === "topic_list_repeat" || place.value === "topic_view_repeat" || place.value === "gallery_album_repeat");
            document.querySelectorAll(".betterads-repeat-field").forEach(function(el){ el.style.display = isRepeat ? "" : "none"; });
            var isGalleryRepeat = place && place.value === "gallery_album_repeat";
            document.querySelectorAll(".betterads-gallery-repeat-mode-field").forEach(function(el){ el.style.display = isGalleryRepeat ? "" : "none"; });
            var sm = scheduleMode ? scheduleMode.value : "always";
            document.querySelectorAll(".betterads-schedule-start").forEach(function(el){ el.style.display = (sm === "window" || sm === "start_only") ? "" : "none"; });
            document.querySelectorAll(".betterads-schedule-end").forEach(function(el){ el.style.display = (sm === "window" || sm === "end_only") ? "" : "none"; });
            updateRepeatPreview();
            updatePlacementNote();
        }
        type.addEventListener("change", toggleFields);
        if (place) place.addEventListener("change", toggleFields);
        if (scheduleMode) scheduleMode.addEventListener("change", toggleFields);
        [repeatStart, repeatEvery, repeatMax].forEach(function(el){ if (el) el.addEventListener("input", updateRepeatPreview); });
        toggleFields();
    })();
    </script>';

}
