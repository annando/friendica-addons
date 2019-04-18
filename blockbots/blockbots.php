<?php
/**
 * Name: blockbots
 * Description: Block all bots - even the ones who don't respect robots.txt
 * Version: 0.1
 * Author: Michael Vogel <https://pirati.ca/profile/heluecht>
 *
 * There are bots that ignore robots.txt, see for example:
 * https://www.archiveteam.org/index.php?title=Robots.txt
 *
 * Additionally the list contains an exhausting list of other known bots.
 *
 * When a bot is detected, the system quits with error "403 Forbidden"
 */

use Friendica\Core\Hook;
use Friendica\Core\System;
use Friendica\Core\Logger;

function blockbots_install()
{
	Hook::register('head', 'addon/blockbots/blockbots.php', 'blockbots_check');
}

function blockbots_uninstall()
{
	Hook::unregister('head', 'addon/blockbots/blockbots.php', 'blockbots_check');
}

function blockbots_check($a, $b)
{
	if (empty($_SERVER['HTTP_USER_AGENT'])) {
		exit;
	}

	$request = ['agent' => $_SERVER['HTTP_USER_AGENT'], 'uri' => $_SERVER['REQUEST_URI']];

	$agents = ['ArchiveTeam ArchiveBot', 'SEMrushBot', '360Spider', 'Twitterbot', 'ltx71', 'AhrefsBot', 'YoudaoBot',
		'Baiduspider', 'MSNBot', 'Googlebot', 'Sosospider', 'JikeSpider', 'BLEXBot', 'picmole', 'LexxeBot',
		'NextGenSearchBot', 'spbot', 'SiteBot', 'MJ12bot', 'CrystalSemanticsBot', 'NetSeer crawler',
		'trovitBot', 'DotBot', 'Ezooms', 'discobot', 'Jyxobot', 'sogou', 'sistrix', 'heritrix', 'GarlikCrawler',
		'NerdByNature.Bot', 'DTS Agent', 'psbot', 'WBSearchBot', 'AddThis.com', 'ia_archiver', 'proximic',
		'discoverybot', 'bl.uk_lddc_bot', 'IstellaBot', 'seokicks', 'UnisterBot', 'Bender', 'wotbox',
		'Yasni', 'netEstate NE Crawler', 'Exabot', 'Pixray-Seeker', 'Linguee', 'integromedb', 'SearchmetricsBot',
		'BDCbot', 'GrapeshotCrawler', 'WeSEE:Search', 'TurnitinBot', 'admantx', 'BUbiNG', 'YisouSpider',
		'facebookexternalhit', 'ldspider', 'Researchscan', 'CCBot', 'Qwantify/Bleriot', 'PaperLiBot', 'bingbot',
		'AppEngine-Google', 'Datanyze'];
// collection@infegy.com
	foreach ($agents as $agent) {
		if (stristr($_SERVER['HTTP_USER_AGENT'], $agent)) {
			Logger::info('blocking user-agent', $request);
			System::httpExit(403);
		}
	}

	$agents = ['diaspora-connection-tester', 'DiasporaFederation', 'Friendica', '(compatible; zot)',
		'Micro.blog', 'Mastodon', 'hackney', 'GangGo', 'python/federation', 'GNU social', 'winHttp',
		'Mr.4x3 Powered', 'Test Certificate Info', 'WordPress.com'];

	foreach ($agents as $agent) {
		if (stristr($_SERVER['HTTP_USER_AGENT'], $agent)) {
			return;
		}
	}

	$agent = blockbots_remove_known_parts($_SERVER['HTTP_USER_AGENT']);

	if (empty($agent)) {
		return;
	}

	$request = array_merge(['rest' => $agent], $request);
	Logger::info('Possible bot', $request);
}

function blockbots_remove_known_parts($agent)
{
	$patterns = ['Chrome/\d*\.\d*\.\d*\.\d*',
		'Firefox/\d*\.\d*\.\d*\.\d*', 'Firefox/\d*\.\d*\.\d*', 'Firefox/\d*\.\d*',
		'rv:\d*\.\d*\.\d*\.\d*', 'rv:\d*\.\d*\.\d*', 'rv:\d*\.\d*',
		'AppleWebKit/\d*\.\d*\.\d*', 'AppleWebKit/\d*\.\d*',
		'Safari/\d*\.\d*\.\d*', 'Safari/\d*\.\d*',
		'Gecko/\d*\.\d*', 'Gecko/\d*',
		'Chromium/\d*\.\d*\.\d*\.\d*', 'Trident/\d*\.\d*', 'Edge/\d*\.\d*',
		'Opera/\d*\.\d*', 'Ceatles/\d*\.\d*',
		'UCBrowser/\d*\.\d*\.\d*\.\d*', 'Navigator/\d*\.\d*\.\d*\.\d*', 'Mozilla/\d*\.\d*',
		'Goanna/\d*\.\d*', 'PaleMoon/\d*\.\d*\.\d*',
		'Windows NT \d*\.\d*',
		'Intel Mac OS X \d*_\d*_\d*', 'Intel Mac OS X \d*\.\d*\.\d*', 'Intel Mac OS X \d*\.\d*',
		'Android \d*\.\d*\.\d*', 'Android \d*\.\d*', 'Android \d*',
		'Presto/\d*\.\d*\.\d*', 'MSIE \d*\.\d*', 'Version/\d*\.\d*\.\d*',
		'Version/\d*\.\d*', '.NET CLR \d*\.\d*\.\d*', 'SLCC2', 'Media Center PC \d*\.\d*',
		'Nexus \d*'
	];

	do {
		$oldagent = $agent;
		foreach ($patterns as $pattern) {
			$agent = preg_replace('=(.*?)' . $pattern . '(.*?)=i', '$1$2', $agent);
		}
	} while ($agent != $oldagent);

	$search = ['KHTML', 'like Gecko', 'WOW64', 'Ubuntu', 'Linux', 'x86_64', 'X11', 'compatible',
		'Macintosh', 'x64', 'Win64', 'Mobile', 'i686', 'en-US', 'zh-CN', 'CrOS', ' de ',
		'F5121', 'Build/34.0.A.1.247', 'CLT-L04', ' fr ', ' U ', 'LG-K420', 'Build/KTU84P',
		'like Mac OS X', '/15E148', 'SM-A320FL', 'a3pre', 'Google Favicon', 'Windows',
		'iPhone', 'iPad', 'CPU', 'OS 12_2', 'FxiOS/16.0b14732', 'googleweblight',
		'Build/JOP40D', ' en-us ', 'Nokia 2.1', 'Build/OPM1.171019.019', 'Build/R16NW',
		' wv ', 'OPR/58.0.3135.127', 'PPC Mac OS X Mach-O', ' pre ', 'Navigator/9.0b3', '11647.104.0'];
	do {
		$oldtext = $agent;
		$agent = ' ' . trim(str_replace($search, ' ', $agent), ' ();:.,') . ' ';
	} while ($oldtext != $agent);

	return trim($agent);
}
