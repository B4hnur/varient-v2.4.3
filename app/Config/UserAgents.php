<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * -------------------------------------------------------------------
 * User Agents
 * -------------------------------------------------------------------
 *
 * This file contains four arrays of user agent data. It is used by the
 * User Agent Class to help identify browser, platform, robot, and
 * mobile device data. The array keys are used to identify the device
 * and the array values are used to set the actual name of the item.
 */
class UserAgents extends BaseConfig
{
    /**
     * -------------------------------------------------------------------
     * OS Platforms
     * -------------------------------------------------------------------
     *
     * @var array<string, string>
     */
    public array $platforms = [
        'windows nt 10.0' => 'Windows 10',
        'windows nt 6.3'  => 'Windows 8.1',
        'windows nt 6.2'  => 'Windows 8',
        'windows nt 6.1'  => 'Windows 7',
        'windows nt 6.0'  => 'Windows Vista',
        'windows nt 5.2'  => 'Windows 2003',
        'windows nt 5.1'  => 'Windows XP',
        'windows nt 5.0'  => 'Windows 2000',
        'windows nt 4.0'  => 'Windows NT 4.0',
        'winnt4.0'        => 'Windows NT 4.0',
        'winnt 4.0'       => 'Windows NT',
        'winnt'           => 'Windows NT',
        'windows 98'      => 'Windows 98',
        'win98'           => 'Windows 98',
        'windows 95'      => 'Windows 95',
        'win95'           => 'Windows 95',
        'windows phone'   => 'Windows Phone',
        'windows'         => 'Unknown Windows OS',
        'android'         => 'Android',
        'blackberry'      => 'BlackBerry',
        'iphone'          => 'iOS',
        'ipad'            => 'iOS',
        'ipod'            => 'iOS',
        'os x'            => 'Mac OS X',
        'ppc mac'         => 'Power PC Mac',
        'freebsd'         => 'FreeBSD',
        'ppc'             => 'Macintosh',
        'linux'           => 'Linux',
        'debian'          => 'Debian',
        'sunos'           => 'Sun Solaris',
        'beos'            => 'BeOS',
        'apachebench'     => 'ApacheBench',
        'aix'             => 'AIX',
        'irix'            => 'Irix',
        'osf'             => 'DEC OSF',
        'hp-ux'           => 'HP-UX',
        'netbsd'          => 'NetBSD',
        'bsdi'            => 'BSDi',
        'openbsd'         => 'OpenBSD',
        'gnu'             => 'GNU/Linux',
        'unix'            => 'Unknown Unix OS',
        'symbian'         => 'Symbian OS',
    ];

    /**
     * -------------------------------------------------------------------
     * Browsers
     * -------------------------------------------------------------------
     *
     * The order of this array should NOT be changed. Many browsers return
     * multiple browser types so we want to identify the subtype first.
     *
     * @var array<string, string>
     */
    public array $browsers = [
        'OPR'    => 'Opera',
        'Flock'  => 'Flock',
        'Edge'   => 'Spartan',
        'Edg'    => 'Edge',
        'Chrome' => 'Chrome',
        // Opera 10+ always reports Opera/9.80 and appends Version/<real version> to the user agent string
        'Opera.*?Version'   => 'Opera',
        'Opera'             => 'Opera',
        'MSIE'              => 'Internet Explorer',
        'Internet Explorer' => 'Internet Explorer',
        'Trident.* rv'      => 'Internet Explorer',
        'Shiira'            => 'Shiira',
        'Firefox'           => 'Firefox',
        'Chimera'           => 'Chimera',
        'Phoenix'           => 'Phoenix',
        'Firebird'          => 'Firebird',
        'Camino'            => 'Camino',
        'Netscape'          => 'Netscape',
        'OmniWeb'           => 'OmniWeb',
        'Safari'            => 'Safari',
        'Mozilla'           => 'Mozilla',
        'Konqueror'         => 'Konqueror',
        'icab'              => 'iCab',
        'Lynx'              => 'Lynx',
        'Links'             => 'Links',
        'hotjava'           => 'HotJava',
        'amaya'             => 'Amaya',
        'IBrowse'           => 'IBrowse',
        'Maxthon'           => 'Maxthon',
        'Ubuntu'            => 'Ubuntu Web Browser',
        'Vivaldi'           => 'Vivaldi',
    ];

