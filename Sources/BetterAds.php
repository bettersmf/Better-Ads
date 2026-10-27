<?php
if (!defined('SMF')) die('No direct access.');

function betterads_load_language()
{
    loadLanguage('BetterAds');
}

/**
 * dev7a lifecycle-schema guard.
 *
 * Repairs installs where the dev7 PHP files were updated but Package Manager
 * did not create the new lifecycle columns. The operation is idempotent and
 * stops checking after it records a successful schema version.
 */
function betterads_ensure_lifecycle_schema()
{
    global $modSettings, $smcFunc, $sourcedir;

    if (!empty($modSettings['betterads_lifecycle_schema_version'])
        && (int)$modSettings['betterads_lifecycle_schema_version'] >= 1)
        return true;

    require_once($sourcedir . '/Subs.php');
    db_extend('packages');

    $smcFunc['db_add_column'](
        '{db_prefix}betterads_offers',
        array(
            'name' => 'campaign_days',
            'type' => 'smallint',
            'size' => 5,
            'unsigned' => true,
            'default' => 30,
        ),
        array(),
        'ignore'
    );

    $smcFunc['db_add_column'](
        '{db_prefix}betterads_requests',
        array(
            'name' => 'campaign_days',
            'type' => 'smallint',
            'size' => 5,
            'unsigned' => true,
            'default' => 30,
        ),
        array(),
        'ignore'
    );

    $smcFunc['db_add_column'](
        '{db_prefix}betterads_requests',
        array(
            'name' => 'renewal_of_request',
            'type' => 'int',
            'size' => 10,
            'unsigned' => true,
            'default' => 0,
        ),
        array(),
        'ignore'
    );

    updateSettings(array('betterads_lifecycle_schema_version' => 1));
    $modSettings['betterads_lifecycle_schema_version'] = 1;

    return true;
}

/**
 * Return the number of advertising requests still waiting for admin review.
 *
 * Static caching prevents duplicate COUNT queries when both the main Admin
 * menu badge and Better Ads admin area are built during the same request.
 */
function betterads_pending_request_count()
{
    global $smcFunc, $modSettings;

    static $pending = null;

    if ($pending !== null)
        return $pending;

    $pending = 0;

    if (empty($modSettings['betterads_version']))
        return $pending;

    $request = $smcFunc['db_query']('', '
        SELECT COUNT(*)
        FROM {db_prefix}betterads_requests
        WHERE status = {string:pending}',
        array('pending' => 'pending')
    );

    list ($pending) = $smcFunc['db_fetch_row']($request);
    $smcFunc['db_free_result']($request);

    $pending = (int)$pending;

    return $pending;
}

/**
 * Add a red pending-request counter to SMF's main Admin menu link.
 */
function betterads_menu_buttons(&$buttons)
{
    // dev9b: kept as a compatibility no-op for installations that already
    // registered this hook. SMF caches the built main menu, so this hook is
    // not reliable for live pending-request counters.
}

/**
 * Compatibility no-op retained for dev9d installations.
 * dev9e merges into the final rendered native SMF badge.
 */
function betterads_current_action(&$current_action)
{
    return;
}

function betterads_admin_areas(&$admin_areas)
{
    global $txt, $smcFunc, $modSettings, $context;
    betterads_load_language();

    // dev24b: SMF only renders a custom image file when its menu builder can
    // physically find that file in the active/default theme. Theme Lab and
    // custom-theme combinations can make that unreliable. Use a branded icon
    // class with embedded image data instead, so both the dropdown/menu icon
    // and the large Admin Configuration icon render from the same asset.
    if (empty($context['betterads_admin_icon_css_loaded']))
    {
        $context['betterads_admin_icon_css_loaded'] = true;
        if (!isset($context['html_headers']))
            $context['html_headers'] = '';

        $context['html_headers'] .= '<style id="betterads-admin-icon-css">'
            . '.main_icons.better_ads_brand:before,.main_icons.better_ads_brand::before,.large_admin_menu_icon.better_ads_brand:before,.large_admin_menu_icon.better_ads_brand::before{content:none!important;display:none!important;}'

            . '.main_icons.better_ads_brand{'
            . 'display:inline-block!important;'
            . 'width:16px!important;height:16px!important;'
            . 'min-width:16px!important;min-height:16px!important;'
            . 'padding:0!important;margin:0 6px 0 0!important;'
            . 'line-height:16px!important;font-size:0!important;'
            . 'color:transparent!important;text-indent:-9999px!important;'
            . 'overflow:hidden!important;'
            . 'background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACuUlEQVR42o2TT2hcVRTGf+fe92beczJjMoZMyiTaFFpxoQsNgugi0dbuqwFX4l7Jogi6a7pqwYXgxpWC6KoiiAguFFtBqYXGhSL+ocZqTNJkMplMZt7rzHvv3uPCJliQ4AcHDhy+H99ZfMKBzplzl5fM3ByH6soVOD+/5OG8v+sg/H/JXbuqiIgqn06+/PajZyfHo+btPNdcEYfgPTgU473GYSh/tbO1D167+qakz22oqoiqGpG3wtffPfP1/NNTs6tdT+oNgiKqeAURKMTQy5X6iHDty5vL77/yyZOqi3kgIv7Eme9nSrVg9uLlrVylrOocK1lIHgZMV5RaaFhZG1AKcto5PH9f+Fj95OPHROSnAKAIgmCYpDrRc7YcRgydo+xCssJivXCr5Xn1Ec+HV9s0ayMkiepQrAUIADaTjFpckdMzFS016vzQdtS9pzuArNVn8YmYZrPCVxsF96TbrLfKkm0mmH0AAOq51hVOHym00e3IUVNQFs8764YL/RBfN6QbGUE7Z7ReglIJ3QdkgIrw+60BF35VqWjGzp4SRZabq30m7nd0ln/mhacmSI41+K3lGBkb0DlIkGWYkmW+4fkzKhg4y9Q4OIXpqVGurxScOh7zzKmjvHG9YLS3ehD8H0ARqgdNh54ic3SHnmTgaKcB6jyzRyyjD53gpY+tDrZ35eSYavZvQGXE2iTJJdXC7UX30ht64prQbES4sMyNNOKjz0BWfyFK17T87ANBaJ0BCFRVqnPL6xsdl0zPVCuluEQ4Zmgllu0d6GwXmHyP2XCTdDynNv0wg9vdpLjR2VBVsbBkPn+vmexWX/xRq/GDrdYw6W31O9Lt79Z8vzMZJ52JatqJY7szPlnbiRj+8c0XW4vdb+e/gyVzpxcqIHrnrRDQQ3qU/4cHFhYuWWsNxnD4WAMLl+y+72/V7UqZiNMocgAAAABJRU5ErkJggg==)!important;'
            . 'background-size:16px 16px!important;'
            . 'background-position:center center!important;'
            . 'background-repeat:no-repeat!important;'
            . 'background-color:transparent!important;'
            . 'box-shadow:none!important;border:0!important;'
            . 'vertical-align:middle!important;'
            . '}'
            . '.large_admin_menu_icon.better_ads_brand{'
            . 'display:block!important;'
            . 'width:32px!important;height:32px!important;'
            . 'min-width:32px!important;min-height:32px!important;'
            . 'padding:0!important;margin:0 auto 2px!important;'
            . 'line-height:32px!important;font-size:0!important;'
            . 'color:transparent!important;text-indent:-9999px!important;'
            . 'overflow:hidden!important;'
            . 'background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAH1ElEQVR42uWXWWxc5RXHf99376z2ZLzEE9uJ7awNBCjEoBDSliVVCVVpCpVStSo0rdqqSIXCS6laVTKh7QPipYWCVKiIVKkSxEIqAqIgkpAS0pBgjGvqLE6CE8eJt/EynvHM3O07fbhDYjcQReWRTzqauTN3zvnP+f/PcuHzftSnfyWKLZ0atnzGEJ3QucWAkiv/zZYdltZgE5r1GU3r0OeVZaBDtN6mjGFziviD7dQlFoADqMq9lkCgsKzwfRCAVfETBBUnAQRasGyYcWcovNiteSFvOkSzTZlPB/Bx8JbXfrj229c8vvnWhS0LUlG8QDBKgQhGQBSIUiCACHNzKxJeacAoRaHosved7NnuHX2P6fPffOGTQFxMO8Di1+7/2fN5qRwvL+JmRdyzIu6AiHvcF/eoJ26fK26vK26PI263I+7hsrjvlsU9WBK3qyxujyvu2yVxD7nifSgi3/9LTmh+ZatmPh3qY8GJgFKPVt/40I/7//XU6kWPvTToHRjw7XjMougLGsHxQ05tBAMYIwgKlAozA1gKbA3YGmOEoOyxYhH+T7aush98+MNs33PPrxJ5Kh8SqsQO/32njqjvBLCj/Qd3tzb+bfc5/6Vjnn3vXS0cGvZZGIXhMmSiQkHAtRULIpDUQspSLLAhaUE6CnlP8fqIxfCUR23aJpPRvPHacXvxzgGz8c6lmb7n2tttpfaxZYdFJ4FdyX/4Ek2kq6ujcvJkTmZqM6xvinKwK8fXbkmx/d0im9Yl2P5hwPulKupSEI8iiSik46i4BZ4Dx4bAnBlm6xd9dvYpuvJLKMXrmRibksyKmEA8PZd5e74QfPGNKOO5TO4/ze9nikyPOvxpYJqyA388JZR9m+Z4AcEgFqpkwawfFsP4jCIxMcXu3yzk6V3DtEmSc3vKfG9DDU21FmfHyopU3JC/BEBn5dIoPxBqqmyevCdOOaFx1sTBhJyHrAm+KYfiUeA6hsa2FH3FKAf3DLH9gXrW39DAHY924bat5b72Ca5rMLzRN8XiNa3ge/M60nwKLIuSJyxrjLO3X7Pp6hp00uZU3uApxclZYdwRonGLSUcoeoJdpRjfM8oNDUVe+UUbjS3VdI+V2fKNZtK1Ft2nAmbOnuP2Dcv4++ERWJhAzl4EoC/U75y6mHUMWgyZpdVMxKJkamPsnoiwrC7GhiUJuocgZTS3NsU4N1DikTtT3HtLFX46Qecpj1OORVC/mMwXUsRxySxJ0X9uktlyANZ81u1LJoCEPc9g2N7vcUPE57vLfZ75R4HbmmNMj+bY1phgVVOUwcEpToxMsaPUTqlvjI2WsH8QtBVhuZvjD4+/z+obl3O9mkTHa0jUVUHgzIun54cPwloGZnzDwDSyKi105wK+uqTM4cEC2w/mMDGPF3efwm2oZcT1Ofxeid7eCK+/l+X4REQODiA0L6EmFqGgF7CzJw+By+z4LESTlwNgYUThGZh2hGqNOnB0mj/vnsGJKaYtm5tub+X516dw6xo4mfcYc+owfUeojYwy1jNK5tAutezEO+rkYJZN911FoxrBFNMcn4YNd7TA6ey8/n8JIa4J21lQKPFmzwyvnlFQjoMugSmBWwST4cBEgHJyGF1D2prgrutsujJryL6aw/QM0Z6y2Ly6mczWlewbEz7qHcAzl46A+QD8sJW6Bn60sYk1Q7O4rQpjPBxP4ZsIAijl4AYQqATKyxNrvIrRgSxLFxUZSad48tc3sfmulQyV4NkTsPvMDDe7DhaEIgwuJ0IV1vpkybCyPkoiril6gieCGwieKBwDvgE/CDAmwZmpAr2SIfdyP7m+00ysu5Y3s/DyEZe3ztjEXTAKtFLzgn8CBRaCwlJwLhdwbLiE0kLREQJj8AIhMIIx4XiWZBKScQYHHc7myiyNVPHME19GXdPAX//tc+i4YrqgqRaB6nnF/mkUuIgRRCscxyNTpUkmbYqewUVRNopZX1EINNlpnw+6oyA+y5MOv70Z1m9czF43yc5/egz0g1OMsChdwI1FEaWwREKeL9cHUOC4PgszteTKNkeLwlA+YHwyoJTzsB2P2oihpT7GAxvKXLMiTnpZG72S4Jf/MQx95FEYAKwILckzlPa8Tf2KNuxrq4lYas7W9EkAAqWq4pYcP5FnPDvNktWN1M0YMlGbqlZNTXWUdE0VdjpGMR5lyETpzGqO7jdMDPmQBVOIkGiARZF+Jg98wEz1dWT7S3zpakUsoiRUw/8C6OwMVxN3/PzgyKxqbUqpnhPjfGt9M0MFmMSW82VLHSnC0BScH4DCuIFxgUIAbkW9SSGVmSY6eYzTR7NQez20LoLzA6xoTqiu4aKCyRHUxfmnLq6DovcppY/cc+jwM8+uW9u9q6d0bMzYfiKqZssifgAmEEyAsgIlGFXp24K2wIoqElEfb2ycmSkL6pvQcaUigSc3tQX+wq+sTfzukYM9a954aN3d0hVsU+FeOCcdohXaCM9e33j/bbt++vDVjZkUBJWkGQkrSCp24TO5KO7AgLJARypaM2FbP52FHU/3juY7D3xd8fMPBKPhEgAAHVqxzQi/amXlxoesq+rWWZbExRiMVPY/M2diXZheFRAKEIPMqTYxuszJ6cN8tO9pxRODQoeGbeYyTyUdWqlLhsRnOvrCU0XHlbrt0Nz2lo2IFhH1sfH/WejryoN/zs5/Aa4OGWAOmKEYAAAAAElFTkSuQmCC)!important;'
            . 'background-size:32px 32px!important;'
            . 'background-position:center center!important;'
            . 'background-repeat:no-repeat!important;'
            . 'background-color:transparent!important;'
            . 'box-shadow:none!important;border:0!important;'
            . '}'
            . '</style>';
    }

    $pending = betterads_pending_request_count();

    $label = $txt['betterads_admin_title'];
    if ($pending > 0)
        $label .= ' (' . $pending . ')';

    $admin_areas['config']['areas']['betterads'] = array(
        'label' => $label,
        'file' => 'BetterAds.php',
        'function' => 'betterads_admin',
        'permission' => array('admin_forum'),
        'icon' => 'better_ads_brand',
    );
}


function betterads_actions(&$actionArray)
{
    $actionArray['betterads'] = array('BetterAds.php', 'betterads_action');
}

function betterads_action()
{
    $sa = isset($_REQUEST['sa']) ? (string)$_REQUEST['sa'] : '';
    if ($sa === 'impression')
        betterads_action_impression();
    elseif ($sa === 'click')
        betterads_action_click();
    elseif ($sa === 'advertise')
    {
        betterads_action_advertise();
        return;
    }
    elseif ($sa === 'request')
    {
        betterads_action_request();
        return;
    }
    elseif ($sa === 'order')
    {
        betterads_action_order();
        return;
    }
    elseif ($sa === 'renew')
    {
        betterads_action_renew();
        return;
    }

    http_response_code(404);
    exit;
}


function betterads_public_destination_url($url)
{
    global $scripturl;

    $target = betterads_safe_target_url($url);
    if ($target === '')
        return '';

    if (strpos($target, '?') === 0)
        return $scripturl . $target;

    if (strpos($target, '/') === 0 && !preg_match('~^//~', $target))
    {
        $parts = parse_url($scripturl);
        $root = (!empty($parts['scheme']) ? $parts['scheme'] . '://' : '')
            . (!empty($parts['host']) ? $parts['host'] : '')
            . (!empty($parts['port']) ? ':' . $parts['port'] : '');
        return $root . $target;
    }

    return $target;
}

