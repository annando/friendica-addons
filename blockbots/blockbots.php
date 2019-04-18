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
		'AppEngine-Google'];
// collection@infegy.com
	foreach ($agents as $agent) {
		if (stristr($_SERVER['HTTP_USER_AGENT'], $agent)) {
			Logger::info('blocking user-agent', $request);
			System::httpExit(403);
		}
	}

	$agents = ['diaspora-connection-tester', 'DiasporaFederation', 'Friendica', '(compatible; zot)',
		'Micro.blog', 'Mastodon', 'hackney', 'GangGo', 'python/federation', 'GNU social', 'winHttp'];

	foreach ($agents as $agent) {
		if (stristr($_SERVER['HTTP_USER_AGENT'], $agent)) {
			return;
		}
	}
/*
	$agents = ['Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.2; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)',
		'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1)',
		'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)',
		'Mozilla/5.0 (iPad; CPU OS 12_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1 Mobile/15E148 Safari/604.1',
		'Mozilla/5.0 (iPhone; CPU iPhone OS 12_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/16.0b14732 Mobile/15E148 Safari/605.1.15',
		'Mozilla/5.0 (iPhone; CPU iPhone OS 12_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1 Mobile/15E148 Safari/604.1',
		'Mozilla/5.0 (Linux; Android 4.2.1; en-us; Nexus 5 Build/JOP40D) AppleWebKit/535.19 (KHTML, like Gecko; googleweblight) Chrome/38.0.1025.166 Mobile Safari/535.19',
		'Mozilla/5.0 (Linux; U; Android 4.4.4; Nexus 5 Build/KTU84P) AppleWebkit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
		'Mozilla/5.0 (Linux; Android 6.0; LG-K420) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.90 Mobile Safari/537.36',
		'Mozilla/5.0 (Linux; Android 8.0.0; SM-A320FL Build/R16NW; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/73.0.3683.90 Mobile Safari/537.36',
		'Mozilla/5.0 (Linux; Android 8.1.0; Nokia 2.1 Build/OPM1.171019.019) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.105 Mobile Safari/537.36',
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:66.0) Gecko/20100101 Firefox/66.0',
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.98 Safari/537.36',
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36 OPR/58.0.3135.127',
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.14; rv:66.0) Gecko/20100101 Firefox/66.0',
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.67 Safari/537.36',
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.62 Safari/537.36',
		'Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; en-US; rv:1.8.1.7pre) Gecko/20070815 Firefox/2.0.0.6 Navigator/9.0b3',
		'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0',
		'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.119 Safari/537.36',
		'Mozilla/5.0 (Windows NT 6.1; rv:60.0) Gecko/20100101 Firefox/60.0',
		'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:66.0) Gecko/20100101 Firefox/66.0',
		'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36',
		'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36',
		'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36',
		'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36',
		'Mozilla/5.0 (Windows NT 6.1; Win64; x64; Trident/7.0; rv:11.0) like Gecko',
		'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1',
		'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko',
		'Mozilla/6.0 (Windows NT 6.2; WOW64; rv:16.0.1) Gecko/20121011 Firefox/16.0.1',
		'Mozilla/5.0 (Windows NT 6.2; WOW64; Trident/7.0; rv:11.0) like Gecko',
		'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36',
		'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36',
		'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36',
		'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:66.0) Gecko/20100101 Firefox/66.0',
		'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36',
		'Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3',
		'Mozilla/5.0 (X11; CrOS x86_64 11647.104.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.88 Safari/537.36',
		'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31',
		'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.75 Safari/537.36 Google Favicon',
		'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.53 Safari/537.36',
		'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
		'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36',
		'Mozilla/5.0 (X11; Linux x86_64; rv:60.0) Gecko/20100101 Firefox/60.0',
		'Mozilla/5.0 (X11; Linux x86_64; rv:60.9) Gecko/20100101 Goanna/4.1 Firefox/60.9 PaleMoon/28.2.2',
		'Mozilla/5.0 (X11; Linux x86_64; rv:66.0) Gecko/20100101 Firefox/66.0',
		'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9a3pre) Gecko/20070330',
		'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:15.0) Gecko/20100101 Firefox/15.0.1',
		'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:66.0) Gecko/20100101 Firefox/66.0',
		'Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; de) Presto/2.9.168 Version/11.52',
		'Mr.4x3 Powered',
		'Test Certificate Info'
];
	foreach ($agents as $agent) {
		if ($_SERVER['HTTP_USER_AGENT'] == $agent) {
			return;
		}
	}
*/
	$string = $_SERVER['HTTP_USER_AGENT'];

	$patterns = ['Chrome/\d*\.\d*\.\d*\.\d*',
		'Firefox/\d*\.\d*\.\d*\.\d*', 'Firefox/\d*\.\d*\.\d*', 'Firefox/\d*\.\d*',
		'rv:\d*\.\d*\.\d*\.\d*', 'rv:\d*\.\d*\.\d*', 'rv:\d*\.\d*',
		'AppleWebKit/\d*\.\d*\.\d*', 'AppleWebKit/\d*\.\d*',
		'Safari/\d*\.\d*\.\d*', 'Safari/\d*\.\d*',
		'Gecko/\d*\.\d*', 'Gecko/\d*',
		'Chromium/\d*\.\d*\.\d*\.\d*', 'Trident/\d*\.\d*', 'Edge/\d*\.\d*',
		'Opera/\d*\.\d*', 'Ceatles/\d*\.\d*',
		'UCBrowser/\d*\.\d*\.\d*\.\d*', 'Navigator/\d*\.\d*\.\d*\.\d*', 'Mozilla/\d*\.\d*',
		'Windows NT \d*\.\d*',
		'Intel Mac OS X \d*\.\d*', 'Intel Mac OS X \d*_\d*_\d*',
		'Android \d*\.\d*\.d*', 'Android \d*\.\d*', 'Android \d*',
		'Presto/\d*\.\d*\.\d*', 'MSIE \d*\.\d*', 'Version/\d*\.\d*\.\d*',
		'Version/\d*\.\d*', '.NET CLR \d*\.\d*\.\d*', 'SLCC2', 'Media Center PC \d*\.\d*',
		'Nexus \d*'
	];

	do {
		$oldagent = $string;
		foreach ($patterns as $pattern) {
			$string = preg_replace('=(.*?)' . $pattern . '(.*?)=i', '$1$2', $string);
		}
	} while ($string != $oldagent);

	$search = ['KHTML', 'like Gecko', 'WOW64', 'Ubuntu', 'Linux', 'x86_64', 'X11', 'compatible',
		'Macintosh', 'x64', 'Win64', 'Mobile', 'i686', 'en-US', 'zh-CN', 'CrOS',
		'F5121', 'Build/34.0.A.1.247', 'CLT-L04', ' fr ', ' U ', 'LG-K420', 'Build/KTU84P'];
	do {
		$oldtext = $string;
		$string = ' ' . trim(str_replace($search, ' ', $string), ' ();:.,') . ' ';
	} while ($oldtext != $string);

	if (empty(trim($string))) {
		return;
	}

	$request = array_merge(['rest' => $string], $request);
	Logger::info('Possible bot', $request);
}
