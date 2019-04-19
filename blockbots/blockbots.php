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
		'AppEngine-Google', 'Datanyze', 'evc-batch', 'HTTP Banner Detection', 'DuckDuckGo'];
// collection@infegy.com
	foreach ($agents as $agent) {
		if (stristr($_SERVER['HTTP_USER_AGENT'], $agent)) {
			Logger::info('blocking user-agent', $request);
			System::httpExit(403);
		}
	}

	$agents = ['diaspora-connection-tester', 'DiasporaFederation', 'Friendica', '(compatible; zot)',
		'Micro.blog', 'Mastodon', 'hackney', 'GangGo', 'python/federation', 'GNU social', 'winHttp',
		'Go-http-client', 'Mr.4x3 Powered', 'Test Certificate Info', 'WordPress.com', 'zgrab',
		'curl/', 'StatusNet', 'OpenGraphReader/', 'Uptimebot/'];

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
	$patterns = [
		'\(Linux; Android [\d\.]*; [^\)]*\)',
		'\(Linux; U; Android [\d\.]*; [^\)]*\)',
		'\(iPhone; CPU [^\)]* like Mac OS X\)',
		'\(iPad; CPU [^\)]* like Mac OS X\)',
		'\(X11; Linux [\d_a-z]*\)',
		'\(X11; Linux [\d_a-z]*; rv:[\d\.a-z]*\)',
		'\(X11; [a-z]*; Linux [\d_a-z]*; rv:[\d\.a-z]*\)',
		'Chrome/[\d\.]*', 'Vivaldi/[\d\.]*',
		'Firefox/[\d\.]*', 'rv:[\d\.a-z]*', 'AppleWebKit/[\d\.]*',
		'Safari/[\d\.]*', 'Gecko/[\d\.]*', 'Quark/[\d\.]*',
		'Chromium/[\d\.]*', 'Trident/[\d\.]*', 'Edge/[\d\.]*', 'Edg/[\d\.]*',
		'Opera/[\d\.]*', 'Ceatles/[\d\.]*',
		'UCBrowser/[\d\.]*', 'Navigator/[\d\.a-z]*', 'Mozilla/[\d\.]*',
		'Goanna/[\d\.]*', 'PaleMoon/[\d\.]*',
		'Windows NT [\d\.]*',
		'Intel Mac OS X \d*_\d*_\d*', 'Intel Mac OS X [\d\.]*',
		'Presto/[\d\.]*', 'MSIE [\d\.]*', 'Version/[\d\.]*',
		'Version/[\d\.]*', '.NET CLR [\d\.]*', 'SLCC2', 'Media Center PC \d*\.\d*',
		'Netscape/\d*\.\d*\.\d*', 'CrOS x86_64 [\d\.]*',
		'Mobile/[\d\.a-z]*', 'Build/[\d\.a-z]*',
		'FxiOS/[\d\.a-z]*', 'OPR/[\d\.]*', 'UBrowser/[\d\.]*'
	];

	do {
		$oldagent = $agent;
		foreach ($patterns as $pattern) {
			$agent = preg_replace('=(.*?)' . $pattern . '(.*?)=i', '$1$2', $agent);
		}
	} while ($agent != $oldagent);

	$search = ['KHTML', 'like Gecko', 'WOW64', 'x86_64', 'X11', 'Linux', 'compatible',
		'Macintosh', 'x64', 'Win64', 'Mobile', 'i686', 'en-US', 'zh-CN', ' de ',
		' fr ', ' U ', 'Google Favicon', 'Windows',
		'googleweblight',' en-us ',
		'Win 9x 4.90', ' SG ', 'Intel Mac OS X x.y',
		' wv ', 'PPC Mac OS X Mach-O', ' pre '];
	do {
		$oldtext = $agent;
		$agent = ' ' . trim(str_replace($search, ' ', $agent), ' ();:.,/') . ' ';
	} while ($oldtext != $agent);

	return trim($agent);
}