function betterads_advertise_offers_from_settings($enabled_only = true)
{
    global $smcFunc;

    betterads_ensure_lifecycle_schema();

    $labels = betterads_placement_labels();
    $offers = array();

    $where = $enabled_only ? 'WHERE enabled = {int:enabled}' : '';
    $params = $enabled_only ? array('enabled' => 1) : array();

    $request = $smcFunc['db_query']('', '
        SELECT id_offer, enabled, placement_key, title, price_text, description, payment_required, payment_url, payment_instructions, campaign_days, sort_order, created_at, updated_at
        FROM {db_prefix}betterads_offers
        ' . $where . '
        ORDER BY sort_order, id_offer',
        $params
    );

    while ($row = $smcFunc['db_fetch_assoc']($request))
    {
        $placement = trim((string)$row['placement_key']);
        $title = trim((string)$row['title']);

        if ($title === '' && isset($labels[$placement]))
            $title = (string)$labels[$placement];

        if ($title === '')
            $title = 'Advertising Option #' . (int)$row['id_offer'];

        $offers[] = array(
            // Keep slot as a compatibility alias for dev17 request URLs/templates.
            'slot' => (int)$row['id_offer'],
            'id_offer' => (int)$row['id_offer'],
            'enabled' => !empty($row['enabled']),
            'placement' => $placement,
            'placement_label' => isset($labels[$placement]) ? (string)$labels[$placement] : '',
            'title' => $title,
            'price' => trim((string)$row['price_text']),
            'description' => trim((string)$row['description']),
            'payment_required' => !empty($row['payment_required']),
            'payment_url' => trim((string)$row['payment_url']),
            'payment_instructions' => trim((string)$row['payment_instructions']),
            'campaign_days' => max(1, (int)$row['campaign_days']),
            'sort_order' => (int)$row['sort_order'],
            'created_at' => (int)$row['created_at'],
            'updated_at' => (int)$row['updated_at'],
        );
    }

    $smcFunc['db_free_result']($request);

    return $offers;
}

function betterads_action_advertise()
{
    global $context, $modSettings, $settings;

    if (empty($modSettings['betterads_enabled']) || empty($modSettings['betterads_advertise_enabled']))
    {
        http_response_code(404);
        exit;
    }

    betterads_load_language();
    loadTemplate('BetterAds');

    $title = trim((string)($modSettings['betterads_advertise_title'] ?? 'Advertise With Us'));
    if ($title === '')
        $title = 'Advertise With Us';

    $body = trim((string)($modSettings['betterads_advertise_body'] ?? 'Interested in advertising with us? Contact us to discuss available placements and campaign options.'));
    $cta_text = trim((string)($modSettings['betterads_advertise_cta_text'] ?? 'Contact Us'));
    if ($cta_text === '')
        $cta_text = 'Contact Us';

    $context['page_title'] = $title;
    $context['sub_template'] = 'betterads_advertise';
    $context['betterads_advertise_title'] = $title;
    $context['betterads_advertise_body'] = $body;
    $context['betterads_advertise_cta_text'] = $cta_text;
    $context['betterads_advertise_cta_url'] = betterads_public_destination_url($modSettings['betterads_advertise_cta_url'] ?? '');
    $context['betterads_advertise_offers'] = betterads_advertise_offers_from_settings();
    $context['betterads_advertise_has_fallback'] = $context['betterads_advertise_cta_url'] !== '';

    if (!isset($context['html_headers']))
        $context['html_headers'] = '';

    if (!empty($settings['default_theme_url']) && strpos($context['html_headers'], 'better_ads.css') === false)
        $context['html_headers'] .= "\n<link rel=\"stylesheet\" href=\"" . htmlspecialchars($settings['default_theme_url'] . '/css/better_ads.css?ba=1.1.0', ENT_QUOTES, 'UTF-8') . '\">';
}


function betterads_get_advertise_offer($id_offer)
{
    global $smcFunc;

    betterads_ensure_lifecycle_schema();

    $id_offer = (int)$id_offer;
    if ($id_offer <= 0)
        return false;

    $labels = betterads_placement_labels();

    $request = $smcFunc['db_query']('', '
        SELECT id_offer, enabled, placement_key, title, price_text, description, payment_required, payment_url, payment_instructions, campaign_days, sort_order, created_at, updated_at
        FROM {db_prefix}betterads_offers
        WHERE id_offer = {int:id_offer}
            AND enabled = {int:enabled}
        LIMIT 1',
        array(
            'id_offer' => $id_offer,
            'enabled' => 1,
        )
    );

    $row = $smcFunc['db_fetch_assoc']($request);
    $smcFunc['db_free_result']($request);

    if (!$row)
        return false;

    $placement = trim((string)$row['placement_key']);
    $title = trim((string)$row['title']);

    if ($title === '' && isset($labels[$placement]))
        $title = (string)$labels[$placement];

    if ($title === '')
        $title = 'Advertising Option #' . $id_offer;

    return array(
        'slot' => $id_offer,
        'id_offer' => $id_offer,
        'enabled' => true,
        'placement' => $placement,
        'placement_label' => isset($labels[$placement]) ? (string)$labels[$placement] : '',
        'title' => $title,
        'price' => trim((string)$row['price_text']),
        'description' => trim((string)$row['description']),
        'payment_required' => !empty($row['payment_required']),
        'payment_url' => trim((string)$row['payment_url']),
        'payment_instructions' => trim((string)$row['payment_instructions']),
        'campaign_days' => max(1, (int)$row['campaign_days']),
        'sort_order' => (int)$row['sort_order'],
        'created_at' => (int)$row['created_at'],
        'updated_at' => (int)$row['updated_at'],
    );
}

function betterads_request_web_url($url)
{
    $url = trim((string)$url);
    if ($url === '')
        return '';

    if (preg_match('~[\r\n]~', $url))
        return false;

    return preg_match('~^https?://~i', $url) ? $url : false;
}

function betterads_generate_order_token()
{
    try
    {
        return bin2hex(random_bytes(24));
    }
    catch (Exception $e)
    {
        return hash('sha256', uniqid((string)mt_rand(), true) . microtime(true));
    }
}

function betterads_get_order_by_token($token)
{
    global $smcFunc;

    $token = trim((string)$token);
    if ($token === '' || !preg_match('~^[a-f0-9]{48,64}$~i', $token))
        return false;

    $request = $smcFunc['db_query']('', '
        SELECT *
        FROM {db_prefix}betterads_requests
        WHERE order_token = {string:token}
        LIMIT 1',
        array('token' => $token)
    );

    $row = $smcFunc['db_fetch_assoc']($request);
    $smcFunc['db_free_result']($request);

    return $row ?: false;
}

function betterads_renewal_root_request_id(array $order)
{
    return !empty($order['renewal_of_request'])
        ? (int)$order['renewal_of_request']
        : (int)$order['id_request'];
}

function betterads_find_open_renewal($root_request_id, $id_unit)
{
    global $smcFunc;

    $root_request_id = (int)$root_request_id;
    $id_unit = (int)$id_unit;
    if ($root_request_id <= 0 || $id_unit <= 0)
        return false;

    $request = $smcFunc['db_query']('', '
        SELECT id_request, order_token, status, payment_status
        FROM {db_prefix}betterads_requests
        WHERE renewal_of_request = {int:root_request}
            AND id_unit = {int:id_unit}
            AND status IN ({string:pending}, {string:approved})
        ORDER BY id_request DESC
        LIMIT 1',
        array(
            'root_request' => $root_request_id,
            'id_unit' => $id_unit,
            'pending' => 'pending',
            'approved' => 'approved',
        )
    );

    $row = $smcFunc['db_fetch_assoc']($request);
    $smcFunc['db_free_result']($request);

    return $row ?: false;
}

function betterads_action_renew()
{
    global $smcFunc, $user_info;

    betterads_ensure_lifecycle_schema();

    if (empty($_POST['renew_campaign']))
    {
        http_response_code(404);
        exit;
    }

    checkSession('post');

    $token = trim((string)($_POST['token'] ?? ''));
    $order = betterads_get_order_by_token($token);
    if (!$order || empty($order['id_unit']) || (string)$order['status'] !== 'completed')
        fatal_lang_error('betterads_renew_unavailable', false);

    $unit = betterads_get_unit_with_placement((int)$order['id_unit']);
    if (!$unit || empty($unit['end_at']))
        fatal_lang_error('betterads_renew_unavailable', false);

    $days_left = ((int)$unit['end_at'] - time()) / 86400;
    if ($days_left > 30)
        fatal_lang_error('betterads_renew_too_early', false);

    $root_request_id = betterads_renewal_root_request_id($order);
    $open = betterads_find_open_renewal($root_request_id, (int)$order['id_unit']);
    if ($open)
        redirectexit('action=betterads;sa=order;token=' . urlencode((string)$open['order_token']));

    $now = time();
    $renew_token = betterads_generate_order_token();
    $payment_required = !empty($order['payment_required']) ? 1 : 0;
    $payment_status = $payment_required ? 'unpaid' : 'waived';

    $smcFunc['db_insert']('insert',
        '{db_prefix}betterads_requests',
        array(
            'id_member' => 'int',
            'id_unit' => 'int',
            'offer_slot' => 'int',
            'offer_title' => 'string-160',
            'placement_key' => 'string-60',
            'price_text' => 'string-80',
            'payment_required' => 'int',
            'payment_url' => 'string-500',
            'payment_instructions' => 'string',
            'payment_status' => 'string-20',
            'payment_reference' => 'string-160',
            'paid_at' => 'int',
            'order_token' => 'string-64',
            'campaign_days' => 'int',
            'renewal_of_request' => 'int',
            'contact_name' => 'string-160',
            'contact_email' => 'string-255',
            'company' => 'string-160',
            'website_url' => 'string-500',
            'creative_url' => 'string-500',
            'message' => 'string',
            'status' => 'string-20',
            'created_at' => 'int',
            'updated_at' => 'int',
        ),
        array(
            (int)($user_info['id'] ?? 0),
            (int)$order['id_unit'],
            (int)$order['offer_slot'],
            (string)$order['offer_title'],
            (string)$order['placement_key'],
            (string)$order['price_text'],
            $payment_required,
            (string)$order['payment_url'],
            (string)$order['payment_instructions'],
            $payment_status,
            '',
            0,
            $renew_token,
            max(1, (int)($order['campaign_days'] ?? 30)),
            $root_request_id,
            (string)$order['contact_name'],
            (string)$order['contact_email'],
            (string)$order['company'],
            (string)$order['website_url'],
            (string)$order['creative_url'],
            sprintf('Renewal of advertising request #%d.', $root_request_id),
            'pending',
            $now,
            $now,
        ),
        array('id_request')
    );

    $id_request = (int)$smcFunc['db_insert_id']('{db_prefix}betterads_requests', 'id_request');

    betterads_notify_admins_new_request(
        $id_request,
        array(
            'title' => (string)$order['offer_title'],
            'price' => (string)$order['price_text'],
            'placement' => (string)$order['placement_key'],
        ),
        array(
            'company' => (string)$order['company'],
            'contact_name' => (string)$order['contact_name'],
            'contact_email' => (string)$order['contact_email'],
        )
    );


    $submitted_renewal = betterads_get_request($id_request);
    if ($submitted_renewal)
        betterads_notify_advertiser($submitted_renewal, 'renewal_submitted');

    redirectexit('action=betterads;sa=order;token=' . urlencode($renew_token));
}

function betterads_action_order()
{
    global $context, $modSettings, $settings, $scripturl, $txt, $user_info;

    betterads_ensure_lifecycle_schema();

    // Existing orders must remain viewable even if the public
    // "Advertise With Us" page is later disabled. Only the master Better Ads
    // switch can disable order access.
    if (empty($modSettings['betterads_enabled']))
    {
        betterads_load_language();
        fatal_lang_error('betterads_order_unavailable', false);
    }

    betterads_load_language();
    loadTemplate('BetterAds');

    $order = false;
    $token = trim((string)($_REQUEST['token'] ?? ''));

    if ($token !== '')
        $order = betterads_get_order_by_token($token);
    elseif (!empty($_REQUEST['request']) && allowedTo('admin_forum'))
        $order = betterads_get_request((int)$_REQUEST['request']);

    if (!$order)
        fatal_lang_error('betterads_order_not_found', false);

    $payment_url = betterads_public_destination_url((string)($order['payment_url'] ?? ''));

    $context['page_title'] = sprintf($txt['betterads_order_page_title'], (int)$order['id_request']);
    $context['sub_template'] = 'betterads_order';
    $context['betterads_order'] = $order;
    $context['betterads_order_payment_url'] = $payment_url;
    $context['betterads_order_can_pay'] =
        !empty($order['payment_required'])
        && in_array((string)$order['status'], array('approved', 'completed'), true)
        && !in_array((string)$order['payment_status'], array('paid', 'waived', 'refunded'), true)
        && $payment_url !== '';

    $context['betterads_order_status_label'] = $txt['betterads_request_status_' . $order['status']] ?? ucfirst((string)$order['status']);
    $context['betterads_order_payment_label'] = $txt['betterads_payment_status_' . $order['payment_status']] ?? ucfirst((string)$order['payment_status']);

    if (!empty($order['creative_url']))
        $context['betterads_order_creative_src'] = betterads_image_src((string)$order['creative_url']);
    else
        $context['betterads_order_creative_src'] = '';

    // Once a request is linked to an ad unit, the same private order URL
    // becomes the advertiser's read-only campaign performance dashboard.
    $context['betterads_order_campaign'] = false;
    if (!empty($order['id_unit']))
    {
        $campaign_unit = betterads_get_unit_with_placement((int)$order['id_unit']);
        if ($campaign_unit)
        {
            $campaign_status = betterads_admin_status($campaign_unit);
            $campaign_status_key = 'betterads_campaign_status_' . $campaign_status;

            $campaign_end = (int)($campaign_unit['end_at'] ?? 0);
            $campaign_start = (int)($campaign_unit['start_at'] ?? 0);
            $days_remaining = $campaign_end > 0 ? (int)ceil(($campaign_end - time()) / 86400) : null;
            $renewal_root = betterads_renewal_root_request_id($order);
            $open_renewal = betterads_find_open_renewal($renewal_root, (int)$campaign_unit['id_unit']);

            $context['betterads_order_campaign'] = array(
                'unit' => $campaign_unit,
                'ranges' => betterads_get_stats_ranges((int)$campaign_unit['id_unit']),
                'last_day' => betterads_get_stats_last_day((int)$campaign_unit['id_unit']),
                'status' => $campaign_status,
                'status_label' => $txt[$campaign_status_key] ?? ucfirst(str_replace('_', ' ', $campaign_status)),
                'placement_label' => betterads_placement_labels()[(string)$campaign_unit['placement_key']]
                    ?? (string)$campaign_unit['placement_key'],
                'creative_src' => ($campaign_unit['unit_type'] ?? '') === 'image'
                    ? betterads_image_src((string)($campaign_unit['image_url'] ?? ''))
                    : '',
                'start_at' => $campaign_start,
                'end_at' => $campaign_end,
                'days_remaining' => $days_remaining,
                'renew_available' => $campaign_end > 0 && $days_remaining <= 30 && !$open_renewal,
                'open_renewal' => $open_renewal,
            );
        }
    }

    if (!isset($context['html_headers']))
        $context['html_headers'] = '';

    if (!empty($settings['default_theme_url']) && strpos($context['html_headers'], 'better_ads.css') === false)
        $context['html_headers'] .= "\n<link rel=\"stylesheet\" href=\"" . htmlspecialchars($settings['default_theme_url'] . '/css/better_ads.css?ba=1.1.0', ENT_QUOTES, 'UTF-8') . '\">';
}

/**
 * Send transactional advertising lifecycle email to the advertiser.
 *
 * All messages use the same private order URL so the recipient always has one
 * bookmark from initial request through payment, campaign stats and renewal.
 */