    /**
     * -------------------------------------------------------------------
     * Mobiles
     * -------------------------------------------------------------------
     *
     * @var array<string, string>
     */
    public array $mobiles = [
        // legacy array, old values commented out
        'mobileexplorer' => 'Mobile Explorer',
        // 'openwave'             => 'Open Wave',
        // 'opera mini'           => 'Opera Mini',
        // 'operamini'            => 'Opera Mini',
        // 'elaine'               => 'Palm',
        'palmsource' => 'Palm',
        // 'digital paths'        => 'Palm',
        // 'avantgo'              => 'Avantgo',
        // 'xiino'                => 'Xiino',
        'palmscape' => 'Palmscape',
        // 'nokia'                => 'Nokia',
        // 'ericsson'             => 'Ericsson',
        // 'blackberry'           => 'BlackBerry',
        // 'motorola'             => 'Motorola'

        // Phones and Manufacturers
        'motorola'             => 'Motorola',
        'nokia'                => 'Nokia',
        'palm'                 => 'Palm',
        'iphone'               => 'Apple iPhone',
        'ipad'                 => 'iPad',
        'ipod'                 => 'Apple iPod Touch',
        'sony'                 => 'Sony Ericsson',
        'ericsson'             => 'Sony Ericsson',
        'blackberry'           => 'BlackBerry',
        'cocoon'               => 'O2 Cocoon',
        'blazer'               => 'Treo',
        'lg'                   => 'LG',
        'amoi'                 => 'Amoi',
        'xda'                  => 'XDA',
        'mda'                  => 'MDA',
        'vario'                => 'Vario',
        'htc'                  => 'HTC',
        'samsung'              => 'Samsung',
        'sharp'                => 'Sharp',
        'sie-'                 => 'Siemens',
        'alcatel'              => 'Alcatel',
        'benq'                 => 'BenQ',
        'ipaq'                 => 'HP iPaq',
        'mot-'                 => 'Motorola',
        'playstation portable' => 'PlayStation Portable',
        'playstation 3'        => 'PlayStation 3',
        'playstation vita'     => 'PlayStation Vita',
        'hiptop'               => 'Danger Hiptop',
        'nec-'                 => 'NEC',
        'panasonic'            => 'Panasonic',
        'philips'              => 'Philips',
        'sagem'                => 'Sagem',
        'sanyo'                => 'Sanyo',
        'spv'                  => 'SPV',
        'zte'                  => 'ZTE',
        'sendo'                => 'Sendo',
        'nintendo dsi'         => 'Nintendo DSi',
        'nintendo ds'          => 'Nintendo DS',
        'nintendo 3ds'         => 'Nintendo 3DS',
        'wii'                  => 'Nintendo Wii',
        'open web'             => 'Open Web',
        'openweb'              => 'OpenWeb',

        // Operating Systems
        'android'    => 'Android',
        'symbian'    => 'Symbian',
        'SymbianOS'  => 'SymbianOS',
        'elaine'     => 'Palm',
        'series60'   => 'Symbian S60',
        'windows ce' => 'Windows CE',

        // Browsers
        'obigo'         => 'Obigo',
        'netfront'      => 'Netfront Browser',
        'openwave'      => 'Openwave Browser',
        'mobilexplorer' => 'Mobile Explorer',
        'operamini'     => 'Opera Mini',
        'opera mini'    => 'Opera Mini',
        'opera mobi'    => 'Opera Mobile',
        'fennec'        => 'Firefox Mobile',

        // Other
        'digital paths' => 'Digital Paths',
        'avantgo'       => 'AvantGo',
        'xiino'         => 'Xiino',
        'novarra'       => 'Novarra Transcoder',
        'vodafone'      => 'Vodafone',
        'docomo'        => 'NTT DoCoMo',
        'o2'            => 'O2',

        // Fallback
        'mobile'     => 'Generic Mobile',
        'wireless'   => 'Generic Mobile',
        'j2me'       => 'Generic Mobile',
        'midp'       => 'Generic Mobile',
        'cldc'       => 'Generic Mobile',
        'up.link'    => 'Generic Mobile',
        'up.browser' => 'Generic Mobile',
        'smartphone' => 'Generic Mobile',
        'cellphone'  => 'Generic Mobile',
    ];

