<?php
if (!defined('SMF')) die('No direct access.');

function template_betterads_above()
{
    echo betterads_render_placement('global_header');
    $contextual = betterads_context_placement('top');
    if ($contextual !== '') echo betterads_render_placement($contextual);
}

function template_betterads_below()
{
    $contextual = betterads_context_placement('bottom');
    if ($contextual !== '') echo betterads_render_placement($contextual);
    echo betterads_render_placement('global_footer');

    if (!empty($_REQUEST['topic']))
        echo betterads_render_repeat_payload('topic_view_repeat'), betterads_repeat_script();
    elseif (!empty($_REQUEST['board']))
        echo betterads_render_repeat_payload('topic_list_repeat'), betterads_repeat_script();
}


function template_betterads_advertise()
{
    global $context, $scripturl, $txt;

    echo '<div class="cat_bar"><h3 class="catbg">',
        htmlspecialchars((string)$context['betterads_advertise_title'], ENT_QUOTES, 'UTF-8'),
        '</h3></div>';

    echo '<div class="windowbg betterads-advertise-page">';

    echo '<section class="betterads-advertise-hero">';
    echo '<div class="betterads-advertise-copy">',
        nl2br(htmlspecialchars((string)$context['betterads_advertise_body'], ENT_QUOTES, 'UTF-8')),
        '</div>';
    echo '<div class="betterads-advertise-trust">';
    foreach (array(
        $txt['betterads_public_trust_updates'],
        $txt['betterads_public_trust_dashboard'],
        $txt['betterads_public_trust_renewal'],
    ) as $trust)
        echo '<span>', htmlspecialchars((string)$trust, ENT_QUOTES, 'UTF-8'), '</span>';
    echo '</div>';
    echo '</section>';

    echo '<section class="betterads-public-workflow">';
    echo '<h4>', $txt['betterads_public_how_it_works'], '</h4>';
    echo '<div class="betterads-public-workflow-grid">';
    $steps = array(
        array('1', $txt['betterads_public_step_choose'], $txt['betterads_public_step_choose_help']),
        array('2', $txt['betterads_public_step_request'], $txt['betterads_public_step_request_help']),
        array('3', $txt['betterads_public_step_approve'], $txt['betterads_public_step_approve_help']),
        array('4', $txt['betterads_public_step_run'], $txt['betterads_public_step_run_help']),
        array('5', $txt['betterads_public_step_track'], $txt['betterads_public_step_track_help']),
    );
    foreach ($steps as $step)
    {
        echo '<div class="betterads-public-step">';
        echo '<span class="betterads-public-step-number">', $step[0], '</span>';
        echo '<div><strong>', htmlspecialchars((string)$step[1], ENT_QUOTES, 'UTF-8'), '</strong>';
        echo '<span>', htmlspecialchars((string)$step[2], ENT_QUOTES, 'UTF-8'), '</span></div>';
        echo '</div>';
    }
    echo '</div></section>';

    echo '<div class="betterads-public-section-heading"><h4>', $txt['betterads_public_available_packages'], '</h4><span>', $txt['betterads_public_available_packages_help'], '</span></div>';

    if (!empty($context['betterads_advertise_offers']))
    {
        echo '<div class="betterads-advertise-options">';
        foreach ($context['betterads_advertise_offers'] as $offer)
        {
            echo '<article class="betterads-advertise-option">';
            echo '<div class="betterads-advertise-option-head">';
            echo '<div class="betterads-advertise-option-title">', htmlspecialchars((string)$offer['title'], ENT_QUOTES, 'UTF-8'), '</div>';
            if (!empty($offer['placement_label']) && $offer['placement_label'] !== $offer['title'])
                echo '<div class="betterads-advertise-option-placement">', htmlspecialchars((string)$offer['placement_label'], ENT_QUOTES, 'UTF-8'), '</div>';
            echo '</div>';

            echo '<div class="betterads-advertise-option-meta">';
            if (!empty($offer['price']))
                echo '<div><span>', $txt['betterads_public_rate'], '</span><strong>', htmlspecialchars((string)$offer['price'], ENT_QUOTES, 'UTF-8'), '</strong></div>';
            if (!empty($offer['campaign_days']))
                echo '<div><span>', $txt['betterads_public_duration'], '</span><strong>', sprintf($txt['betterads_public_campaign_duration'], (int)$offer['campaign_days']), '</strong></div>';
            echo '</div>';

            echo '<div class="betterads-advertise-option-includes">';
            echo '<span>✓ ', $txt['betterads_public_includes_review'], '</span>';
            echo '<span>✓ ', !empty($offer['payment_required']) ? $txt['betterads_public_includes_payment'] : $txt['betterads_public_includes_no_payment'], '</span>';
            echo '<span>✓ ', $txt['betterads_public_includes_reporting'], '</span>';
            echo '</div>';

            if (!empty($offer['description']))
                echo '<div class="betterads-advertise-option-description">', nl2br(htmlspecialchars((string)$offer['description'], ENT_QUOTES, 'UTF-8')), '</div>';

            $fallback = (string)$context['betterads_advertise_cta_url'];
            $button_class = $fallback === '' ? 'button betterads-inquiry-button betterads-inquiry-pending' : 'button betterads-inquiry-button';
            echo '<div class="betterads-advertise-option-action">';
            echo '<a class="button betterads-primary-request" href="', $scripturl, '?action=betterads;sa=request;offer=', (int)$offer['slot'], '">', $txt['betterads_request_package_button'], '</a>';
            echo '<a class="', $button_class, '"',
                $fallback !== '' ? ' href="' . htmlspecialchars($fallback, ENT_QUOTES, 'UTF-8') . '"' : '',
                ' data-betterads-fallback="', htmlspecialchars($fallback, ENT_QUOTES, 'UTF-8'),
                '" data-betterads-title="', htmlspecialchars((string)$offer['title'], ENT_QUOTES, 'UTF-8'),
                '" data-betterads-placement="', htmlspecialchars((string)$offer['placement'], ENT_QUOTES, 'UTF-8'),
                '" data-betterads-price="', htmlspecialchars((string)$offer['price'], ENT_QUOTES, 'UTF-8'),
                '">', htmlspecialchars((string)$context['betterads_advertise_cta_text'], ENT_QUOTES, 'UTF-8'), '</a></div>';

            echo '</article>';
        }
        echo '</div>';
    }
    else
    {
        echo '<div class="information betterads-public-empty">', $txt['betterads_public_no_packages'], '</div>';
    }

    echo '<div class="betterads-public-expectation">';
    echo '<strong>', $txt['betterads_public_what_to_expect'], '</strong>';
    echo '<span>', $txt['betterads_public_what_to_expect_help'], '</span>';
    echo '</div>';

    echo '<div id="betterads-inquiry-status" class="betterads-inquiry-status smalltext"></div>';

    echo '<script>
(function(){
    function appendParams(base, data){
        try {
            var url = new URL(base, window.location.href);
            Object.keys(data).forEach(function(key){
                if (data[key] !== "") url.searchParams.set(key, data[key]);
            });
            return url.toString();
        } catch(e) {
            var sep = base.indexOf("?") === -1 ? "?" : "&";
            var pairs = [];
            Object.keys(data).forEach(function(key){
                if (data[key] !== "") pairs.push(encodeURIComponent(key) + "=" + encodeURIComponent(data[key]));
            });
            return base + (pairs.length ? sep + pairs.join("&") : "");
        }
    }

    function resolveInquiry(){
        var contactLink = document.querySelector("a[href*=\"action=contactstaff\"]");
        var buttons = document.querySelectorAll(".betterads-inquiry-button");
        var status = document.getElementById("betterads-inquiry-status");

        buttons.forEach(function(btn){
            var fallback = btn.getAttribute("data-betterads-fallback") || "";
            var base = contactLink ? contactLink.href : fallback;

            if (!base) {
                btn.removeAttribute("href");
                btn.classList.add("betterads-inquiry-unavailable");
                btn.classList.remove("betterads-inquiry-pending");
                return;
            }

            btn.href = appendParams(base, {
                betterads_option: btn.getAttribute("data-betterads-title") || "",
                betterads_placement: btn.getAttribute("data-betterads-placement") || "",
                betterads_price: btn.getAttribute("data-betterads-price") || ""
            });

            btn.classList.remove("betterads-inquiry-pending", "betterads-inquiry-unavailable");
        });

        if (status) {
            if (contactLink)
                status.textContent = ' . json_encode($txt['betterads_inquiry_using_contact_staff'] ?? 'Advertising inquiries are handled through Contact Staff.') . ';
            else if (' . (!empty($context['betterads_advertise_has_fallback']) ? 'true' : 'false') . ')
                status.textContent = ' . json_encode($txt['betterads_inquiry_using_fallback'] ?? 'Advertising inquiries are handled through the configured inquiry destination.') . ';
            else
                status.textContent = ' . json_encode($txt['betterads_inquiry_unavailable'] ?? 'Advertising inquiry contact is currently unavailable.') . ';
        }
    }

    if (document.readyState === "loading")
        document.addEventListener("DOMContentLoaded", resolveInquiry);
    else
        resolveInquiry();
})();
</script>';

    echo '</div>';
}