function betterads_notify_advertiser(array $request, $event)
{
    global $scripturl, $sourcedir, $mbname;

    $email = trim((string)($request['contact_email'] ?? ''));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
        return false;

    $token = trim((string)($request['order_token'] ?? ''));
    if ($token === '')
        return false;

    require_once($sourcedir . '/Subs-Post.php');

    $id_request = (int)($request['id_request'] ?? 0);
    $package = trim((string)($request['offer_title'] ?? 'Advertising'));
    $placement = trim((string)($request['placement_key'] ?? ''));
    $name = trim((string)($request['contact_name'] ?? ''));
    $order_url = $scripturl . '?action=betterads;sa=order;token=' . urlencode($token);

    $subject_suffix = 'Advertising update';
    $headline = 'There is an update to your advertising request.';
    $body = '';

    switch ((string)$event)
    {
        case 'submitted':
            $subject_suffix = 'Advertising request received';
            $headline = 'We received your advertising request.';
            $body = "Your request is now waiting for review.";
            break;

        case 'renewal_submitted':
            $subject_suffix = 'Advertising renewal received';
            $headline = 'We received your campaign renewal request.';
            $body = "Your renewal is now waiting for review.";
            break;

        case 'approved':
            $subject_suffix = 'Advertising request approved';
            $headline = 'Your advertising request has been approved.';
            if (!empty($request['payment_required'])
                && !in_array((string)($request['payment_status'] ?? ''), array('paid', 'waived'), true))
                $body = "Open your private order page to review the approval and complete payment.";
            else
                $body = "Your request is approved and ready for the next campaign step.";
            break;

        case 'declined':
            $subject_suffix = 'Advertising request update';
            $headline = 'Your advertising request was not approved.';
            $body = "Your private order page contains the current request status.";
            break;

        case 'payment_confirmed':
            $subject_suffix = 'Advertising payment confirmed';
            $headline = 'Your advertising payment has been confirmed.';
            $body = "Your campaign can now move forward. You can follow its status from your private order page.";
            break;

        case 'campaign_created':
            $subject_suffix = 'Your advertising campaign is ready';
            $headline = 'Your advertising campaign has been created.';
            $body = "Your private order page is now also your campaign performance dashboard. Use it to view campaign status, impressions, clicks and CTR.";
            break;

        case 'renewal_applied':
            $subject_suffix = 'Advertising renewal applied';
            $headline = 'Your campaign renewal has been applied.';
            $body = "The existing campaign has been extended. Your statistics continue on the same campaign dashboard.";
            break;
    }

    $subject = '[' . (string)$mbname . '] ' . $subject_suffix;

    $message = '';
    if ($name !== '')
        $message .= 'Hello ' . $name . ",\n\n";

    $message .= $headline . "\n\n";
    if ($body !== '')
        $message .= $body . "\n\n";

    if ($id_request > 0)
        $message .= 'Request: #' . $id_request . "\n";
    if ($package !== '')
        $message .= 'Package: ' . $package . "\n";
    if ($placement !== '')
        $message .= 'Placement: ' . $placement . "\n";
    if (!empty($request['price_text']))
        $message .= 'Rate: ' . trim((string)$request['price_text']) . "\n";

    $message .= "\nPrivate order / campaign link:\n" . $order_url . "\n";
    $message .= "\nKeep this link private. It provides access to your advertising order and campaign reporting.\n";

    return sendmail($email, $subject, $message);
}

function betterads_notify_admins_new_request($id_request, array $offer, array $form)
{
    global $smcFunc, $modSettings, $scripturl, $sourcedir, $mbname;

    if (empty($modSettings['betterads_notify_admin_email']))
        return;

    $admins = array();
    $request = $smcFunc['db_query']('', '
        SELECT id_member, real_name, email_address
        FROM {db_prefix}members
        WHERE is_activated = {int:activated}
            AND (id_group = {int:admin_group} OR FIND_IN_SET({int:admin_group}, additional_groups) != 0)
            AND email_address != {string:empty}',
        array(
            'activated' => 1,
            'admin_group' => 1,
            'empty' => '',
        )
    );

    while ($row = $smcFunc['db_fetch_assoc']($request))
        $admins[(int)$row['id_member']] = $row;

    $smcFunc['db_free_result']($request);

    if (empty($admins))
        return;

    require_once($sourcedir . '/Subs-Post.php');

    $package = trim((string)($offer['title'] ?? 'Advertising Request'));
    $advertiser = trim((string)($form['company'] ?? ''));
    if ($advertiser === '')
        $advertiser = trim((string)($form['contact_name'] ?? ''));

    $subject = '[' . (string)$mbname . '] New advertising request #' . (int)$id_request;

    $manage_url = $scripturl . '?action=admin;area=betterads;sa=requests';
    $order_url = $scripturl . '?action=betterads;sa=order;request=' . (int)$id_request;

    $message = "A new advertising request has been submitted.\n\n"
        . "Request: #" . (int)$id_request . "\n"
        . "Package: " . $package . "\n"
        . "Advertiser: " . $advertiser . "\n"
        . "Contact: " . trim((string)($form['contact_email'] ?? '')) . "\n";

    if (!empty($offer['price']))
        $message .= "Rate: " . trim((string)$offer['price']) . "\n";
    if (!empty($offer['placement']))
        $message .= "Placement: " . trim((string)$offer['placement']) . "\n";

    $message .= "\nReview advertising requests:\n" . $manage_url . "\n";

    foreach ($admins as $admin)
        sendmail((string)$admin['email_address'], $subject, $message);
}

function betterads_action_request()
{
    global $context, $modSettings, $settings, $smcFunc, $user_info, $scripturl, $txt;

    if (empty($modSettings['betterads_enabled']) || empty($modSettings['betterads_advertise_enabled']))
    {
        http_response_code(404);
        exit;
    }

    betterads_load_language();
    loadTemplate('BetterAds');

    $slot = isset($_REQUEST['offer']) ? (int)$_REQUEST['offer'] : 0;
    $offer = betterads_get_advertise_offer($slot);

    if (!$offer)
    {
        http_response_code(404);
        exit;
    }

    $context['page_title'] = $txt['betterads_request_page_title'];
    $context['sub_template'] = 'betterads_request';
    $context['betterads_request_offer'] = $offer;
    $context['betterads_request_submitted'] = !empty($_GET['submitted']);
    $context['betterads_request_errors'] = array();

    $default_name = !$user_info['is_guest'] ? (string)($user_info['name'] ?? '') : '';
    $default_email = !$user_info['is_guest'] ? (string)($user_info['email'] ?? '') : '';

    $context['betterads_request_form'] = array(
        'contact_name' => $default_name,
        'contact_email' => $default_email,
        'company' => '',
        'website_url' => '',
        'creative_url' => '',
        'message' => '',
    );

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ad_request']))
    {
        checkSession();

        $form = array(
            'contact_name' => trim((string)($_POST['contact_name'] ?? '')),
            'contact_email' => trim((string)($_POST['contact_email'] ?? '')),
            'company' => trim((string)($_POST['company'] ?? '')),
            'website_url' => trim((string)($_POST['website_url'] ?? '')),
            'creative_url' => trim((string)($_POST['creative_url'] ?? '')),
            'message' => trim((string)($_POST['message'] ?? '')),
        );

        $limits = array(
            'contact_name' => 160,
            'contact_email' => 255,
            'company' => 160,
            'website_url' => 500,
            'creative_url' => 500,
            'message' => 6000,
        );

        foreach ($limits as $key => $max)
        {
            if (function_exists('mb_substr'))
                $form[$key] = mb_substr($form[$key], 0, $max, 'UTF-8');
            else
                $form[$key] = substr($form[$key], 0, $max);
        }

        $context['betterads_request_form'] = $form;

        if ($form['contact_name'] === '')
            $context['betterads_request_errors'][] = $txt['betterads_request_error_name'];

        if ($form['contact_email'] === '' || !filter_var($form['contact_email'], FILTER_VALIDATE_EMAIL))
            $context['betterads_request_errors'][] = $txt['betterads_request_error_email'];

        $website = betterads_request_web_url($form['website_url']);
        if ($form['website_url'] !== '' && $website === false)
            $context['betterads_request_errors'][] = $txt['betterads_request_error_website'];

        $creative = betterads_request_web_url($form['creative_url']);
        if ($form['creative_url'] !== '' && $creative === false)
            $context['betterads_request_errors'][] = $txt['betterads_request_error_creative'];

        $uploaded_creative = '';
        $has_upload = !empty($_FILES['creative_upload']['name']);

        // Only move the uploaded file after the text fields have validated.
        // The uploaded graphic takes precedence over an external creative URL.
        if (empty($context['betterads_request_errors']) && $has_upload)
        {
            $uploaded_creative = betterads_store_upload($_FILES['creative_upload']);
            if ($uploaded_creative === false)
                $context['betterads_request_errors'][] = $txt['betterads_request_error_creative_upload'];
        }

        if (empty($context['betterads_request_errors']))
        {
            $now = time();
            $order_token = betterads_generate_order_token();
            $creative_value = $uploaded_creative !== ''
                ? $uploaded_creative
                : ($creative === false ? '' : (string)$creative);

            $smcFunc['db_insert']('insert',
                '{db_prefix}betterads_requests',
                array(
                    'id_member' => 'int',
                    'offer_slot' => 'int',
                    'offer_title' => 'string-160',
                    'placement_key' => 'string-60',
                    'price_text' => 'string-80',
                    'payment_required' => 'int',
                    'payment_url' => 'string-500',
                    'payment_instructions' => 'string',
                    'payment_status' => 'string-20',
                    'payment_reference' => 'string-160',
                    'paid_at' => 'int',
                    'order_token' => 'string-64',
                    'campaign_days' => 'int',
                    'renewal_of_request' => 'int',
                    'contact_name' => 'string-160',
                    'contact_email' => 'string-255',
                    'company' => 'string-160',
                    'website_url' => 'string-500',
                    'creative_url' => 'string-500',
                    'message' => 'string',
                    'status' => 'string-20',
                    'created_at' => 'int',
                    'updated_at' => 'int',
                ),
                array(
                    (int)($user_info['id'] ?? 0),
                    (int)$offer['slot'],
                    (string)$offer['title'],
                    (string)$offer['placement'],
                    (string)$offer['price'],
                    !empty($offer['payment_required']) ? 1 : 0,
                    (string)$offer['payment_url'],
                    (string)$offer['payment_instructions'],
                    !empty($offer['payment_required']) ? 'unpaid' : 'waived',
                    '',
                    0,
                    $order_token,
                    max(1, (int)($offer['campaign_days'] ?? 30)),
                    0,
                    (string)$form['contact_name'],
                    (string)$form['contact_email'],
                    (string)$form['company'],
                    $website === false ? '' : (string)$website,
                    $creative_value,
                    (string)$form['message'],
                    'pending',
                    $now,
                    $now,
                ),
                array('id_request')
            );

            $id_request = (int)$smcFunc['db_insert_id']('{db_prefix}betterads_requests', 'id_request');
            betterads_notify_admins_new_request($id_request, $offer, $form);

            $submitted_request = betterads_get_request($id_request);
            if ($submitted_request)
                betterads_notify_advertiser($submitted_request, 'submitted');

            redirectexit('action=betterads;sa=order;token=' . urlencode($order_token));
        }
    }

    if (!isset($context['html_headers']))
        $context['html_headers'] = '';

    if (!empty($settings['default_theme_url']) && strpos($context['html_headers'], 'better_ads.css') === false)
        $context['html_headers'] .= "\n<link rel=\"stylesheet\" href=\"" . htmlspecialchars($settings['default_theme_url'] . '/css/better_ads.css?ba=1.1.0', ENT_QUOTES, 'UTF-8') . '\">';
}

function betterads_stats_day()
{
    return (int)gmdate('Ymd');
}

function betterads_stats_increment($id_unit, $field)
{
    global $smcFunc;

    $id_unit = (int)$id_unit;
    if ($id_unit <= 0 || !in_array($field, array('impressions', 'clicks'), true))
        return false;

    // Only known units can accrue statistics.
    if (!betterads_get_unit($id_unit))
        return false;

    $day = betterads_stats_day();
    $column = $field === 'clicks' ? 'clicks' : 'impressions';

    $smcFunc['db_query']('', '
        INSERT INTO {db_prefix}betterads_stats_daily
            (id_unit, stat_day, impressions, clicks)
        VALUES
            ({int:id}, {int:day}, {int:impressions}, {int:clicks})
        ON DUPLICATE KEY UPDATE ' . $column . ' = ' . $column . ' + 1',
        array(
            'id' => $id_unit,
            'day' => $day,
            'impressions' => $column === 'impressions' ? 1 : 0,
            'clicks' => $column === 'clicks' ? 1 : 0,
        )
    );

    return true;
}

function betterads_action_impression()
{
    global $modSettings;

    $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;

    // Statistics are intentionally lightweight. An impression is recorded
    // only when the browser executes the tracker emitted with a rendered ad.
    if (!empty($modSettings['betterads_enabled']) && $id > 0)
        betterads_stats_increment($id, 'impressions');

    if (!headers_sent())
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }
    http_response_code(204);
    exit;
}

function betterads_safe_target_url($url)
{
    $url = trim((string)$url);
    if ($url === '' || preg_match('~[\r\n]~', $url))
        return '';

    // Allow normal web URLs and site-relative destinations only.
    if (preg_match('~^https?://~i', $url))
        return $url;
    if (strpos($url, '/') === 0 || strpos($url, '?') === 0)
        return $url;

    return '';
}

function betterads_action_click()
{
    global $scripturl;

    $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
    $unit = $id > 0 ? betterads_get_unit($id) : false;

    // Click tracking is dev10's first-party path for image/link ads only.
    if (!$unit || ($unit['unit_type'] ?? '') !== 'image')
    {
        redirectexit();
    }

    $target = betterads_safe_target_url($unit['target_url'] ?? '');
    if ($target === '')
        redirectexit();

    betterads_stats_increment($id, 'clicks');

    if (strpos($target, '?') === 0)
        $target = $scripturl . $target;
    elseif (strpos($target, '/') === 0 && !preg_match('~^//~', $target))
    {
        $parts = parse_url($scripturl);
        $root = (!empty($parts['scheme']) ? $parts['scheme'] . '://' : '')
            . (!empty($parts['host']) ? $parts['host'] : '')
            . (!empty($parts['port']) ? ':' . $parts['port'] : '');
        $target = $root . $target;
    }

    if (!headers_sent())
        header('Location: ' . $target, true, 302);
    exit;
}