    /**
     * -------------------------------------------------------------------
     * Robots
     * -------------------------------------------------------------------
     *
     * There are hundred of bots but these are the most common.
     *
     * @var array<string, string>
     */
    public array $robots = [
        // Google
        'googlebot'            => 'Googlebot',
        'mediapartners-google' => 'MediaPartners Google',
        'adsbot-google'        => 'AdsBot Google',
        'feedfetcher-google'   => 'Feedfetcher Google',
        'googlebot-image'      => 'Googlebot-Image',
        'googlebot-news'       => 'Googlebot-News',
        'googlebot-video'      => 'Googlebot-Video',
        'googlebot-mobile'     => 'Googlebot-Mobile',
        'google-extended'      => 'Google Extended',

        // Bing / Microsoft
        'bingbot'              => 'Bing',
        'msnbot'               => 'MSNBot',
        'msnbot-media'         => 'MSNBot-Media',
        'bingpreview'          => 'BingPreview',

        // Yahoo
        'slurp'                => 'Inktomi Slurp',
        'yahoo'                => 'Yahoo! Slurp',

        // Yandex
        'yandex'               => 'YandexBot',
        'yandeximages'         => 'YandexImages',
        'yandexvideo'          => 'YandexVideo',
        'yandexnews'           => 'YandexNews',
        'yandexblogs'          => 'YandexBlogs',
        'yandexmetrika'        => 'YandexMetrika',

        // Baidu
        'baiduspider'          => 'Baiduspider',
        'baiduspider-image'    => 'Baidu Image Spider',
        'baiduspider-news'     => 'Baidu News Spider',
        'baiduspider-video'    => 'Baidu Video Spider',

        // DuckDuckGo
        'duckduckbot'          => 'DuckDuckGo Bot',

        // Ask Jeeves
        'ask jeeves'           => 'Ask Jeeves',

        // Archive / Caching
        'ia_archiver'          => 'Alexa Crawler',
        'mj12bot'              => 'Majestic-12',
        'commoncrawl'          => 'Common Crawl',
        'archive.org_bot'      => 'Archive.org Bot',

        // Cloudflare / Uptime Bots
        'uptimebot'            => 'Uptimebot',
        'pingdom'              => 'PingdomBot',
        'statuscake'           => 'StatusCake Bot',

        // Facebook
        'facebookexternalhit'  => 'Facebook Bot',
        'facebot'              => 'Facebot',

        // Twitter
        'twitterbot'           => 'Twitter Bot',

        // LinkedIn
        'linkedinbot'          => 'LinkedIn Bot',

        // Other Known Crawlers
        'fastcrawler'          => 'FastCrawler',
        'infoseek'             => 'InfoSeek Robot 1.0',
        'lycos'                => 'Lycos',
        'coccocbot'            => 'CocCocBot',
        'applebot'             => 'AppleBot',
        'sogou'                => 'Sogou Spider',
        'seznambot'            => 'SeznamBot',
        'exabot'               => 'ExaBot',
        'ahrefsbot'            => 'AhrefsBot',
        'semrushbot'           => 'SEMRushBot',
        'dotbot'               => 'DotBot',
        'screaming frog'       => 'Screaming Frog SEO Spider',
        'petalbot'             => 'PetalBot (Huawei)',
        'gigabot'              => 'Gigabot',
        'buzzbot'              => 'BuzzBot',
        'turnitinbot'          => 'Turnitin Bot',
        'outbrain'             => 'Outbrain Crawler',
        'linkdexbot'           => 'Linkdex Bot',
        'magpie-crawler'       => 'Magpie Crawler',
        'garlikcrawler'        => 'Garlik Crawler',
        'crazywebcrawler'      => 'Crazy Webcrawler',

        // AI & Research Crawlers
        'openai'               => 'OpenAI Bot',
        'anthropic'            => 'Anthropic AI Crawler',
        'deepmind'             => 'DeepMind Crawler',
        'perplexitybot'        => 'Perplexity Bot',

        // General Web Crawlers
        'curl'                 => 'cURL Request',
        'wget'                 => 'Wget Request',
        'python-requests'      => 'Python Requests',
        'httpclient'           => 'Generic HTTP Client',

        // Additional Crawlers
        'nutch'                => 'Apache Nutch',
        'scrapy'               => 'Scrapy',
        'heritrix'             => 'Heritrix',
        'blekkobot'            => 'Blekko Bot',
        'genieo'               => 'Genieo Web Filter',
        'sistrix'              => 'SISTRIX Crawler',
        'rogerbot'             => 'Rogerbot',
        'siteexplorer'         => 'SiteExplorer',
        'domainreanimator'     => 'Domain Re-Animator Bot',
        'proximic'             => 'Proximic Spider',
        'spinn3r'              => 'Spinn3r',
        'zoominfobot'          => 'ZoomInfoBot',
        'ccbot'                => 'CCBot',
        'netseer'              => 'NetSeer Crawler',
        'meanpathbot'          => 'Meanpath Bot',
        'qwantify'             => 'Qwantify Bot',
        'surdotlybot'          => 'SurdotlyBot',
        'mail.ru'              => 'Mail.Ru Bot',
        'dumbot'               => 'Dumbot',
        'yodaobot'             => 'YodaoBot',
        '360spider'            => '360Spider',
        'sputnikbot'           => 'SputnikBot',
        'yeti'                 => 'Naver Yeti',
        'daumoa'               => 'Daumoa',
        'ezooms'               => 'Ezooms',
        'friendly'             => 'Friendly Spider',
        'grapeshot'            => 'GrapeshotCrawler',
        'holmes'               => 'Holmes',
        'istellabot'           => 'IstellaBot',
        'ltx71'                => 'ltx71',
        'nerdybot'             => 'NerdyBot',
        'omgilibot'            => 'Omgili bot',
        'omgilibot'            => 'Omgili bot',
        'psbot'                => 'PSBot',
        'redditbot'            => 'Reddit Bot',
        'surdotlybot'          => 'SurdotlyBot',
        'tagoobot'             => 'Tagoobot',
        'trendictionbot'       => 'Trendiction Bot',
        'voyager'              => 'Voyager',
        'wotbox'               => 'Wotbox',
        'yacybot'              => 'YacyBot',
        'yoozbot'              => 'YoozBot',
        'zgrab'                => 'ZGrab',
    ];
}