function template_betterads_request()
{
    global $context, $txt, $scripturl;

    $offer = $context['betterads_request_offer'];
    $form = $context['betterads_request_form'];

    echo '<div class="cat_bar"><h3 class="catbg">', htmlspecialchars($txt['betterads_request_page_title'], ENT_QUOTES, 'UTF-8'), '</h3></div>';
    echo '<div class="windowbg betterads-request-page">';

    if (!empty($context['betterads_request_submitted']))
    {
        echo '<div class="infobox betterads-request-success"><strong>', $txt['betterads_request_success_title'], '</strong><br>', $txt['betterads_request_success_body'], '</div>';
        echo '<div class="betterads-request-actions"><a class="button" href="', $scripturl, '?action=betterads;sa=advertise">', $txt['betterads_request_back_to_options'], '</a></div>';
        echo '</div>';
        return;
    }

    echo '<div class="betterads-request-summary betterads-request-summary-polished">';
    echo '<div class="betterads-request-summary-main"><strong>', htmlspecialchars((string)$offer['title'], ENT_QUOTES, 'UTF-8'), '</strong>';
    if (!empty($offer['placement_label']))
        echo '<span>', htmlspecialchars((string)$offer['placement_label'], ENT_QUOTES, 'UTF-8'), '</span>';
    echo '</div>';
    echo '<div class="betterads-request-summary-meta">';
    if (!empty($offer['price']))
        echo '<div><span>', $txt['betterads_public_rate'], '</span><strong>', htmlspecialchars((string)$offer['price'], ENT_QUOTES, 'UTF-8'), '</strong></div>';
    if (!empty($offer['campaign_days']))
        echo '<div><span>', $txt['betterads_public_duration'], '</span><strong>', sprintf($txt['betterads_public_campaign_duration'], (int)$offer['campaign_days']), '</strong></div>';
    echo '</div></div>';

    echo '<div class="betterads-request-expectations">';
    echo '<strong>', $txt['betterads_request_what_happens_next'], '</strong>';
    echo '<span>1. ', $txt['betterads_request_next_review'], '</span>';
    echo '<span>2. ', !empty($offer['payment_required']) ? $txt['betterads_request_next_payment'] : $txt['betterads_request_next_no_payment'], '</span>';
    echo '<span>3. ', $txt['betterads_request_next_dashboard'], '</span>';
    echo '</div>';

    echo '<p>', $txt['betterads_request_intro'], '</p>';

    if (!empty($context['betterads_request_errors']))
    {
        echo '<div class="errorbox"><strong>', $txt['betterads_request_fix_errors'], '</strong><ul>';
        foreach ($context['betterads_request_errors'] as $error)
            echo '<li>', htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8'), '</li>';
        echo '</ul></div>';
    }

    echo '<form class="betterads-request-form" action="', $scripturl, '?action=betterads;sa=request;offer=', (int)$offer['slot'], '" method="post" enctype="multipart/form-data">';
    echo '<div class="betterads-request-grid">';

    echo '<label>', $txt['betterads_request_name'], '<input type="text" name="contact_name" maxlength="160" required value="', htmlspecialchars((string)$form['contact_name'], ENT_QUOTES, 'UTF-8'), '"></label>';
    echo '<label>', $txt['betterads_request_email'], '<input type="email" name="contact_email" maxlength="255" required value="', htmlspecialchars((string)$form['contact_email'], ENT_QUOTES, 'UTF-8'), '"></label>';
    echo '<label>', $txt['betterads_request_company'], '<input type="text" name="company" maxlength="160" value="', htmlspecialchars((string)$form['company'], ENT_QUOTES, 'UTF-8'), '"></label>';
    echo '<label>', $txt['betterads_request_website'], '<input type="url" name="website_url" maxlength="500" placeholder="https://" value="', htmlspecialchars((string)$form['website_url'], ENT_QUOTES, 'UTF-8'), '"></label>';
    echo '<label class="betterads-request-wide betterads-request-upload">', $txt['betterads_request_creative_upload'], '<input type="file" name="creative_upload" accept="image/jpeg,image/png,image/gif,image/webp"><span class="smalltext">', $txt['betterads_request_creative_upload_help'], '</span></label>';
    echo '<label class="betterads-request-wide">', $txt['betterads_request_creative_url'], '<input type="url" name="creative_url" maxlength="500" placeholder="https://" value="', htmlspecialchars((string)$form['creative_url'], ENT_QUOTES, 'UTF-8'), '"><span class="smalltext">', $txt['betterads_request_creative_help'], '</span></label>';
    echo '<label class="betterads-request-wide">', $txt['betterads_request_message'], '<textarea name="message" rows="6" maxlength="6000">', htmlspecialchars((string)$form['message'], ENT_QUOTES, 'UTF-8'), '</textarea></label>';

    echo '</div>';
    echo '<div class="betterads-request-actions"><a class="button" href="', $scripturl, '?action=betterads;sa=advertise">', $txt['betterads_request_cancel'], '</a><button class="button" type="submit" name="submit_ad_request" value="1">', $txt['betterads_request_submit'], '</button></div>';
    echo '<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">';
    echo '</form>';
    echo '</div>';
}