function betterads_get_lifetime_stats()
{
    global $smcFunc;

    $stats = array();
    $request = $smcFunc['db_query']('', '
        SELECT id_unit, SUM(impressions) AS impressions, SUM(clicks) AS clicks
        FROM {db_prefix}betterads_stats_daily
        GROUP BY id_unit');

    while ($row = $smcFunc['db_fetch_assoc']($request))
    {
        $id = (int)$row['id_unit'];
        $impressions = (int)$row['impressions'];
        $clicks = (int)$row['clicks'];
        $stats[$id] = array(
            'impressions' => $impressions,
            'clicks' => $clicks,
            'ctr' => $impressions > 0 ? ($clicks / $impressions) * 100 : 0.0,
        );
    }
    $smcFunc['db_free_result']($request);

    return $stats;
}


function betterads_get_stats_ranges($id_unit)
{
    global $smcFunc;

    $id_unit = (int)$id_unit;
    if ($id_unit <= 0)
        return false;

    $today = (int)gmdate('Ymd');
    $seven_start = (int)gmdate('Ymd', time() - (6 * 86400));
    $thirty_start = (int)gmdate('Ymd', time() - (29 * 86400));

    $ranges = array(
        'today' => array('impressions'=>0, 'clicks'=>0, 'ctr'=>0.0),
        'seven' => array('impressions'=>0, 'clicks'=>0, 'ctr'=>0.0),
        'thirty' => array('impressions'=>0, 'clicks'=>0, 'ctr'=>0.0),
        'lifetime' => array('impressions'=>0, 'clicks'=>0, 'ctr'=>0.0),
    );

    $request = $smcFunc['db_query']('', '
        SELECT stat_day, impressions, clicks
        FROM {db_prefix}betterads_stats_daily
        WHERE id_unit = {int:id}
        ORDER BY stat_day DESC',
        array('id' => $id_unit)
    );

    while ($row = $smcFunc['db_fetch_assoc']($request))
    {
        $day = (int)$row['stat_day'];
        $impressions = (int)$row['impressions'];
        $clicks = (int)$row['clicks'];

        $ranges['lifetime']['impressions'] += $impressions;
        $ranges['lifetime']['clicks'] += $clicks;

        if ($day >= $thirty_start && $day <= $today)
        {
            $ranges['thirty']['impressions'] += $impressions;
            $ranges['thirty']['clicks'] += $clicks;
        }

        if ($day >= $seven_start && $day <= $today)
        {
            $ranges['seven']['impressions'] += $impressions;
            $ranges['seven']['clicks'] += $clicks;
        }

        if ($day === $today)
        {
            $ranges['today']['impressions'] += $impressions;
            $ranges['today']['clicks'] += $clicks;
        }
    }
    $smcFunc['db_free_result']($request);

    foreach ($ranges as $key => $values)
    {
        $ranges[$key]['ctr'] = $values['impressions'] > 0
            ? ($values['clicks'] / $values['impressions']) * 100
            : 0.0;
    }

    return $ranges;
}

function betterads_get_stats_last_day($id_unit)
{
    global $smcFunc;

    $id_unit = (int)$id_unit;
    if ($id_unit <= 0)
        return 0;

    $request = $smcFunc['db_query']('', '
        SELECT MAX(stat_day) AS last_day
        FROM {db_prefix}betterads_stats_daily
        WHERE id_unit = {int:id}',
        array('id' => $id_unit)
    );

    $row = $smcFunc['db_fetch_assoc']($request);
    $smcFunc['db_free_result']($request);

    return !empty($row['last_day']) ? (int)$row['last_day'] : 0;
}

/**
 * Compatibility stub retained from dev9b.
 *
 * dev9c inserts the visible Admin badge into the final rendered HTML instead
 * of depending on theme-specific menu IDs/classes.
 */
function betterads_load_admin_request_badge()
{
    return;
}

function betterads_load_theme()
{
    global $context, $settings, $modSettings;

    if (empty($modSettings['betterads_enabled']) || (isset($_REQUEST['action']) && $_REQUEST['action'] === 'admin'))
        return;

    loadTemplate('BetterAds');
    if (!isset($context['template_layers']) || !is_array($context['template_layers']))
        $context['template_layers'] = array();
    if (!in_array('betterads', $context['template_layers'], true))
        $context['template_layers'][] = 'betterads';

    if (!isset($context['html_headers'])) $context['html_headers'] = '';
    if (!empty($settings['default_theme_url']) && strpos($context['html_headers'], 'better_ads.css') === false)
        $context['html_headers'] .= "\n<link rel=\"stylesheet\" href=\"" . htmlspecialchars($settings['default_theme_url'] . '/css/better_ads.css?ba=1.1.0', ENT_QUOTES, 'UTF-8') . '\">';
}

function betterads_gallery_available()
{
    global $sourcedir;

    return !empty($sourcedir) && is_file(rtrim($sourcedir, '/\\') . '/BetterGallery.php');
}

function betterads_placement_labels()
{
    global $txt;
    betterads_load_language();

    $labels = array(
        '' => $txt['betterads_placement_none'],
        'global_header' => $txt['betterads_placement_global_header'],
        'global_footer' => $txt['betterads_placement_global_footer'],
        'board_index_top' => $txt['betterads_placement_board_index_top'],
        'board_index_bottom' => $txt['betterads_placement_board_index_bottom'],
        'topic_list_top' => $txt['betterads_placement_topic_list_top'],
        'topic_list_bottom' => $txt['betterads_placement_topic_list_bottom'],
        'topic_list_repeat' => $txt['betterads_placement_topic_list_repeat'],
        'topic_view_top' => $txt['betterads_placement_topic_view_top'],
        'topic_view_bottom' => $txt['betterads_placement_topic_view_bottom'],
        'topic_view_repeat' => $txt['betterads_placement_topic_view_repeat'],
    );

    if (betterads_gallery_available())
    {
        $labels['gallery_index_top'] = $txt['betterads_placement_gallery_index_top'];
        $labels['gallery_index_bottom'] = $txt['betterads_placement_gallery_index_bottom'];
        $labels['gallery_album_top'] = $txt['betterads_placement_gallery_album_top'];
        $labels['gallery_album_bottom'] = $txt['betterads_placement_gallery_album_bottom'];
        $labels['gallery_album_repeat'] = $txt['betterads_placement_gallery_album_repeat'];
    }

    return $labels;
}

function betterads_placement_groups()
{
    global $txt;
    betterads_load_language();
    $groups = array(
        $txt['betterads_group_portal'] => array(''),
        $txt['betterads_group_global'] => array('global_header', 'global_footer'),
        $txt['betterads_group_forum'] => array('board_index_top', 'board_index_bottom', 'topic_list_top', 'topic_list_bottom', 'topic_list_repeat', 'topic_view_top', 'topic_view_bottom', 'topic_view_repeat'),
    );

    if (betterads_gallery_available())
        $groups[$txt['betterads_group_gallery']] = array('gallery_index_top', 'gallery_index_bottom', 'gallery_album_top', 'gallery_album_bottom', 'gallery_album_repeat');

    return $groups;
}


function betterads_admin_status($unit)
{
    if (!empty($unit['archived']))
        return 'archived';

    if (empty($unit['enabled']))
        return 'disabled';

    if (!betterads_unit_has_content($unit))
        return 'needs_content';

    $schedule_state = betterads_schedule_state($unit);
    if ($schedule_state === 'upcoming')
        return 'scheduled';
    if ($schedule_state === 'ended')
        return 'expired';

    return 'active';
}

function betterads_admin_filter_matches($unit, $filter)
{
    $status = (string)($unit['admin_status'] ?? betterads_admin_status($unit));

    if ($filter === 'all')
        return true;
    if ($filter === 'current')
        return $status !== 'archived';

    return $status === $filter;
}

function betterads_get_request($id_request)
{
    global $smcFunc;

    $id_request = (int)$id_request;
    if ($id_request <= 0)
        return false;

    $request = $smcFunc['db_query']('', '
        SELECT *
        FROM {db_prefix}betterads_requests
        WHERE id_request = {int:id_request}
        LIMIT 1',
        array('id_request' => $id_request)
    );

    $row = $smcFunc['db_fetch_assoc']($request);
    $smcFunc['db_free_result']($request);

    return $row ?: false;
}

function betterads_admin()
{
    global $context, $txt, $smcFunc, $scripturl, $modSettings, $settings;

    betterads_ensure_lifecycle_schema();
    isAllowedTo('admin_forum');
    betterads_load_language();
    loadTemplate('BetterAdsAdmin');

    if (!isset($context['html_headers'])) $context['html_headers'] = '';
    if (!empty($settings['default_theme_url']) && strpos($context['html_headers'], 'better_ads.css') === false)
        $context['html_headers'] .= "\n<link rel=\"stylesheet\" href=\"" . htmlspecialchars($settings['default_theme_url'] . '/css/better_ads.css?ba=1.1.0', ENT_QUOTES, 'UTF-8') . '\">';

    $context['page_title'] = $txt['betterads_admin_title'];
    $context['sub_template'] = 'betterads_admin';

    $betterads_admin_sections = array('dashboard', 'ads', 'tiers', 'requests', 'stats', 'settings');
    $context['betterads_admin_section'] = isset($_REQUEST['sa']) ? (string)$_REQUEST['sa'] : 'dashboard';
    if (!in_array($context['betterads_admin_section'], $betterads_admin_sections, true))
        $context['betterads_admin_section'] = 'dashboard';

    if (isset($_POST['save_settings']))
    {
        checkSession();
        $label_text = trim((string) ($_POST['betterads_label_text'] ?? 'ADVERTISEMENT'));
        if ($label_text === '') $label_text = 'ADVERTISEMENT';
        if (function_exists('mb_substr')) $label_text = mb_substr($label_text, 0, 80, 'UTF-8');
        else $label_text = substr($label_text, 0, 80);
        updateSettings(array(
            'betterads_enabled' => !empty($_POST['betterads_enabled']) ? 1 : 0,
            'betterads_show_label' => !empty($_POST['betterads_show_label']) ? 1 : 0,
            'betterads_label_text' => $label_text,
        ));
        redirectexit('action=admin;area=betterads;sa=settings');
    }



    if (isset($_POST['update_ad_request']))
    {
        checkSession();

        $id_request = isset($_POST['id_request']) ? (int)$_POST['id_request'] : 0;
        $status = isset($_POST['request_status']) ? (string)$_POST['request_status'] : '';
        $payment_status = isset($_POST['payment_status']) ? (string)$_POST['payment_status'] : 'unpaid';
        $payment_reference = trim((string)($_POST['payment_reference'] ?? ''));
        $allowed_statuses = array('pending', 'approved', 'declined', 'completed');
        $allowed_payment_statuses = array('unpaid', 'pending', 'paid', 'waived', 'refunded');

        if (function_exists('mb_substr'))
            $payment_reference = mb_substr($payment_reference, 0, 160, 'UTF-8');
        else
            $payment_reference = substr($payment_reference, 0, 160);

        if ($id_request > 0
            && in_array($status, $allowed_statuses, true)
            && in_array($payment_status, $allowed_payment_statuses, true))
        {
            $existing_request = betterads_get_request($id_request);
            $old_status = $existing_request ? (string)$existing_request['status'] : '';
            $old_payment_status = $existing_request ? (string)$existing_request['payment_status'] : '';
            $paid_at = $existing_request ? (int)$existing_request['paid_at'] : 0;

            if ($payment_status === 'paid' && $paid_at <= 0)
                $paid_at = time();

            $smcFunc['db_query']('', '
                UPDATE {db_prefix}betterads_requests
                SET status = {string:status},
                    payment_status = {string:payment_status},
                    payment_reference = {string:payment_reference},
                    paid_at = {int:paid_at},
                    updated_at = {int:updated}
                WHERE id_request = {int:id}',
                array(
                    'status' => $status,
                    'payment_status' => $payment_status,
                    'payment_reference' => $payment_reference,
                    'paid_at' => $paid_at,
                    'updated' => time(),
                    'id' => $id_request,
                )
            );

            $updated_request = betterads_get_request($id_request);
            if ($updated_request)
            {
                if ($old_status !== $status && $status === 'approved')
                    betterads_notify_advertiser($updated_request, 'approved');
                elseif ($old_status !== $status && $status === 'declined')
                    betterads_notify_advertiser($updated_request, 'declined');

                if ($old_payment_status !== $payment_status
                    && in_array($payment_status, array('paid', 'waived'), true))
                    betterads_notify_advertiser($updated_request, 'payment_confirmed');
            }
        }

        redirectexit('action=admin;area=betterads;sa=requests');
    }

    if (isset($_POST['apply_ad_renewal']))
    {
        checkSession();

        $id_request = isset($_POST['id_request']) ? (int)$_POST['id_request'] : 0;
        $renewal = $id_request > 0 ? betterads_get_request($id_request) : false;

        if ($renewal
            && !empty($renewal['renewal_of_request'])
            && !empty($renewal['id_unit'])
            && (string)$renewal['status'] === 'approved'
            && (empty($renewal['payment_required']) || in_array((string)$renewal['payment_status'], array('paid', 'waived'), true)))
        {
            $unit = betterads_get_unit_with_placement((int)$renewal['id_unit']);
            if ($unit)
            {
                $days = max(1, (int)($renewal['campaign_days'] ?? 30));
                $base = max(time(), (int)($unit['end_at'] ?? 0));
                $new_end = $base + ($days * 86400);
                $start_at = (int)($unit['start_at'] ?? 0);

                $smcFunc['db_query']('', '
                    UPDATE {db_prefix}betterads_units
                    SET schedule_mode = {string:schedule_mode},
                        end_at = {int:end_at},
                        archived = {int:not_archived},
                        archived_at = {int:no_archive_time},
                        updated_at = {int:updated}
                    WHERE id_unit = {int:id_unit}',
                    array(
                        'schedule_mode' => $start_at > 0 ? 'window' : 'end_only',
                        'end_at' => $new_end,
                        'not_archived' => 0,
                        'no_archive_time' => 0,
                        'updated' => time(),
                        'id_unit' => (int)$renewal['id_unit'],
                    )
                );

                $smcFunc['db_query']('', '
                    UPDATE {db_prefix}betterads_requests
                    SET status = {string:completed},
                        updated_at = {int:updated}
                    WHERE id_request = {int:id_request}',
                    array(
                        'completed' => 'completed',
                        'updated' => time(),
                        'id_request' => $id_request,
                    )
                );

                $completed_renewal = betterads_get_request($id_request);
                if ($completed_renewal)
                    betterads_notify_advertiser($completed_renewal, 'renewal_applied');
            }
        }

        redirectexit('action=admin;area=betterads;sa=requests');
    }

    if (isset($_POST['add_advertise_offer']))
    {
        checkSession();

        $request = $smcFunc['db_query']('', '
            SELECT COALESCE(MAX(sort_order), 0) AS max_sort
            FROM {db_prefix}betterads_offers',
            array()
        );
        $row = $smcFunc['db_fetch_assoc']($request);
        $smcFunc['db_free_result']($request);

        $next_sort = ((int)($row['max_sort'] ?? 0)) + 10;
        $now = time();

        $smcFunc['db_insert']('insert',
            '{db_prefix}betterads_offers',
            array(
                'enabled' => 'int',
                'placement_key' => 'string-60',
                'title' => 'string-160',
                'price_text' => 'string-80',
                'description' => 'string',
                'payment_required' => 'int',
                'payment_url' => 'string-500',
                'payment_instructions' => 'string',
                'campaign_days' => 'int',
                'sort_order' => 'int',
                'created_at' => 'int',
                'updated_at' => 'int',
            ),
            array(
                0,
                '',
                '',
                '',
                '',
                1,
                '',
                '',
                30,
                $next_sort,
                $now,
                $now,
            ),
            array('id_offer')
        );

        redirectexit('action=admin;area=betterads;sa=tiers');
    }

    if (isset($_POST['save_advertise_offer']))
    {
        checkSession();

        $id_offer = isset($_POST['id_offer']) ? (int)$_POST['id_offer'] : 0;
        if ($id_offer > 0)
        {
            $valid_placements = betterads_placement_labels();

            $placement = trim((string)($_POST['offer_placement'] ?? ''));
            if ($placement !== '' && !isset($valid_placements[$placement]))
                $placement = '';

            $offer_title = trim((string)($_POST['offer_title'] ?? ''));
            $offer_price = trim((string)($_POST['offer_price'] ?? ''));
            $offer_description = trim((string)($_POST['offer_description'] ?? ''));
            $offer_payment_required = !empty($_POST['offer_payment_required']) ? 1 : 0;
            $offer_payment_url = trim((string)($_POST['offer_payment_url'] ?? ''));
            $offer_payment_instructions = trim((string)($_POST['offer_payment_instructions'] ?? ''));
            $offer_campaign_days = max(1, min(3650, (int)($_POST['offer_campaign_days'] ?? 30)));
            $sort_order = max(0, min(65535, (int)($_POST['offer_sort_order'] ?? 0)));

            $offer_title = function_exists('mb_substr') ? mb_substr($offer_title, 0, 160, 'UTF-8') : substr($offer_title, 0, 160);
            $offer_price = function_exists('mb_substr') ? mb_substr($offer_price, 0, 80, 'UTF-8') : substr($offer_price, 0, 80);
            $offer_description = function_exists('mb_substr') ? mb_substr($offer_description, 0, 1000, 'UTF-8') : substr($offer_description, 0, 1000);
            $offer_payment_url = function_exists('mb_substr') ? mb_substr($offer_payment_url, 0, 500, 'UTF-8') : substr($offer_payment_url, 0, 500);
            $offer_payment_instructions = function_exists('mb_substr') ? mb_substr($offer_payment_instructions, 0, 2000, 'UTF-8') : substr($offer_payment_instructions, 0, 2000);

            if ($offer_payment_url !== '' && betterads_safe_target_url($offer_payment_url) === '')
                fatal_lang_error('betterads_payment_error_url', false);

            $smcFunc['db_query']('', '
                UPDATE {db_prefix}betterads_offers
                SET enabled = {int:enabled},
                    placement_key = {string:placement},
                    title = {string:title},
                    price_text = {string:price},
                    description = {string:description},
                    payment_required = {int:payment_required},
                    payment_url = {string:payment_url},
                    payment_instructions = {string:payment_instructions},
                    campaign_days = {int:campaign_days},
                    sort_order = {int:sort_order},
                    updated_at = {int:updated_at}
                WHERE id_offer = {int:id_offer}',
                array(
                    'enabled' => !empty($_POST['offer_enabled']) ? 1 : 0,
                    'placement' => $placement,
                    'title' => $offer_title,
                    'price' => $offer_price,
                    'description' => $offer_description,
                    'payment_required' => $offer_payment_required,
                    'payment_url' => $offer_payment_url,
                    'payment_instructions' => $offer_payment_instructions,
                    'campaign_days' => $offer_campaign_days,
                    'sort_order' => $sort_order,
                    'updated_at' => time(),
                    'id_offer' => $id_offer,
                )
            );
        }

        redirectexit('action=admin;area=betterads;sa=tiers');
    }

    if (isset($_POST['delete_advertise_offer']))
    {
        checkSession();

        $id_offer = isset($_POST['id_offer']) ? (int)$_POST['id_offer'] : 0;
        if ($id_offer > 0)
        {
            // Requests snapshot the package details, so removing a tier never
            // removes or rewrites previously submitted advertising requests.
            $smcFunc['db_query']('', '
                DELETE FROM {db_prefix}betterads_offers
                WHERE id_offer = {int:id_offer}',
                array('id_offer' => $id_offer)
            );
        }

        redirectexit('action=admin;area=betterads;sa=tiers');
    }

    if (isset($_POST['save_advertise_settings']))
    {
        checkSession();

        $link_text = trim((string)($_POST['betterads_advertise_link_text'] ?? 'Advertise With Us'));
        $page_title = trim((string)($_POST['betterads_advertise_title'] ?? 'Advertise With Us'));
        $body = trim((string)($_POST['betterads_advertise_body'] ?? ''));
        $cta_text = trim((string)($_POST['betterads_advertise_cta_text'] ?? 'Contact Us'));
        $cta_url = trim((string)($_POST['betterads_advertise_cta_url'] ?? ''));

        if ($link_text === '') $link_text = 'Advertise With Us';
        if ($page_title === '') $page_title = 'Advertise With Us';
        if ($cta_text === '') $cta_text = 'Contact Us';

        if (function_exists('mb_substr'))
        {
            $link_text = mb_substr($link_text, 0, 80, 'UTF-8');
            $page_title = mb_substr($page_title, 0, 160, 'UTF-8');
            $body = mb_substr($body, 0, 4000, 'UTF-8');
            $cta_text = mb_substr($cta_text, 0, 80, 'UTF-8');
            $cta_url = mb_substr($cta_url, 0, 500, 'UTF-8');
        }
        else
        {
            $link_text = substr($link_text, 0, 80);
            $page_title = substr($page_title, 0, 160);
            $body = substr($body, 0, 4000);
            $cta_text = substr($cta_text, 0, 80);
            $cta_url = substr($cta_url, 0, 500);
        }

        if ($cta_url !== '' && betterads_safe_target_url($cta_url) === '')
            fatal_lang_error('betterads_advertise_error_url', false);

        updateSettings(array(
            'betterads_advertise_enabled' => !empty($_POST['betterads_advertise_enabled']) ? 1 : 0,
            'betterads_advertise_link_text' => $link_text,
            'betterads_advertise_title' => $page_title,
            'betterads_advertise_body' => $body,
            'betterads_advertise_cta_text' => $cta_text,
            'betterads_advertise_cta_url' => $cta_url,
            'betterads_notify_admin_email' => !empty($_POST['betterads_notify_admin_email']) ? 1 : 0,
        ));

        redirectexit('action=admin;area=betterads;sa=settings');
    }


    if (isset($_REQUEST['archive']) && (int) $_REQUEST['archive'] > 0)
    {
        checkSession('get');
        $id = (int) $_REQUEST['archive'];
        $smcFunc['db_query']('', '
            UPDATE {db_prefix}betterads_units
            SET archived={int:yes}, archived_at={int:now}, updated_at={int:now}
            WHERE id_unit={int:id}',
            array('yes'=>1, 'now'=>time(), 'id'=>$id)
        );
        $filter = isset($_REQUEST['filter']) ? (string)$_REQUEST['filter'] : 'current';
        redirectexit('action=admin;area=betterads;sa=ads;filter=' . urlencode($filter));
    }

    if (isset($_REQUEST['restore']) && (int) $_REQUEST['restore'] > 0)
    {
        checkSession('get');
        $id = (int) $_REQUEST['restore'];
        $smcFunc['db_query']('', '
            UPDATE {db_prefix}betterads_units
            SET archived={int:no}, archived_at={int:none}, updated_at={int:now}
            WHERE id_unit={int:id}',
            array('no'=>0, 'none'=>0, 'now'=>time(), 'id'=>$id)
        );
        $filter = isset($_REQUEST['filter']) ? (string)$_REQUEST['filter'] : 'current';
        redirectexit('action=admin;area=betterads;sa=ads;filter=' . urlencode($filter));
    }

    if (isset($_REQUEST['delete']) && (int) $_REQUEST['delete'] > 0)
    {
        checkSession('get');
        $id = (int) $_REQUEST['delete'];
        $unit = betterads_get_unit($id);
        if ($unit && strpos($unit['image_url'], 'local:') === 0)
            betterads_delete_local_image($unit['image_url']);
        $smcFunc['db_query']('', 'DELETE FROM {db_prefix}betterads_placements WHERE id_unit={int:id}', array('id'=>$id));
        // A permanently deleted campaign can no longer own statistics. Remove
        // its daily rows here so the stats table does not accumulate orphans.
        $smcFunc['db_query']('', 'DELETE FROM {db_prefix}betterads_stats_daily WHERE id_unit={int:id}', array('id'=>$id));
        $smcFunc['db_query']('', 'DELETE FROM {db_prefix}betterads_units WHERE id_unit={int:id}', array('id'=>$id));
        $filter = isset($_REQUEST['filter']) ? (string)$_REQUEST['filter'] : 'current';
        redirectexit('action=admin;area=betterads;sa=ads;filter=' . urlencode($filter));
    }

    if (isset($_POST['save_unit']))
    {
        checkSession();
        $id = isset($_POST['id_unit']) ? (int) $_POST['id_unit'] : 0;
        $source_request_id = isset($_POST['source_request_id']) ? (int) $_POST['source_request_id'] : 0;
        $name = trim((string) ($_POST['name'] ?? ''));
        $type = in_array($_POST['unit_type'] ?? '', array('code','image'), true) ? $_POST['unit_type'] : 'code';
        $audience = in_array($_POST['audience'] ?? '', array('everyone','guests','members'), true) ? $_POST['audience'] : 'everyone';
        $target_boards = betterads_ids_to_csv($_POST['target_boards'] ?? array());
        $excluded_boards = betterads_ids_to_csv($_POST['excluded_boards'] ?? array());
        $target_groups = betterads_ids_to_csv($_POST['target_groups'] ?? array());
        if ($audience === 'guests' && $target_groups !== '')
            fatal_lang_error('betterads_error_guest_groups', false);
        $code = (string) ($_POST['ad_code'] ?? '');
        $image_url = trim((string) ($_POST['image_url'] ?? ''));
        $target_url = trim((string) ($_POST['target_url'] ?? ''));
        $alt_text = trim((string) ($_POST['alt_text'] ?? ''));
        $image_size_mode = in_array($_POST['image_size_mode'] ?? '', array('responsive','original','custom'), true) ? $_POST['image_size_mode'] : 'responsive';
        $image_width = max(0, min(5000, (int) ($_POST['image_width'] ?? 0)));
        $image_height = max(0, min(5000, (int) ($_POST['image_height'] ?? 0)));
        $image_align = in_array($_POST['image_align'] ?? '', array('left','center','right'), true) ? $_POST['image_align'] : 'center';
        $maintain_ratio = !empty($_POST['maintain_ratio']) ? 1 : 0;
        $label_position = in_array($_POST['label_position'] ?? '', array('above','below'), true) ? $_POST['label_position'] : 'above';
        $label_align = in_array($_POST['label_align'] ?? '', array('match','left','center','right'), true) ? $_POST['label_align'] : 'match';
        $enabled = !empty($_POST['enabled']) ? 1 : 0;
        $schedule_mode = in_array($_POST['schedule_mode'] ?? '', array('always','window','start_only','end_only'), true) ? $_POST['schedule_mode'] : 'always';
        $start_at = betterads_parse_admin_datetime((string) ($_POST['start_at'] ?? ''));
        $end_at = betterads_parse_admin_datetime((string) ($_POST['end_at'] ?? ''));
        if ($schedule_mode === 'always') { $start_at = 0; $end_at = 0; }
        elseif ($schedule_mode === 'start_only') $end_at = 0;
        elseif ($schedule_mode === 'end_only') $start_at = 0;
        elseif ($schedule_mode === 'window' && ($start_at <= 0 || $end_at <= 0 || $end_at <= $start_at))
            fatal_lang_error('betterads_error_schedule_window', false);
        if ($schedule_mode === 'start_only' && $start_at <= 0) fatal_lang_error('betterads_error_schedule_start', false);
        if ($schedule_mode === 'end_only' && $end_at <= 0) fatal_lang_error('betterads_error_schedule_end', false);
        $placement = (string) ($_POST['placement_key'] ?? '');
        $labels = betterads_placement_labels();
        if (!isset($labels[$placement])) $placement = '';
        $repeat_every = max(1, min(100, (int) ($_POST['repeat_every'] ?? 5)));
        $repeat_start = max(1, min(100, (int) ($_POST['repeat_start'] ?? 3)));
        $repeat_max = max(1, min(20, (int) ($_POST['repeat_max'] ?? 2)));
        $gallery_repeat_mode = (isset($_POST['gallery_repeat_mode']) && $_POST['gallery_repeat_mode'] === 'card') ? 'card' : 'row';
        if (!in_array($placement, array('topic_list_repeat', 'topic_view_repeat', 'gallery_album_repeat'), true))
        {
            $repeat_every = 5;
            $repeat_start = 3;
            $repeat_max = 2;
        }

        if ($name === '') fatal_lang_error('betterads_error_name', false);

        $admin_uploaded_replacement = false;
        if ($type === 'image' && !empty($_FILES['image_upload']['name']))
        {
            $uploaded = betterads_store_upload($_FILES['image_upload']);
            if ($uploaded === false) fatal_lang_error('betterads_error_upload', false);
            if ($id)
            {
                $old = betterads_get_unit($id);
                if ($old && strpos($old['image_url'], 'local:') === 0)
                    betterads_delete_local_image($old['image_url']);
            }
            $image_url = $uploaded;
            $admin_uploaded_replacement = true;
        }

        // A request-owned upload should remain attached to the historical
        // request. Copy it for the new ad so deleting/replacing the ad later
        // cannot remove the advertiser's original submitted creative.
        if (!$id
            && $source_request_id > 0
            && $type === 'image'
            && !$admin_uploaded_replacement
            && strpos($image_url, 'local:') === 0)
        {
            $copied = betterads_duplicate_local_image($image_url);
            if ($copied === false)
                fatal_lang_error('betterads_error_upload', false);
            $image_url = $copied;
        }

        $now = time();
        if ($id > 0 && betterads_get_unit($id))
        {
            $smcFunc['db_query']('', 'UPDATE {db_prefix}betterads_units SET name={string:name}, unit_type={string:type}, ad_code={string:code}, image_url={string:image}, target_url={string:target}, alt_text={string:alt}, image_size_mode={string:size_mode}, image_width={int:image_width}, image_height={int:image_height}, image_align={string:image_align}, maintain_ratio={int:maintain_ratio}, label_position={string:label_position}, label_align={string:label_align}, audience={string:aud}, target_boards={string:target_boards}, excluded_boards={string:excluded_boards}, target_groups={string:target_groups}, enabled={int:enabled}, schedule_mode={string:schedule_mode}, start_at={int:start_at}, end_at={int:end_at}, updated_at={int:now} WHERE id_unit={int:id}', array(
                'name'=>$name,'type'=>$type,'code'=>$code,'image'=>$image_url,'target'=>$target_url,'alt'=>$alt_text,'size_mode'=>$image_size_mode,'image_width'=>$image_width,'image_height'=>$image_height,'image_align'=>$image_align,'maintain_ratio'=>$maintain_ratio,'label_position'=>$label_position,'label_align'=>$label_align,'aud'=>$audience,'target_boards'=>$target_boards,'excluded_boards'=>$excluded_boards,'target_groups'=>$target_groups,'enabled'=>$enabled,'schedule_mode'=>$schedule_mode,'start_at'=>$start_at,'end_at'=>$end_at,'now'=>$now,'id'=>$id,
            ));
        }
        else
        {
            $smcFunc['db_insert']('insert', '{db_prefix}betterads_units', array(
                'name'=>'string','unit_type'=>'string','ad_code'=>'string','image_url'=>'string','target_url'=>'string','alt_text'=>'string','image_size_mode'=>'string','image_width'=>'int','image_height'=>'int','image_align'=>'string','maintain_ratio'=>'int','label_position'=>'string','label_align'=>'string','audience'=>'string','target_boards'=>'string','excluded_boards'=>'string','target_groups'=>'string','enabled'=>'int','schedule_mode'=>'string','start_at'=>'int','end_at'=>'int','archived'=>'int','archived_at'=>'int','created_at'=>'int','updated_at'=>'int'
            ), array($name,$type,$code,$image_url,$target_url,$alt_text,$image_size_mode,$image_width,$image_height,$image_align,$maintain_ratio,$label_position,$label_align,$audience,$target_boards,$excluded_boards,$target_groups,$enabled,$schedule_mode,$start_at,$end_at,0,0,$now,$now), array('id_unit'));
            $id = (int) $smcFunc['db_insert_id']('{db_prefix}betterads_units', 'id_unit');
        }

        $smcFunc['db_query']('', 'DELETE FROM {db_prefix}betterads_placements WHERE id_unit={int:id}', array('id'=>$id));
        if ($placement !== '')
            $smcFunc['db_insert']('insert', '{db_prefix}betterads_placements', array(
                'placement_key'=>'string','id_unit'=>'int','enabled'=>'int','sort_order'=>'int','repeat_every'=>'int','repeat_start'=>'int','repeat_max'=>'int'
            ), array($placement,$id,1,0,$repeat_every,$repeat_start,$repeat_max), array('id_placement'));

        betterads_save_gallery_repeat_mode($id, $placement, $gallery_repeat_mode);

        // dev7: repeat timing belongs to the placement pool, not to one creative.
        // Keep every ad assigned to the same repeat placement on one shared schedule.
        if (in_array($placement, array('topic_list_repeat', 'topic_view_repeat', 'gallery_album_repeat'), true))
            $smcFunc['db_query']('', 'UPDATE {db_prefix}betterads_placements SET repeat_every={int:every}, repeat_start={int:start}, repeat_max={int:max} WHERE placement_key={string:key} AND enabled=1', array(
                'every'=>$repeat_every, 'start'=>$repeat_start, 'max'=>$repeat_max, 'key'=>$placement,
            ));

        if ($source_request_id > 0)
        {
            $source_request = betterads_get_request($source_request_id);

            // Only an approved, not-yet-linked request can be attached.
            if ($source_request
                && $source_request['status'] === 'approved'
                && empty($source_request['id_unit']))
            {
                $smcFunc['db_query']('', '
                    UPDATE {db_prefix}betterads_requests
                    SET id_unit = {int:id_unit},
                        status = {string:status},
                        updated_at = {int:updated_at}
                    WHERE id_request = {int:id_request}',
                    array(
                        'id_unit' => $id,
                        'status' => 'completed',
                        'updated_at' => time(),
                        'id_request' => $source_request_id,
                    )
                );

                $completed_request = betterads_get_request($source_request_id);
                if ($completed_request)
                    betterads_notify_advertiser($completed_request, 'campaign_created');
            }
        }

        redirectexit('action=admin;area=betterads;sa=ads;edit=' . $id);
    }

    $edit_id = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
    $context['betterads_source_request'] = false;

    $context['betterads_edit'] = $edit_id ? betterads_get_unit_with_placement($edit_id) : array(
        'id_unit'=>0,'name'=>'','unit_type'=>'code','ad_code'=>'','image_url'=>'','target_url'=>'','alt_text'=>'','image_size_mode'=>'responsive','image_width'=>0,'image_height'=>0,'image_align'=>'center','maintain_ratio'=>1,'label_position'=>'above','label_align'=>'match','audience'=>'everyone','target_boards'=>'','excluded_boards'=>'','target_groups'=>'','enabled'=>1,'schedule_mode'=>'always','start_at'=>0,'end_at'=>0,'archived'=>0,'archived_at'=>0,'placement_key'=>'','repeat_every'=>5,'repeat_start'=>3,'repeat_max'=>2
    );

    $create_from_request = !$edit_id && isset($_GET['create_from_request']) ? (int)$_GET['create_from_request'] : 0;
    if ($create_from_request > 0)
    {
        $source_request = betterads_get_request($create_from_request);

        if ($source_request
            && $source_request['status'] === 'approved'
            && empty($source_request['id_unit']))
        {
            $ad_name_parts = array('Ad', (string)$source_request['offer_title']);
            if (trim((string)$source_request['company']) !== '')
                $ad_name_parts[] = trim((string)$source_request['company']);
            elseif (trim((string)$source_request['contact_name']) !== '')
                $ad_name_parts[] = trim((string)$source_request['contact_name']);

            $creative_value = trim((string)$source_request['creative_url']);
            $creative_url = strpos($creative_value, 'local:') === 0
                ? $creative_value
                : betterads_request_web_url($creative_value);
            $target_url = betterads_request_web_url((string)$source_request['website_url']);

            $context['betterads_edit']['name'] = implode(' - ', $ad_name_parts);
            $context['betterads_edit']['unit_type'] = $creative_url ? 'image' : 'code';
            $context['betterads_edit']['image_url'] = $creative_url ? (string)$creative_url : '';
            $context['betterads_edit']['target_url'] = $target_url ? (string)$target_url : '';
            $context['betterads_edit']['alt_text'] = trim((string)$source_request['company']) !== ''
                ? trim((string)$source_request['company'])
                : (string)$source_request['offer_title'];
            $context['betterads_edit']['placement_key'] = (string)$source_request['placement_key'];

            $campaign_days = max(1, (int)($source_request['campaign_days'] ?? 30));
            $context['betterads_edit']['schedule_mode'] = 'window';
            $context['betterads_edit']['start_at'] = time();
            $context['betterads_edit']['end_at'] = time() + ($campaign_days * 86400);

            // Always start generated ads disabled for admin review.
            $context['betterads_edit']['enabled'] = 0;
            $context['betterads_source_request'] = $source_request;
        }
    }
    $all_betterads_units = betterads_get_units_with_placements();
    $context['betterads_stats'] = betterads_get_lifetime_stats();

    $valid_filters = array('current','active','scheduled','expired','disabled','archived','all');
    $context['betterads_filter'] = isset($_GET['filter']) && in_array($_GET['filter'], $valid_filters, true)
        ? (string)$_GET['filter']
        : 'current';

    $context['betterads_filter_counts'] = array_fill_keys($valid_filters, 0);
    $context['betterads_placement_counts'] = array();

    foreach ($all_betterads_units as &$betterads_admin_unit)
    {
        $betterads_admin_unit['has_content'] = betterads_unit_has_content($betterads_admin_unit) ? 1 : 0;
        $betterads_admin_unit['schedule_state'] = betterads_schedule_state($betterads_admin_unit);
        $betterads_admin_unit['admin_status'] = betterads_admin_status($betterads_admin_unit);

        $context['betterads_filter_counts']['all']++;
        if ($betterads_admin_unit['admin_status'] !== 'archived')
            $context['betterads_filter_counts']['current']++;
        if (isset($context['betterads_filter_counts'][$betterads_admin_unit['admin_status']]))
            $context['betterads_filter_counts'][$betterads_admin_unit['admin_status']]++;

        $betterads_key = (string)($betterads_admin_unit['placement_key'] ?? '');
        if ($betterads_key !== ''
            && empty($betterads_admin_unit['archived'])
            && !empty($betterads_admin_unit['enabled'])
            && !empty($betterads_admin_unit['has_content']))
        {
            $context['betterads_placement_counts'][$betterads_key] =
                ($context['betterads_placement_counts'][$betterads_key] ?? 0) + 1;
        }
    }
    unset($betterads_admin_unit);

    $context['betterads_units'] = array_values(array_filter(
        $all_betterads_units,
        function ($unit) use ($context) {
            return betterads_admin_filter_matches($unit, $context['betterads_filter']);
        }
    ));

    // Statistics should represent every campaign, including disabled, expired,
    // and archived ads. The Ads page can still use its normal status filter.
    $context['betterads_stats_units'] = $all_betterads_units;

    $context['betterads_lifecycle_counts'] = array(
        'running' => 0,
        'scheduled' => 0,
        'ending_soon' => 0,
        'expired' => 0,
    );
    $lifecycle_now = time();
    foreach ($all_betterads_units as $lifecycle_unit)
    {
        if (!empty($lifecycle_unit['archived']))
            continue;

        $status = (string)($lifecycle_unit['admin_status'] ?? betterads_admin_status($lifecycle_unit));
        $end_at = (int)($lifecycle_unit['end_at'] ?? 0);

        if ($status === 'scheduled')
            $context['betterads_lifecycle_counts']['scheduled']++;
        elseif ($status === 'expired')
            $context['betterads_lifecycle_counts']['expired']++;
        elseif ($status === 'active')
        {
            $context['betterads_lifecycle_counts']['running']++;
            if ($end_at > $lifecycle_now && $end_at <= ($lifecycle_now + 7 * 86400))
                $context['betterads_lifecycle_counts']['ending_soon']++;
        }
    }

    $context['betterads_stats_detail'] = false;
    $stats_id = isset($_GET['stats']) ? (int)$_GET['stats'] : 0;
    if ($stats_id > 0)
    {
        $stats_unit = betterads_get_unit_with_placement($stats_id);
        if ($stats_unit)
        {
            $context['betterads_stats_detail'] = array(
                'unit' => $stats_unit,
                'ranges' => betterads_get_stats_ranges($stats_id),
            );
        }
    }
    $context['betterads_placements'] = betterads_placement_labels();
    $context['betterads_placement_groups'] = betterads_placement_groups();
    $context['betterads_board_options'] = betterads_admin_board_options();
    $context['betterads_group_options'] = betterads_admin_group_options();
    $context['betterads_preview'] = false;
    $preview_id = isset($_GET['preview']) ? (int) $_GET['preview'] : 0;
    if ($preview_id > 0)
    {
        $preview_unit = betterads_get_unit_with_placement($preview_id);
        if ($preview_unit)
        {
            $preview_copy = $preview_unit;
            $preview_copy['enabled'] = 1;
            $preview_copy['audience'] = 'everyone';
            $preview_copy['schedule_mode'] = 'always';
            $preview_copy['start_at'] = 0;
            $preview_copy['end_at'] = 0;
            $preview_copy['target_boards'] = '';
            $preview_copy['excluded_boards'] = '';
            $preview_copy['target_groups'] = '';
            $preview_copy['archived'] = 0;
            $context['betterads_preview'] = array(
                'unit' => $preview_unit,
                'html' => betterads_render_unit($preview_copy, 'admin_preview'),
            );
        }
    }
    $context['betterads_enabled'] = !empty($modSettings['betterads_enabled']);
    $context['betterads_show_label'] = !isset($modSettings['betterads_show_label']) || !empty($modSettings['betterads_show_label']);
    $context['betterads_label_text'] = trim((string) ($modSettings['betterads_label_text'] ?? 'ADVERTISEMENT'));
    if ($context['betterads_label_text'] === '') $context['betterads_label_text'] = 'ADVERTISEMENT';

    $context['betterads_advertise_enabled'] = !empty($modSettings['betterads_advertise_enabled']);
    $context['betterads_advertise_link_text'] = trim((string)($modSettings['betterads_advertise_link_text'] ?? 'Advertise With Us'));
    $context['betterads_advertise_title'] = trim((string)($modSettings['betterads_advertise_title'] ?? 'Advertise With Us'));
    $context['betterads_advertise_body'] = trim((string)($modSettings['betterads_advertise_body'] ?? 'Interested in advertising with us? Contact us to discuss available placements and campaign options.'));
    $context['betterads_advertise_cta_text'] = trim((string)($modSettings['betterads_advertise_cta_text'] ?? 'Contact Us'));
    $context['betterads_advertise_cta_url'] = trim((string)($modSettings['betterads_advertise_cta_url'] ?? ''));
    $context['betterads_notify_admin_email'] = !isset($modSettings['betterads_notify_admin_email']) || !empty($modSettings['betterads_notify_admin_email']);
    $context['betterads_advertise_has_fallback'] = $context['betterads_advertise_cta_url'] !== '';
    $context['betterads_advertise_offer_placements'] = betterads_placement_labels();
    $context['betterads_request_statuses'] = array(
        'pending' => $txt['betterads_request_status_pending'],
        'approved' => $txt['betterads_request_status_approved'],
        'declined' => $txt['betterads_request_status_declined'],
        'completed' => $txt['betterads_request_status_completed'],
    );
    $context['betterads_payment_statuses'] = array(
        'unpaid' => $txt['betterads_payment_status_unpaid'],
        'pending' => $txt['betterads_payment_status_pending'],
        'paid' => $txt['betterads_payment_status_paid'],
        'waived' => $txt['betterads_payment_status_waived'],
        'refunded' => $txt['betterads_payment_status_refunded'],
    );
    $context['betterads_request_counts'] = array_fill_keys(array_keys($context['betterads_request_statuses']), 0);
    $context['betterads_requests'] = array();

    $request = $smcFunc['db_query']('', '
        SELECT status, COUNT(*) AS total
        FROM {db_prefix}betterads_requests
        GROUP BY status');
    while ($row = $smcFunc['db_fetch_assoc']($request))
        if (isset($context['betterads_request_counts'][$row['status']]))
            $context['betterads_request_counts'][$row['status']] = (int)$row['total'];
    $smcFunc['db_free_result']($request);

    $request = $smcFunc['db_query']('', '
        SELECT *
        FROM {db_prefix}betterads_requests
        ORDER BY created_at DESC, id_request DESC
        LIMIT 50');
    while ($row = $smcFunc['db_fetch_assoc']($request))
    {
        $row['creative_src'] = trim((string)$row['creative_url']) !== ''
            ? betterads_image_src((string)$row['creative_url'])
            : '';
        $row['creative_is_local'] = strpos((string)$row['creative_url'], 'local:') === 0;
        $context['betterads_requests'][] = $row;
    }
    $smcFunc['db_free_result']($request);

    $context['betterads_advertise_offers_admin'] = betterads_advertise_offers_from_settings(false);
}

function betterads_get_unit($id)
{
    global $smcFunc;
    $request = $smcFunc['db_query']('', 'SELECT * FROM {db_prefix}betterads_units WHERE id_unit={int:id} LIMIT 1', array('id'=>(int)$id));
    $row = $smcFunc['db_fetch_assoc']($request);
    $smcFunc['db_free_result']($request);
    return $row ?: false;
}

function betterads_get_unit_with_placement($id)
{
    global $smcFunc;
    $request = $smcFunc['db_query']('', 'SELECT u.*, COALESCE(p.placement_key, {string:empty}) AS placement_key, COALESCE(p.repeat_every, 5) AS repeat_every, COALESCE(p.repeat_start, 3) AS repeat_start, COALESCE(p.repeat_max, 2) AS repeat_max FROM {db_prefix}betterads_units AS u LEFT JOIN {db_prefix}betterads_placements AS p ON (p.id_unit=u.id_unit AND p.enabled=1) WHERE u.id_unit={int:id} LIMIT 1', array('id'=>(int)$id,'empty'=>''));
    $row = $smcFunc['db_fetch_assoc']($request);
    $smcFunc['db_free_result']($request);
    return $row ?: false;
}

function betterads_get_units_with_placements()
{
    global $smcFunc;
    $rows = array();
    $request = $smcFunc['db_query']('', 'SELECT u.*, COALESCE(p.placement_key, {string:empty}) AS placement_key, COALESCE(p.repeat_every, 5) AS repeat_every, COALESCE(p.repeat_start, 3) AS repeat_start, COALESCE(p.repeat_max, 2) AS repeat_max FROM {db_prefix}betterads_units AS u LEFT JOIN {db_prefix}betterads_placements AS p ON (p.id_unit=u.id_unit AND p.enabled=1) ORDER BY u.id_unit DESC', array('empty'=>''));
    while ($row = $smcFunc['db_fetch_assoc']($request)) $rows[] = $row;
    $smcFunc['db_free_result']($request);
    return $rows;
}

function betterads_store_upload($file)
{
    global $boarddir;
    if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || (int)$file['error'] !== UPLOAD_ERR_OK)
        return false;

    // Keep advertiser/admin creative uploads intentionally modest.
    if (!empty($file['size']) && (int)$file['size'] > 8 * 1024 * 1024)
        return false;

    $info = @getimagesize($file['tmp_name']);
    if (!$info) return false;

    $mime_to_ext = array('image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp');
    $mime = $info['mime'] ?? '';
    if (!isset($mime_to_ext[$mime])) return false;

    $storage = rtrim($boarddir, '/\\') . '/better_ads_storage';
    if (!is_dir($storage) && !@mkdir($storage, 0755, true)) return false;

    try
    {
        $token = bin2hex(random_bytes(4));
    }
    catch (Exception $e)
    {
        $token = substr(md5(uniqid('', true)), 0, 8);
    }

    $name = 'ad_' . time() . '_' . $token . '.' . $mime_to_ext[$mime];
    if (!@move_uploaded_file($file['tmp_name'], $storage . '/' . $name)) return false;

    return 'local:' . $name;
}

function betterads_delete_local_image($value)
{
    global $boarddir;
    if (strpos((string)$value, 'local:') !== 0) return;
    $name = basename(substr((string)$value, 6));
    $path = rtrim($boarddir, '/\\') . '/better_ads_storage/' . $name;
    if (is_file($path)) @unlink($path);
}

function betterads_duplicate_local_image($value)
{
    global $boarddir;

    if (strpos((string)$value, 'local:') !== 0)
        return (string)$value;

    $source_name = basename(substr((string)$value, 6));
    $source_path = rtrim($boarddir, '/\\') . '/better_ads_storage/' . $source_name;

    if (!is_file($source_path))
        return false;

    $ext = strtolower(pathinfo($source_name, PATHINFO_EXTENSION));
    if (!in_array($ext, array('jpg','jpeg','png','gif','webp'), true))
        return false;

    try
    {
        $token = bin2hex(random_bytes(4));
    }
    catch (Exception $e)
    {
        $token = substr(md5(uniqid('', true)), 0, 8);
    }

    $new_name = 'ad_' . time() . '_' . $token . '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
    $new_path = rtrim($boarddir, '/\\') . '/better_ads_storage/' . $new_name;

    if (!@copy($source_path, $new_path))
        return false;

    return 'local:' . $new_name;
}

function betterads_image_src($value)
{
    global $boardurl;
    if (strpos((string)$value, 'local:') === 0)
        return rtrim($boardurl, '/') . '/better_ads_storage/' . rawurlencode(basename(substr((string)$value, 6)));
    return (string)$value;
}

function betterads_admin_timezone()
{
    global $user_info;
    $name = !empty($user_info['timezone']) ? (string)$user_info['timezone'] : date_default_timezone_get();
    try { return new DateTimeZone($name); }
    catch (Exception $e) { return new DateTimeZone(date_default_timezone_get()); }
}

function betterads_parse_admin_datetime($value)
{
    $value = trim((string)$value);
    if ($value === '') return 0;
    try {
        $dt = new DateTime($value, betterads_admin_timezone());
        return max(0, (int)$dt->getTimestamp());
    } catch (Exception $e) { return 0; }
}

function betterads_format_admin_datetime($timestamp)
{
    $timestamp = (int)$timestamp;
    if ($timestamp <= 0) return '';
    $dt = new DateTime('@' . $timestamp);
    $dt->setTimezone(betterads_admin_timezone());
    return $dt->format('Y-m-d\\TH:i');
}

function betterads_schedule_allows($unit, $now = null)
{
    if (!$unit || !is_array($unit)) return false;
    $mode = (string)($unit['schedule_mode'] ?? 'always');
    if (!in_array($mode, array('always','window','start_only','end_only'), true)) $mode = 'always';
    if ($mode === 'always') return true;
    $now = $now === null ? time() : (int)$now;
    $start = (int)($unit['start_at'] ?? 0);
    $end = (int)($unit['end_at'] ?? 0);
    if (($mode === 'window' || $mode === 'start_only') && $start > 0 && $now < $start) return false;
    if (($mode === 'window' || $mode === 'end_only') && $end > 0 && $now >= $end) return false;
    if ($mode === 'window') return $start > 0 && $end > $start;
    if ($mode === 'start_only') return $start > 0;
    if ($mode === 'end_only') return $end > 0;
    return true;
}

function betterads_schedule_state($unit, $now = null)
{
    $mode = (string)($unit['schedule_mode'] ?? 'always');
    if ($mode === 'always') return 'always';
    $now = $now === null ? time() : (int)$now;
    $start = (int)($unit['start_at'] ?? 0); $end = (int)($unit['end_at'] ?? 0);
    if (($mode === 'window' || $mode === 'start_only') && $start > $now) return 'upcoming';
    if (($mode === 'window' || $mode === 'end_only') && $end > 0 && $now >= $end) return 'ended';
    return 'active';
}


function betterads_csv_ids($value)
{
    if (is_array($value))
        $parts = $value;
    else
        $parts = preg_split('~[\s,;]+~', trim((string)$value), -1, PREG_SPLIT_NO_EMPTY);

    $ids = array();
    foreach ((array)$parts as $part)
    {
        $id = (int)$part;
        if ($id > 0) $ids[$id] = $id;
    }
    ksort($ids, SORT_NUMERIC);
    return array_values($ids);
}

function betterads_ids_to_csv($value)
{
    return implode(',', betterads_csv_ids($value));
}

function betterads_admin_board_options()
{
    global $smcFunc;
    $out = array();
    $request = $smcFunc['db_query']('', '
        SELECT b.id_board, b.name AS board_name, c.name AS cat_name
        FROM {db_prefix}boards AS b
            INNER JOIN {db_prefix}categories AS c ON (c.id_cat = b.id_cat)
        ORDER BY c.cat_order, b.board_order, b.id_board');
    while ($row = $smcFunc['db_fetch_assoc']($request))
        $out[(int)$row['id_board']] = (string)$row['cat_name'] . ' — ' . (string)$row['board_name'];
    $smcFunc['db_free_result']($request);
    return $out;
}

function betterads_admin_group_options()
{
    global $smcFunc;
    $out = array();
    $request = $smcFunc['db_query']('', '
        SELECT id_group, group_name
        FROM {db_prefix}membergroups
        WHERE id_group > {int:regular}
        ORDER BY group_name',
        array('regular' => 0));
    while ($row = $smcFunc['db_fetch_assoc']($request))
        $out[(int)$row['id_group']] = (string)$row['group_name'];
    $smcFunc['db_free_result']($request);
    return $out;
}

function betterads_targeting_allows($unit)
{
    global $board, $user_info;

    $include_boards = betterads_csv_ids($unit['target_boards'] ?? '');
    $exclude_boards = betterads_csv_ids($unit['excluded_boards'] ?? '');
    $current_board = !empty($board) ? (int)$board : 0;

    // An include list means "only while viewing one of these boards/topics".
    if (!empty($include_boards) && ($current_board <= 0 || !in_array($current_board, $include_boards, true)))
        return false;

    // Exclusions always win when the current page has board context.
    if ($current_board > 0 && !empty($exclude_boards) && in_array($current_board, $exclude_boards, true))
        return false;

    $target_groups = betterads_csv_ids($unit['target_groups'] ?? '');
    if (!empty($target_groups))
    {
        if (!empty($user_info['is_guest'])) return false;

        $viewer_groups = array();
        foreach ((array)($user_info['groups'] ?? array()) as $group_id)
        {
            $group_id = (int)$group_id;
            if ($group_id > 0) $viewer_groups[$group_id] = $group_id;
        }
        foreach (array('id_group', 'id_post_group') as $key)
        {
            $group_id = (int)($user_info[$key] ?? 0);
            if ($group_id > 0) $viewer_groups[$group_id] = $group_id;
        }

        if (empty(array_intersect($target_groups, array_values($viewer_groups))))
            return false;
    }

    return true;
}

function betterads_audience_allows($audience)
{
    global $user_info;
    if ($audience === 'guests') return !empty($user_info['is_guest']);
    if ($audience === 'members') return empty($user_info['is_guest']);
    return true;
}

function betterads_unit_has_content($unit)
{
    if (!$unit || !is_array($unit)) return false;
    if (($unit['unit_type'] ?? 'code') === 'image')
        return trim((string)($unit['image_url'] ?? '')) !== '';
    return trim((string)($unit['ad_code'] ?? '')) !== '';
}

function betterads_unit_is_eligible($unit, $ignore_enabled = false, $ignore_audience = false, $ignore_schedule = false, $ignore_targeting = false)
{
    if (!$unit || !is_array($unit)) return false;
    if (!empty($unit['archived'])) return false;
    if (!$ignore_enabled && empty($unit['enabled'])) return false;
    if (!$ignore_audience && !betterads_audience_allows((string)($unit['audience'] ?? 'everyone'))) return false;
    if (!$ignore_schedule && !betterads_schedule_allows($unit)) return false;
    if (!$ignore_targeting && !betterads_targeting_allows($unit)) return false;
    return betterads_unit_has_content($unit);
}

function betterads_wrap_native_cell($content, $placement, $unit = array())
{
    global $txt, $user_info;

    $native_align = (($unit['unit_type'] ?? '') === 'image' && in_array(($unit['image_align'] ?? 'center'), array('left','center','right'), true))
        ? (string)$unit['image_align']
        : 'center';
    $native_align_class = ' betterads-native-align-' . $native_align;

    // dev21f: dev21e proved the correct alignment value reaches the renderer.
    // The remaining problem is layout, so force the exact native content
    // container into a full-width block and align the shrink-wrapped slot there.
    $native_text_align = $native_align === 'right' ? 'right' : ($native_align === 'left' ? 'left' : 'center');
    $native_container_style = 'display:block !important;width:100% !important;box-sizing:border-box !important;text-align:' . $native_text_align . ' !important;';

    if ($content === '' || $placement === '' || $placement === 'admin_preview')
        return $content;

    $safe_placement = preg_replace('~[^a-z0-9_-]+~i', '-', (string)$placement);

    // Board/index and standalone placements use the forum's own cat_bar/windowbg
    // classes so Theme Lab and third-party themes can style the ad naturally.
    if (in_array($placement, array(
        'header',
        'footer',
        'board_index_top',
        'board_index_bottom',
    ), true))
    {
        return '<section class="betterads-native-cell betterads-native-general betterads-native-' . $safe_placement . '">'
            . '<div class="cat_bar betterads-native-header"><h3 class="catbg"><span class="betterads-sponsored-mark" aria-hidden="true">📣</span> ' . htmlspecialchars((string)($txt['betterads_native_sponsored'] ?? 'Sponsored'), ENT_QUOTES, 'UTF-8') . '</h3></div>'
            . '<div class="windowbg betterads-native-body' . $native_align_class . '" style="' . $native_container_style . '">' . $content . '</div>'
            . '</section>';
    }

    // Topic-list placements should read visually like another item in the list,
    // rather than a freestanding block dropped between rows.
    if (in_array($placement, array('topic_list_top', 'topic_list_bottom', 'topic_list_repeat'), true))
    {
        return '<div class="windowbg betterads-native-cell betterads-native-topic-row betterads-native-' . $safe_placement . '">'
            . '<div class="betterads-topic-sponsored-rail">'
            . '<span class="betterads-topic-sponsored-icon" aria-hidden="true">📣</span>'
            . '<span class="betterads-topic-sponsored-text">' . htmlspecialchars((string)($txt['betterads_native_sponsored'] ?? 'Sponsored'), ENT_QUOTES, 'UTF-8') . '</span>'
            . '</div>'
            . '<div class="betterads-topic-sponsored-content' . $native_align_class . '" style="' . $native_container_style . '">' . $content . '</div>'
            . '</div>';
    }

    // Topic-view placements borrow the same visual hierarchy as a post:
    // a themed header followed by an enclosed message-style body.
    if (in_array($placement, array('topic_view_top', 'topic_view_bottom', 'topic_view_repeat'), true))
    {
        return '<article class="betterads-native-cell betterads-native-post betterads-native-' . $safe_placement . '">'
            . '<div class="cat_bar betterads-native-header"><h3 class="catbg"><span class="betterads-sponsored-mark" aria-hidden="true">📣</span> ' . htmlspecialchars((string)($txt['betterads_native_sponsored'] ?? 'Sponsored'), ENT_QUOTES, 'UTF-8') . ' <span class="betterads-native-header-note">' . htmlspecialchars((string)($txt['betterads_native_advertisement'] ?? 'Advertisement'), ENT_QUOTES, 'UTF-8') . '</span></h3></div>'
            . '<div class="windowbg betterads-native-post-grid">'
            . '<aside class="betterads-native-post-rail"><strong>' . htmlspecialchars((string)($txt['betterads_native_advertisement'] ?? 'Advertisement'), ENT_QUOTES, 'UTF-8') . '</strong><span>' . htmlspecialchars((string)($txt['betterads_native_support_forum'] ?? 'Support the forum'), ENT_QUOTES, 'UTF-8') . '</span><span class="betterads-post-megaphone" aria-hidden="true">📣</span></aside>'
            . '<div class="betterads-native-post-content' . $native_align_class . '" style="' . $native_container_style . '">' . $content . '</div>'
            . '</div>'
            . '</article>';
    }

    // Better Gallery placements use a compact themed Sponsored module.
    if (strpos($placement, 'gallery_') === 0)
    {
        return '<section class="betterads-native-cell betterads-native-gallery betterads-native-' . $safe_placement . '">'
            . '<div class="cat_bar betterads-native-header"><h3 class="catbg"><span class="betterads-sponsored-mark" aria-hidden="true">📣</span> ' . htmlspecialchars((string)($txt['betterads_native_sponsored'] ?? 'Sponsored'), ENT_QUOTES, 'UTF-8') . '</h3></div>'
            . '<div class="windowbg betterads-native-body' . $native_align_class . '" style="' . $native_container_style . '">' . $content . '</div>'
            . '</section>';
    }

    // Anything contributed by another Better MOD or a future placement still
    // gets the general themed cell instead of falling back to a floating ad.
    return '<section class="betterads-native-cell betterads-native-general betterads-native-' . $safe_placement . '">'
        . '<div class="cat_bar betterads-native-header"><h3 class="catbg"><span class="betterads-sponsored-mark" aria-hidden="true">📣</span> ' . htmlspecialchars((string)($txt['betterads_native_sponsored'] ?? 'Sponsored'), ENT_QUOTES, 'UTF-8') . '</h3></div>'
        . '<div class="windowbg betterads-native-body' . $native_align_class . '" style="' . $native_container_style . '">' . $content . '</div>'
        . '</section>';
}

function betterads_render_unit($unit, $placement = '', $render_options = array())
{
    global $modSettings, $txt;
    if (!betterads_unit_is_eligible($unit)) return '';
    $body = '';
    if ($unit['unit_type'] === 'image')
    {
        $src = betterads_image_src($unit['image_url']);
        if ($src === '') return '';
        $size_mode = in_array((string)($unit['image_size_mode'] ?? 'responsive'), array('responsive','original','custom'), true) ? (string)$unit['image_size_mode'] : 'responsive';
        $align = in_array((string)($unit['image_align'] ?? 'center'), array('left','center','right'), true) ? (string)$unit['image_align'] : 'center';
        $width = max(0, min(5000, (int)($unit['image_width'] ?? 0)));
        $height = max(0, min(5000, (int)($unit['image_height'] ?? 0)));
        $maintain_ratio = !isset($unit['maintain_ratio']) || !empty($unit['maintain_ratio']);
        $styles = array();
        $classes = array('betterads-image');
        if ($size_mode === 'responsive')
        {
            $styles[] = 'max-width:100%';
            $styles[] = 'height:auto';
        }
        elseif ($size_mode === 'original')
        {
            $classes[] = 'betterads-image-original';
            $styles[] = 'max-width:100%';
            $styles[] = 'height:auto';
        }
        else
        {
            $classes[] = 'betterads-image-custom';
            if ($maintain_ratio)
            {
                if ($width > 0) $styles[] = 'width:' . $width . 'px';
                elseif ($height > 0) $styles[] = 'height:' . $height . 'px';
                if ($width > 0) $styles[] = 'height:auto';
                if ($width === 0 && $height > 0) $styles[] = 'width:auto';
                if ($height > 0 && $width > 0) $styles[] = 'max-height:' . $height . 'px';
            }
            else
            {
                if ($width > 0) $styles[] = 'width:' . $width . 'px';
                if ($height > 0) $styles[] = 'height:' . $height . 'px';
            }
            $styles[] = 'max-width:100%';
        }
        $style_attr = !empty($styles) ? ' style="' . htmlspecialchars(implode(';', $styles), ENT_QUOTES, 'UTF-8') . '"' : '';
        $alt_text = trim((string) $unit['alt_text']);
        $alt_attr = htmlspecialchars($alt_text, ENT_QUOTES, 'UTF-8');
        $title_attr = $alt_text !== '' ? ' title="' . $alt_attr . '"' : '';
        $img = '<img class="' . implode(' ', $classes) . '" src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" alt="' . $alt_attr . '"' . $title_attr . ' loading="lazy"' . $style_attr . '>';

        // dev21b: make image alignment deterministic at render time.
        // Theme CSS can make anchors/block elements span the full content width,
        // which made Center/Right appear stuck on the left inside native cells.
        // Keep the clickable wrapper shrink-to-fit, then move that wrapper itself.
        $edge_margin = $align === 'right'
            ? 'margin-left:auto;margin-right:0;'
            : ($align === 'center'
                ? 'margin-left:auto;margin-right:auto;'
                : 'margin-left:0;margin-right:auto;');

        if (!empty($unit['target_url']))
        {
            global $scripturl;
            $click_url = $scripturl . '?action=betterads;sa=click;id=' . (int)$unit['id_unit'];
            $img = '<a class="betterads-image-link betterads-image-link-' . $align . '" href="' . htmlspecialchars($click_url, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="sponsored noopener"' . $title_attr . ' style="display:block;width:max-content;max-width:100%;' . $edge_margin . '">' . $img . '</a>';
        }
        else
        {
            // Images without a target URL need the same shrink-to-fit alignment.
            $img = '<span class="betterads-image-link betterads-image-link-' . $align . '" style="display:block;width:max-content;max-width:100%;' . $edge_margin . '">' . $img . '</span>';
        }

        $body = '<div class="betterads-image-wrap betterads-align-' . $align . '" style="display:block;width:100%;max-width:100%;">' . $img . '</div>';
    }
    else
        $body = (string)$unit['ad_code'];
    if ($body === '') return '';
    $label = '';
    $label_position = in_array(($unit['label_position'] ?? 'above'), array('above','below'), true) ? (string)$unit['label_position'] : 'above';
    $label_align = in_array(($unit['label_align'] ?? 'match'), array('match','left','center','right'), true) ? (string)$unit['label_align'] : 'match';

    if ($label_align === 'match')
    {
        if (($unit['unit_type'] ?? '') === 'image' && in_array(($unit['image_align'] ?? 'center'), array('left','center','right'), true))
            $label_align = (string)$unit['image_align'];
        else
            $label_align = 'center';
    }

    $gallery_card_mode = $placement === 'gallery_album_repeat'
        && !empty($render_options['gallery_presentation'])
        && $render_options['gallery_presentation'] === 'card';

    if (!$gallery_card_mode && (!isset($modSettings['betterads_show_label']) || !empty($modSettings['betterads_show_label'])))
    {
        $label_text = trim((string) ($modSettings['betterads_label_text'] ?? 'ADVERTISEMENT'));
        if ($label_text === '') $label_text = 'ADVERTISEMENT';
        $label = '<div class="betterads-label betterads-label-' . $label_align . '">' . htmlspecialchars($label_text, ENT_QUOTES, 'UTF-8') . '</div>';
    }
    $class = $placement !== '' ? ' betterads-slot-' . preg_replace('~[^a-z0-9_-]+~i', '-', $placement) : '';

    // dev21d: position the ENTIRE rendered ad slot directly.
    // This is intentionally inline so theme CSS cannot leave the creative
    // visually pinned to the left. The label and creative move together.
    $slot_style = '';
    if (($unit['unit_type'] ?? '') === 'image')
    {
        $slot_align = in_array(($unit['image_align'] ?? 'center'), array('left','center','right'), true)
            ? (string)$unit['image_align']
            : 'center';

        // The parent native cell owns horizontal alignment. The slot itself is
        // an inline-block so text-align on that parent can physically move it.
        $slot_style = 'display:inline-block !important;width:auto !important;max-width:100% !important;margin:0 !important;vertical-align:top !important;text-align:initial;';
    }

    $tracker = '';
    if ($placement !== 'admin_preview')
    {
        global $scripturl;
        $track_url = $scripturl . '?action=betterads;sa=impression;id=' . (int)$unit['id_unit'];
        $tracker = '<script>(function(){var u=' . json_encode($track_url) . ';try{if(navigator.sendBeacon){navigator.sendBeacon(u);return;}}catch(e){}try{fetch(u,{method:"GET",credentials:"same-origin",cache:"no-store",keepalive:true});}catch(e){}})();</script>';
    }
    $content = $label_position === 'below' ? $body . $label : $label . $body;
    $slot = '<div class="betterads-slot' . $class
        . '" data-betterads-unit="' . (int)$unit['id_unit']
        . '" data-betterads-align="' . htmlspecialchars((string)($unit['image_align'] ?? ''), ENT_QUOTES, 'UTF-8')
        . '" data-betterads-placement="' . htmlspecialchars((string)$placement, ENT_QUOTES, 'UTF-8')
        . '"' . ($slot_style !== '' ? ' style="' . $slot_style . '"' : '')
        . '>' . $content . $tracker . '</div>';

    if ($gallery_card_mode)
    {
        return '<article class="betterads-gallery-sponsored-card windowbg" data-bg-slot-presentation="card">'
            . '<div class="betterads-gallery-sponsored-creative">' . $slot . '</div>'
            . '<div class="betterads-gallery-sponsored-card-body">'
            . '<strong class="betterads-gallery-sponsored-title">' . htmlspecialchars((string)($txt['betterads_gallery_sponsored_card_title'] ?? 'Sponsored'), ENT_QUOTES, 'UTF-8') . '</strong>'
            . '<div class="smalltext betterads-gallery-sponsored-note">' . htmlspecialchars((string)($txt['betterads_gallery_sponsored_card_note'] ?? 'Advertisement'), ENT_QUOTES, 'UTF-8') . '</div>'
            . '</div>'
            . '</article>';
    }

    return betterads_wrap_native_cell($slot, $placement, $unit);
}

function betterads_get_eligible_placement_units($placement, $with_repeat = false)
{
    global $smcFunc, $modSettings;
    if (empty($modSettings['betterads_enabled'])) return array();

    $rows = array();
    $select_repeat = $with_repeat ? ', p.repeat_every, p.repeat_start, p.repeat_max' : '';
    $request = $smcFunc['db_query']('', 'SELECT u.*' . $select_repeat . ' FROM {db_prefix}betterads_placements AS p INNER JOIN {db_prefix}betterads_units AS u ON (u.id_unit=p.id_unit) WHERE p.placement_key={string:key} AND p.enabled=1 AND u.enabled=1 AND u.archived=0 ORDER BY p.sort_order, p.id_placement', array('key'=>(string)$placement));
    while ($row = $smcFunc['db_fetch_assoc']($request))
    {
        if (!betterads_unit_is_eligible($row)) continue;
        if ($with_repeat)
        {
            $row['repeat_every'] = max(1, min(100, (int)$row['repeat_every']));
            $row['repeat_start'] = max(1, min(100, (int)$row['repeat_start']));
            $row['repeat_max'] = max(1, min(20, (int)$row['repeat_max']));
        }
        $rows[] = $row;
    }
    $smcFunc['db_free_result']($request);
    return $rows;
}

function betterads_render_placement($placement)
{
    // dev7: a placement is now a creative pool. Static placements show one
    // eligible creative per request instead of stacking every matching ad.
    static $selection_cache = array();

    $rows = betterads_get_eligible_placement_units($placement, false);
    if (empty($rows)) return '';

    if (!isset($selection_cache[$placement]))
    {
        if (count($rows) === 1)
            $selection_cache[$placement] = 0;
        else
            $selection_cache[$placement] = mt_rand(0, count($rows) - 1);
    }

    $index = min((int)$selection_cache[$placement], count($rows) - 1);
    return betterads_render_unit($rows[$index], $placement);
}

function betterads_get_repeat_placements($placement)
{
    return betterads_get_eligible_placement_units($placement, true);
}

function betterads_render_repeat_payload($placement)
{
    $rows = betterads_get_repeat_placements($placement);
    if (empty($rows)) return '';

    $mode = $placement === 'topic_view_repeat' ? 'topic-view' : 'topic-list';

    // Repeat timing is placement-level in dev7. All creatives in the pool are
    // synchronized when saved/upgraded, so one schedule drives the cycle.
    $schedule = $rows[0];
    $templates = '';
    foreach ($rows as $row)
    {
        $body = betterads_render_unit($row, $placement);
        if ($body === '') continue;
        $templates .= '<template class="betterads-repeat-template" data-unit="' . (int)$row['id_unit'] . '">' . $body . '</template>';
    }
    if ($templates === '') return '';

    return '<div class="betterads-repeat-payload" hidden data-mode="' . $mode . '" data-start="' . (int)$schedule['repeat_start'] . '" data-every="' . (int)$schedule['repeat_every'] . '" data-max="' . (int)$schedule['repeat_max'] . '">' . $templates . '</div>';
}

function betterads_repeat_script()
{
    return <<<'HTML'
<script>
(function(){
    function runScripts(root){
        root.querySelectorAll('script').forEach(function(oldScript){
            var script = document.createElement('script');
            Array.prototype.forEach.call(oldScript.attributes, function(attr){ script.setAttribute(attr.name, attr.value); });
            script.text = oldScript.text || oldScript.textContent || '';
            oldScript.parentNode.replaceChild(script, oldScript);
        });
    }
    function contentRows(mode){
        if (mode === 'topic-list') {
            var topicContainer = document.getElementById('topic_container');
            if (!topicContainer) return [];

            // Count only real SMF topic rows. This deliberately ignores any
            // structural/helper elements a theme or Better Portal may place
            // inside #topic_container.
            return Array.prototype.filter.call(topicContainer.children, function(el){
                if (el.nodeType !== 1 || el.classList.contains('betterads-repeat-insert'))
                    return false;

                return !!(
                    el.querySelector('.message_index_title') &&
                    el.querySelector('.board_stats') &&
                    el.querySelector('.lastpost')
                );
            });
        }

        if (mode === 'topic-view') {
            var posts = document.getElementById('forumposts');
            if (!posts) return [];

            // SMF's actual post wrappers are msg123, msg456, etc. Search them
            // directly, then keep only top-level post wrappers so nested ids
            // can never disturb the repeat count.
            return Array.prototype.filter.call(posts.querySelectorAll('[id^="msg"]'), function(el){
                if (!/^msg\d+$/.test(el.id || '') || el.classList.contains('betterads-repeat-insert'))
                    return false;

                var parentMsg = el.parentElement ? el.parentElement.closest('[id^="msg"]') : null;
                return !parentMsg || !/^msg\d+$/.test(parentMsg.id || '');
            });
        }

        return [];
    }
    function placePayload(payload){
        var mode = payload.getAttribute('data-mode');
        if (!mode) return;
        var rows = contentRows(mode);
        if (!rows.length) return;

        var templates = Array.prototype.slice.call(payload.querySelectorAll('template.betterads-repeat-template'));
        if (!templates.length) return;

        var start = Math.max(1, parseInt(payload.getAttribute('data-start') || '3', 10));
        var every = Math.max(1, parseInt(payload.getAttribute('data-every') || '5', 10));
        var max = Math.max(1, parseInt(payload.getAttribute('data-max') || '2', 10));
        var placed = 0;

        for (var i = start; i <= rows.length && placed < max; i += every){
            var anchor = rows[i - 1];
            if (!anchor || !anchor.parentNode)
                continue;

            // dev7: cycle A -> B -> C -> A through every eligible creative
            // assigned to this placement instead of repeating one ad forever.
            var tpl = templates[placed % templates.length];
            var host = document.createElement('div');
            host.className = 'betterads-repeat-insert betterads-repeat-' + mode;
            host.setAttribute('data-betterads-after-item', String(i));
            host.setAttribute('data-betterads-rotation-index', String(placed % templates.length));
            host.innerHTML = tpl.innerHTML;

            anchor.parentNode.insertBefore(host, anchor.nextSibling);
            runScripts(host);
            placed++;
        }
    }
    function init(){ document.querySelectorAll('.betterads-repeat-payload').forEach(placePayload); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
</script>
HTML;
}

function betterads_context_placement($edge)
{
    $action = isset($_REQUEST['action']) ? (string)$_REQUEST['action'] : '';
    if ($action !== '') return '';
    if (!empty($_REQUEST['topic'])) return 'topic_view_' . $edge;
    if (!empty($_REQUEST['board'])) return 'topic_list_' . $edge;
    return 'board_index_' . $edge;
}

function betterads_gallery_repeat_mode($id_unit)
{
    global $modSettings;

    $id_unit = (int)$id_unit;
    if ($id_unit <= 0)
        return 'row';

    $key = 'betterads_gallery_repeat_mode_' . $id_unit;
    return (!empty($modSettings[$key]) && $modSettings[$key] === 'card') ? 'card' : 'row';
}

function betterads_save_gallery_repeat_mode($id_unit, $placement, $mode)
{
    global $modSettings, $smcFunc;

    $id_unit = (int)$id_unit;
    if ($id_unit <= 0)
        return;

    $key = 'betterads_gallery_repeat_mode_' . $id_unit;

    if ($placement !== 'gallery_album_repeat')
    {
        if (isset($modSettings[$key]))
        {
            $smcFunc['db_query']('', '
                DELETE FROM {db_prefix}settings
                WHERE variable = {string:variable}',
                array('variable' => $key)
            );
            unset($modSettings[$key]);
        }
        return;
    }

    $mode = $mode === 'card' ? 'card' : 'row';
    updateSettings(array($key => $mode));
}

function betterads_gallery_slot($slot, &$html, $meta = array())
{
    $slot = (string)$slot;
    $html = '';

    if (!in_array($slot, array(
        'gallery_index_top',
        'gallery_index_bottom',
        'gallery_album_top',
        'gallery_album_bottom',
        'gallery_album_repeat',
    ), true))
        return;

    if ($slot !== 'gallery_album_repeat')
    {
        $html = betterads_render_placement($slot);
        return;
    }

    $item_index = max(0, (int)($meta['item_index'] ?? 0));
    if ($item_index <= 0)
        return;

    $rows = betterads_get_repeat_placements($slot);
    if (empty($rows))
        return;

    $schedule = $rows[0];
    $start = max(1, (int)$schedule['repeat_start']);
    $every = max(1, (int)$schedule['repeat_every']);
    $max = max(1, (int)$schedule['repeat_max']);

    if ($item_index < $start || (($item_index - $start) % $every) !== 0)
        return;

    $occurrence = (int)(($item_index - $start) / $every);
    if ($occurrence >= $max)
        return;

    $row = $rows[$occurrence % count($rows)];
    $gallery_presentation = betterads_gallery_repeat_mode((int)$row['id_unit']);
    $html = betterads_render_unit($row, $slot, array(
        'gallery_presentation' => $gallery_presentation,
    ));
}

function betterads_portal_blocks(&$blocks)
{
    global $txt;
    betterads_load_language();
    $blocks['better_ads'] = array(
        'label' => $txt['betterads_portal_block'],
        'description' => $txt['betterads_portal_block_desc'],
        'category' => 'better_smf',
        'provider' => 'Better Ads',
        'render' => 'betterads_portal_render',
        'cacheable' => false,
        'default_ttl' => 0,
        'default_title' => $txt['betterads_portal_block'],
        'default_settings' => array('unit_id'=>0),
        'settings_fields' => array('unit_id'=>array('type'=>'select','label'=>$txt['betterads_portal_choose'],'options_callback'=>'betterads_portal_unit_options')),
    );
}

function betterads_portal_unit_options()
{
    global $smcFunc, $txt;
    betterads_load_language();
    $out = array(0 => $txt['betterads_portal_choose_none']);
    $request = $smcFunc['db_query']('', 'SELECT id_unit, name, unit_type, ad_code, image_url, audience, enabled FROM {db_prefix}betterads_units WHERE enabled=1 AND archived=0 ORDER BY name');
    while ($row = $smcFunc['db_fetch_assoc']($request))
        if (betterads_unit_has_content($row)) $out[(int)$row['id_unit']] = (string)$row['name'];
    $smcFunc['db_free_result']($request);
    return $out;
}

function betterads_portal_render($block)
{
    $settings = json_decode((string)($block['settings'] ?? ''), true);
    if (!is_array($settings)) $settings = array();
    $id = (int)($settings['unit_id'] ?? 0);
    if (!$id) return array('body'=>'');
    $unit = betterads_get_unit($id);
    return array('body'=>betterads_render_unit($unit, 'portal_block'));
}
