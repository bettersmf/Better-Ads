<?php
if (!defined('SMF')) die('No direct access.');

function betterads_footer_pending_request_count()
{
    global $smcFunc, $modSettings;
    static $pending = null;
    if ($pending !== null) return $pending;
    $pending = 0;
    if (empty($modSettings['betterads_version'])) return $pending;

    $request = $smcFunc['db_query']('', '
        SELECT COUNT(*)
        FROM {db_prefix}betterads_requests
        WHERE status = {string:pending}',
        array('pending' => 'pending')
    );
    list ($pending) = $smcFunc['db_fetch_row']($request);
    $smcFunc['db_free_result']($request);
    return $pending = (int)$pending;
}

function betterads_buffer_unified_admin_badge($buffer)
{
    global $scripturl;

    if (!function_exists('allowedTo') || !allowedTo('admin_forum'))
        return $buffer;

    $pending = betterads_footer_pending_request_count();
    if ($pending <= 0)
        return $buffer;

    $safe_scripturl = preg_quote($scripturl, '~');
    $admin_link_pattern = '~(<a\b[^>]*href=(["\'])'
        . $safe_scripturl
        . '\?action=admin(?:;[^"\']*)?\2[^>]*>)(.*?)(</a>)~is';

    $buffer = preg_replace_callback(
        $admin_link_pattern,
        function ($matches) use ($pending) {
            $inside = $matches[3];
            $amt_pattern = '~(<span\b[^>]*class=(["\'])[^"\']*\bamt\b[^"\']*\2[^>]*>)(\d+)(</span>)~i';

            if (preg_match($amt_pattern, $inside))
            {
                $inside = preg_replace_callback(
                    $amt_pattern,
                    function ($amt) use ($pending) {
                        return $amt[1] . ((int)$amt[3] + $pending) . $amt[4];
                    },
                    $inside,
                    1
                );
            }
            else
            {
                $added = 0;
                $inside = preg_replace(
                    '~(</span>\s*)$~i',
                    ' <span class="amt">' . $pending . '</span>$1',
                    $inside,
                    1,
                    $added
                );
                if ($added === 0)
                    $inside .= ' <span class="amt">' . $pending . '</span>';
            }

            return $matches[1] . $inside . $matches[4];
        },
        $buffer,
        1
    );

    if (strpos($buffer, 'id="betterads-unified-admin-badge-css"') === false)
    {
        $css = '<style id="betterads-unified-admin-badge-css">'
            . '.button_admin>a.active .amt,.button_admin .active .amt,#button_admin>a.active .amt,#button_admin .active .amt{'
            . 'background:#163f68!important;color:#fff!important;'
            . 'box-shadow:inset 0 0 0 1px rgba(255,255,255,.22),0 0 0 1px rgba(0,0,0,.18)!important;'
            . '}</style>';

        $head_pos = stripos($buffer, '</head>');
        $buffer = $head_pos !== false
            ? substr($buffer,0,$head_pos).$css.substr($buffer,$head_pos)
            : $css.$buffer;
    }

    return $buffer;
}

function betterads_footer_link_buffer($buffer)
{
    global $modSettings, $scripturl;

    if (empty($modSettings['betterads_enabled']))
        return $buffer;

    $buffer = betterads_buffer_unified_admin_badge($buffer);

    if (empty($modSettings['betterads_advertise_enabled']))
        return $buffer;

    $footer_pos = stripos($buffer, 'id="footer"');
    if ($footer_pos === false) $footer_pos = stripos($buffer, "id='footer'");
    if ($footer_pos === false) return $buffer;

    $label = trim((string)($modSettings['betterads_advertise_link_text'] ?? 'Advertise With Us'));
    if ($label === '') $label = 'Advertise With Us';

    $href = $scripturl . '?action=betterads;sa=advertise';
    $safe_href = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');
    $safe_label = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');

    if (strpos(substr($buffer, $footer_pos), 'action=betterads;sa=advertise') !== false)
        return $buffer;

    $before = substr($buffer, 0, $footer_pos);
    $footer = substr($buffer, $footer_pos);
    $link = '<a href="' . $safe_href . '">' . $safe_label . '</a> | ';

    $count = 0;
    $footer = preg_replace(
        '~<a(\s+[^>]*href=(["\'])#(?:top_section|top)\2[^>]*)>~i',
        $link . '<a$1>', $footer, 1, $count
    );

    if ($count === 0)
    {
        $footer = preg_replace(
            '~(<li\b[^>]*class=(["\'])[^"\']*floatright[^"\']*\2[^>]*>)(.*?)(</li>)~is',
            '$1$3 | <a href="' . $safe_href . '">' . $safe_label . '</a>$4',
            $footer, 1
        );
    }

    return $before . $footer;
}