function template_betterads_order()
{
    global $context, $txt, $scripturl;

    $order = $context['betterads_order'];
    $request_status = (string)$order['status'];
    $payment_status = (string)$order['payment_status'];

    echo '<div class="cat_bar"><h3 class="catbg">', sprintf($txt['betterads_order_heading'], (int)$order['id_request']), '</h3></div>';
    echo '<div class="windowbg betterads-order-page">';

    echo '<div class="betterads-order-status-grid">';
    echo '<div><span class="smalltext">', $txt['betterads_order_request_status'], '</span><strong>', htmlspecialchars((string)$context['betterads_order_status_label'], ENT_QUOTES, 'UTF-8'), '</strong></div>';
    echo '<div><span class="smalltext">', $txt['betterads_order_payment_status'], '</span><strong>', htmlspecialchars((string)$context['betterads_order_payment_label'], ENT_QUOTES, 'UTF-8'), '</strong></div>';
    echo '</div>';

    echo '<div class="betterads-order-summary">';
    echo '<h4>', htmlspecialchars((string)$order['offer_title'], ENT_QUOTES, 'UTF-8'), '</h4>';
    if (!empty($order['price_text']))
        echo '<div class="betterads-order-price">', htmlspecialchars((string)$order['price_text'], ENT_QUOTES, 'UTF-8'), '</div>';
    if (!empty($order['placement_key']))
        echo '<div class="smalltext">', $txt['betterads_order_placement'], ': ', htmlspecialchars((string)$order['placement_key'], ENT_QUOTES, 'UTF-8'), '</div>';
    if (!empty($order['campaign_days']))
        echo '<div class="smalltext">', sprintf($txt['betterads_campaign_duration_days'], (int)$order['campaign_days']), '</div>';
    if (!empty($order['renewal_of_request']))
        echo '<div class="betterads-renewal-badge">', sprintf($txt['betterads_renewal_of_request'], (int)$order['renewal_of_request']), '</div>';
    echo '</div>';

    echo '<div class="betterads-order-columns">';
    echo '<div class="betterads-order-panel"><h4>', $txt['betterads_order_advertiser'], '</h4>';
    echo '<div><strong>', htmlspecialchars((string)$order['contact_name'], ENT_QUOTES, 'UTF-8'), '</strong></div>';
    if (!empty($order['company']))
        echo '<div>', htmlspecialchars((string)$order['company'], ENT_QUOTES, 'UTF-8'), '</div>';
    echo '<div>', htmlspecialchars((string)$order['contact_email'], ENT_QUOTES, 'UTF-8'), '</div>';
    if (!empty($order['website_url']))
        echo '<div><a href="', htmlspecialchars((string)$order['website_url'], ENT_QUOTES, 'UTF-8'), '" target="_blank" rel="noopener">', $txt['betterads_order_destination'], '</a></div>';
    echo '</div>';

    echo '<div class="betterads-order-panel"><h4>', $txt['betterads_order_creative'], '</h4>';
    if (!empty($context['betterads_order_creative_src']))
        echo '<a href="', htmlspecialchars((string)$context['betterads_order_creative_src'], ENT_QUOTES, 'UTF-8'), '" target="_blank" rel="noopener"><img class="betterads-order-creative" src="', htmlspecialchars((string)$context['betterads_order_creative_src'], ENT_QUOTES, 'UTF-8'), '" alt="" loading="lazy"></a>';
    else
        echo '<div class="smalltext">', $txt['betterads_order_no_creative'], '</div>';
    echo '</div>';
    echo '</div>';

    echo '<div class="betterads-order-payment">';

    if ($request_status === 'pending')
    {
        echo '<div class="information"><strong>', $txt['betterads_order_waiting_approval_title'], '</strong><br>', $txt['betterads_order_waiting_approval'], '</div>';
    }
    elseif ($request_status === 'declined')
    {
        echo '<div class="errorbox"><strong>', $txt['betterads_order_declined_title'], '</strong><br>', $txt['betterads_order_declined'], '</div>';
    }
    elseif ($payment_status === 'paid')
    {
        echo '<div class="infobox"><strong>', $txt['betterads_order_paid_title'], '</strong><br>', $txt['betterads_order_paid'];
        if (!empty($order['payment_reference']))
            echo '<div class="smalltext">', $txt['betterads_order_reference'], ': ', htmlspecialchars((string)$order['payment_reference'], ENT_QUOTES, 'UTF-8'), '</div>';
        echo '</div>';
    }
    elseif ($payment_status === 'waived' || empty($order['payment_required']))
    {
        echo '<div class="infobox"><strong>', $txt['betterads_order_no_payment_title'], '</strong><br>', $txt['betterads_order_no_payment'], '</div>';
    }
    elseif ($payment_status === 'refunded')
    {
        echo '<div class="information"><strong>', $txt['betterads_order_refunded_title'], '</strong><br>', $txt['betterads_order_refunded'], '</div>';
    }
    else
    {
        echo '<h4>', $txt['betterads_order_payment_heading'], '</h4>';
        if (in_array($request_status, array('approved', 'completed'), true))
            echo '<p><strong>', $txt['betterads_order_payment_approved'], '</strong></p>';
        else
            echo '<p>', $txt['betterads_order_payment_intro'], '</p>';

        if (!empty($order['payment_instructions']))
            echo '<div class="information betterads-order-instructions">', nl2br(htmlspecialchars((string)$order['payment_instructions'], ENT_QUOTES, 'UTF-8')), '</div>';

        if (!empty($context['betterads_order_can_pay']))
            echo '<div class="betterads-order-pay-actions"><a class="button betterads-pay-button" href="', htmlspecialchars((string)$context['betterads_order_payment_url'], ENT_QUOTES, 'UTF-8'), '" target="_blank" rel="noopener noreferrer">', $txt['betterads_order_pay_now'], '</a></div>';
        elseif (empty($order['payment_url']))
            echo '<div class="information">', $txt['betterads_order_payment_not_configured'], '</div>';

        echo '<p class="smalltext betterads-payment-safety">', $txt['betterads_order_payment_safety'], '</p>';
    }

    echo '</div>';

    if (!empty($context['betterads_order_campaign']))
    {
        $campaign = $context['betterads_order_campaign'];
        $unit = $campaign['unit'];

        echo '<div class="cat_bar betterads-advertiser-performance-heading"><h3 class="catbg">', $txt['betterads_advertiser_performance_title'], '</h3></div>';
        echo '<div class="betterads-advertiser-dashboard">';

        echo '<div class="betterads-advertiser-campaign-head">';
        echo '<div><strong>', htmlspecialchars((string)$unit['name'], ENT_QUOTES, 'UTF-8'), '</strong>';
        echo '<span>', htmlspecialchars((string)$campaign['placement_label'], ENT_QUOTES, 'UTF-8'), '</span></div>';
        echo '<div><span class="betterads-campaign-status betterads-campaign-status-', htmlspecialchars((string)$campaign['status'], ENT_QUOTES, 'UTF-8'), '">', htmlspecialchars((string)$campaign['status_label'], ENT_QUOTES, 'UTF-8'), '</span></div>';
        echo '</div>';

        echo '<p class="betterads-advertiser-performance-intro">', $txt['betterads_advertiser_performance_intro'], '</p>';

        $range_labels = array(
            'today' => $txt['betterads_stats_today'],
            'seven' => $txt['betterads_stats_7_days'],
            'thirty' => $txt['betterads_stats_30_days'],
            'lifetime' => $txt['betterads_stats_lifetime'],
        );

        echo '<div class="betterads-advertiser-stats-grid">';
        foreach ($range_labels as $range_key => $range_label)
        {
            $stats = $campaign['ranges'][$range_key] ?? array('impressions'=>0, 'clicks'=>0, 'ctr'=>0.0);

            echo '<div class="betterads-advertiser-stats-card">';
            echo '<h4>', htmlspecialchars((string)$range_label, ENT_QUOTES, 'UTF-8'), '</h4>';
            echo '<div class="betterads-advertiser-metrics">';
            echo '<div><strong>', number_format((int)$stats['impressions']), '</strong><span>', $txt['betterads_impressions_short'], '</span></div>';
            echo '<div><strong>', number_format((int)$stats['clicks']), '</strong><span>', $txt['betterads_clicks_short'], '</span></div>';
            echo '<div><strong>', number_format((float)$stats['ctr'], 2), '%</strong><span>', $txt['betterads_ctr'], '</span></div>';
            echo '</div></div>';
        }
        echo '</div>';

        echo '<div class="betterads-advertiser-campaign-info">';
        echo '<div><span>', $txt['betterads_advertiser_campaign_status'], '</span><strong>', htmlspecialchars((string)$campaign['status_label'], ENT_QUOTES, 'UTF-8'), '</strong></div>';
        echo '<div><span>', $txt['betterads_advertiser_campaign_placement'], '</span><strong>', htmlspecialchars((string)$campaign['placement_label'], ENT_QUOTES, 'UTF-8'), '</strong></div>';

        if (!empty($campaign['end_at']))
            echo '<div><span>', $txt['betterads_campaign_ends'], '</span><strong>', timeformat((int)$campaign['end_at']), '</strong></div>';

        if ($campaign['days_remaining'] !== null)
        {
            $remaining = (int)$campaign['days_remaining'];
            echo '<div><span>', $txt['betterads_campaign_time_remaining'], '</span><strong>',
                $remaining > 0 ? sprintf($txt['betterads_campaign_days_remaining'], $remaining) : $txt['betterads_campaign_expired'],
                '</strong></div>';
        }

        if (!empty($campaign['last_day']))
        {
            $last_day_text = (string)$campaign['last_day'];
            $last_day_formatted = substr($last_day_text, 0, 4) . '-' . substr($last_day_text, 4, 2) . '-' . substr($last_day_text, 6, 2);
            echo '<div><span>', $txt['betterads_advertiser_stats_updated'], '</span><strong>', htmlspecialchars($last_day_formatted, ENT_QUOTES, 'UTF-8'), '</strong></div>';
        }
        else
            echo '<div><span>', $txt['betterads_advertiser_stats_updated'], '</span><strong>', $txt['betterads_advertiser_no_activity_yet'], '</strong></div>';

        echo '</div>';

        if (!empty($campaign['open_renewal']))
        {
            echo '<div class="information betterads-renewal-notice"><strong>', $txt['betterads_renewal_in_progress_title'], '</strong><br>', $txt['betterads_renewal_in_progress'], '</div>';
            if (!empty($campaign['open_renewal']['order_token']))
                echo '<div class="betterads-renewal-actions"><a class="button" href="', $scripturl, '?action=betterads;sa=order;token=', urlencode((string)$campaign['open_renewal']['order_token']), '">', $txt['betterads_view_renewal_order'], '</a></div>';
        }
        elseif (!empty($campaign['renew_available']))
        {
            echo '<div class="betterads-renewal-panel"><div><strong>', $txt['betterads_renew_campaign_title'], '</strong><span>', sprintf($txt['betterads_renew_campaign_help'], (int)($order['campaign_days'] ?? 30)), '</span></div>';
            echo '<form action="', $scripturl, '?action=betterads;sa=renew" method="post">';
            echo '<input type="hidden" name="token" value="', htmlspecialchars((string)$order['order_token'], ENT_QUOTES, 'UTF-8'), '">';
            echo '<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">';
            echo '<button class="button" type="submit" name="renew_campaign" value="1">', $txt['betterads_renew_campaign_button'], '</button>';
            echo '</form></div>';
        }

        if (!empty($campaign['creative_src']))
        {
            echo '<div class="betterads-advertiser-live-creative"><h4>', $txt['betterads_advertiser_live_creative'], '</h4>';
            echo '<img src="', htmlspecialchars((string)$campaign['creative_src'], ENT_QUOTES, 'UTF-8'), '" alt="" loading="lazy">';
            echo '</div>';
        }

        echo '<p class="smalltext betterads-advertiser-private-note">', $txt['betterads_advertiser_private_note'], '</p>';
        echo '</div>';
    }

    if (!empty($order['message']))
    {
        echo '<details class="betterads-order-notes"><summary>', $txt['betterads_order_notes'], '</summary><div>', nl2br(htmlspecialchars((string)$order['message'], ENT_QUOTES, 'UTF-8')), '</div></details>';
    }

    echo '<div class="betterads-request-actions"><a class="button" href="', $scripturl, '?action=betterads;sa=advertise">', $txt['betterads_request_back_to_options'], '</a></div>';
    echo '</div>';
}
