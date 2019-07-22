<?php

/**
 * hlock class
 * PHP Version 7
 *
 * @see       https://github.com/Trebaxa/hlock
 * @version   1.5  
 * @author    Harald Petrich <service@trebaxa.com>
 * @copyright 2018 - 2019 Harald Petrich
 * @license   GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. This project should help us developer to protect our PHP projects from hacking. Bad IPs will be reported to central server 
 * and hlock updates hisself with a current list of bad ips, bots and SQL injection rules.
 * Be part of the network and help us to get the web safer!
 *  
 * This version is compatible with keimeno CMS, but can easly changed to be compatible with Wordpress, Joomla and Typo3.
 * Just change the path to files and ensure the HLOCK_ROOT is successfully set.
 * 
 * Install WordPress
 * 1. add hlock.class.php to folder /wp-include
 * 2. add PHP code to index.php in root: require ( './wp-includes/hlock.class.php');hlock::run(dirname(__FILE__));
 * 
 * Install TYPO3
 * 1. add hlock.class.php to folder / where index.php is located
 * 2. add PHP code to index.php in root: require ( './typo3_src/hlock.class.php');hlock::run(dirname(__FILE__));
 * 
 * Install Keimeno
 * 1. already implemented ;-)
 * 2. Take the better CMS
 */

# define subpath of your project. last char must be a /
define('SUB_PATH_OF_SYSTEM', '');
date_default_timezone_set('Europe/Berlin');

class hlock {
    # standard settings
    protected static $config = array(
        'hcache_lifetime_hours' => 3,
        'blacklist_lifetime_hours' => 1,
        'log_lines_count' => 98,
        'email' => '',
        'mime_types_filter_active' => true,
        'forbidden_file_ext' => array(
            'php',
            'php3',
            'php5',
            'exe',
            'cmd',
            'bat'),
        
        'allowed_mime_types' => array(
            'image/svg+xml' => 'svg',
            'image/svg' => 'svg',
            'image/jpeg' => 'jpg|jpeg|jpe',
            'image/gif' => 'gif',
            'image/png' => 'png',
            'image/bmp' => 'bmp',
            'image/tiff' => 'tif|tiff',
            'image/x-icon' => 'ico',
            'video/x-ms-asf' => 'asf|asx',
            'video/x-ms-wmv' => 'wmv',
            'video/x-ms-wmx' => 'wmx',
            'video/x-ms-wm' => 'wm',
            'video/avi' => 'avi',
            'video/divx' => 'divx',
            'video/x-flv' => 'flv',
            'video/quicktime' => 'mov|qt',
            'video/mpeg' => 'mpeg|mpg|mpe',
            'video/mp4' => 'mp4|m4v',
            'video/ogg' => 'ogv',
            'video/webm' => 'webm',
            'video/x-matroska' => 'mkv',
            'text/plain' => 'txt|asc|c|cc|h',
            'text/csv' => 'csv',
            'text/tab-separated-values' => 'tsv',
            'text/calendar' => 'ics',
            'text/richtext' => 'rtx',
            'text/css' => 'css',
            'text/html' => 'htm|html',
            'audio/mpeg' => 'mp3|m4a|m4b',
            'audio/x-realaudio' => 'ra|ram',
            'audio/wav' => 'wav',
            'audio/ogg' => 'ogg|oga',
            'audio/midi' => 'mid|midi',
            'audio/x-ms-wma' => 'wma',
            'audio/x-ms-wax' => 'wax',
            'audio/x-matroska' => 'mka',
            'application/rtf' => 'rtf',
            'application/javascript' => 'js',
            'application/pdf' => 'pdf',
            'application/x-shockwave-flash' => 'swf',
            'application/java' => 'class',
            'application/x-tar' => 'tar',
            'application/zip' => 'zip',
            'application/x-gzip' => 'gz|gzip',
            'application/rar' => 'rar',
            'application/x-7z-compressed' => '7z',
            'application/msword' => 'doc',
            'application/vnd.ms-powerpoint' => 'pot|pps|ppt',
            'application/vnd.ms-write' => 'wri',
            'application/vnd.ms-excel' => 'xla|xls|xlt|xlw',
            'application/vnd.ms-access' => 'mdb',
            'application/vnd.ms-project' => 'mpp',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-word.document.macroEnabled.12' => 'docm',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.template' => 'dotx',
            'application/vnd.ms-word.template.macroEnabled.12' => 'dotm',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-excel.sheet.macroEnabled.12' => 'xlsm',
            'application/vnd.ms-excel.sheet.binary.macroEnabled.12' => 'xlsb',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.template' => 'xltx',
            'application/vnd.ms-excel.template.macroEnabled.12' => 'xltm',
            'application/vnd.ms-excel.addin.macroEnabled.12' => 'xlam',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/vnd.ms-powerpoint.presentation.macroEnabled.12' => 'pptm',
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow' => 'ppsx',
            'application/vnd.ms-powerpoint.slideshow.macroEnabled.12' => 'ppsm',
            'application/vnd.openxmlformats-officedocument.presentationml.template' => 'potx',
            'application/vnd.ms-powerpoint.template.macroEnabled.12' => 'potm',
            'application/vnd.ms-powerpoint.addin.macroEnabled.12' => 'ppam',
            'application/vnd.openxmlformats-officedocument.presentationml.slide' => 'sldx',
            'application/vnd.ms-powerpoint.slide.macroEnabled.12' => 'sldm',
            'application/onenote' => 'onetoc|onetoc2|onetmp|onepkg',
            'application/vnd.oasis.opendocument.text' => 'odt',
            'application/vnd.oasis.opendocument.presentation' => 'odp',
            'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
            'application/vnd.oasis.opendocument.graphics' => 'odg',
            'application/vnd.oasis.opendocument.chart' => 'odc',
            'application/vnd.oasis.opendocument.database' => 'odb',
            'application/vnd.oasis.opendocument.formula' => 'odf',
            'application/wordperfect' => 'wp|wpd',
            'application/vnd.apple.keynote' => 'key',
            'application/vnd.apple.numbers' => 'numbers',
            'application/vnd.apple.pages' => 'pages',
            ));
    protected static $hlock_root = "";
    protected static $host = "";

    /**
     * hlock::auto_detect_system()
     * 
     * @return void
     */
    protected static function auto_detect_system() {
        static::$host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
        # default
        self::set_config_arr(array(
            'hpath' => static::$hlock_root . 'includes/lib/hlock/accesslog/',
            'sub_folder' => static::$hlock_root . 'includes/lib/hlock/',
            'hlock_blocked_file' => static::$hlock_root . 'includes/lib/hlock/hacklogblock_' . static::$host . '.txt',
            'hlock_blacklist' => static::$hlock_root . 'includes/lib/hlock/blacklist.json',
            'badips_file' => static::$hlock_root . 'includes/lib/hlock/badips_' . static::$host . '.txt',
            'badbots_file' => static::$hlock_root . 'includes/lib/hlock/badbots_' . static::$host . '.txt',
            ));

        # detect Keimeno CMS
        if (is_dir(static::$hlock_root . 'admin') && is_file(static::$hlock_root . 'admin/inc/keimeno.class.php')) {
            self::set_config_arr(array(
                'hpath' => static::$hlock_root . 'includes/lib/hlock/accesslog/',
                'sub_folder' => static::$hlock_root . 'includes/lib/hlock/',
                'hlock_blocked_file' => static::$hlock_root . 'includes/lib/hlock/hacklogblock_' . static::$host . '.txt',
                'hlock_blacklist' => static::$hlock_root . 'includes/lib/hlock/blacklist.json',
                'badips_file' => static::$hlock_root . 'includes/lib/hlock/badips_' . static::$host . '.txt',
                'badbots_file' => static::$hlock_root . 'includes/lib/hlock/badbots_' . static::$host . '.txt',
                ));
        }

        # detect WordPress
        if (is_dir(static::$hlock_root . 'wp-admin')) {
            self::set_config_arr(array(
                'sub_folder' => static::$hlock_root . 'wp-content/hlock/',
                'hpath' => static::$hlock_root . 'wp-content/hlock/accesslog/',
                'hlock_blocked_file' => static::$hlock_root . 'wp-content/hlock/hacklogblock_' . static::$host . '.txt',
                'hlock_blacklist' => static::$hlock_root . 'wp-content/hlock/blacklist.json',
                'badips_file' => static::$hlock_root . 'wp-content/hlock/badips_' . static::$host . '.txt',
                'badbots_file' => static::$hlock_root . 'wp-content/hlock/badbots_' . static::$host . '.txt',
                ));
        }

        #detect TYPO3
        if (is_dir(static::$hlock_root . 'fileadmin') && is_dir(static::$hlock_root . 'typo3conf')) {
            self::set_config_arr(array(
                'sub_folder' => static::$hlock_root . 'fileadmin/hlock/',
                'hpath' => static::$hlock_root . 'fileadmin/hlock/accesslog/',
                'hlock_blocked_file' => static::$hlock_root . 'fileadmin/hlock/hacklogblock_' . static::$host . '.txt',
                'hlock_blacklist' => static::$hlock_root . 'fileadmin/hlock/blacklist.json',
                'badips_file' => static::$hlock_root . 'fileadmin/hlock/badips_' . static::$host . '.txt',
                'badbots_file' => static::$hlock_root . 'fileadmin/hlock/badbots_' . static::$host . '.txt',
                ));
        }

        if (!is_dir(static::$config['sub_folder']))
            mkdir(static::$config['sub_folder'], 0755);
        if (!is_dir(static::$config['hpath']))
            mkdir(static::$config['hpath'], 0755);
    }

    /**
     * hlock::get_the_ip()
     * 
     * @return
     */
    public static function get_the_ip() {
        return isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    }

    /**
     * hlock::_p()
     * 
     * @param mixed $arr
     * @return void
     */
    public static function _p($arr) {
        echo '<pre>' . print_r((array )$arr, 1) . '</pre>';
    }

    /**
     * hlock::set_config_value()
     * 
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    protected static function set_config_value($key, $value) {
        static::$config[$key] = $value;
    }

    /**
     * hlock::set_config_arr()
     * 
     * @param mixed $arr
     * @return void
     */
    protected static function set_config_arr($arr) {
        static::$config = array_merge(static::$config, $arr);
    }

    /**
     * hlock::set_root()
     * 
     * @param mixed $path
     * @return void
     */
    protected static function set_root($path) {
        if (empty($path)) {
            static::$hlock_root = $_SERVER['DOCUMENT_ROOT'] . (substr($_SERVER['DOCUMENT_ROOT'], -1) == DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR) . SUB_PATH_OF_SYSTEM;
        }
        else {
            static::$hlock_root = $path . (substr($path, -1) == DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR);
        }
    }

    /**
     * hlock::check_filename()
     * 
     * @param mixed $name
     * @return void
     */
    public static function check_filename($name) {
        $ext = end((explode(".", $name)));
        if (in_array($ext, static::$config['forbidden_file_ext']) || preg_match("/^.*\.([a-zA-Z]{3}).html$/", $name) || preg_match("/^.*\.([a-zA-Z]{3}).htm$/", $name)) {
            self::report_hack('invalid file upload');
            self::exit_env('FILE_UPLOAD ' . $ext);
        }
    }

    /**
     * hlock::check_mime()
     * 
     * @param mixed $file
     * @return void
     */
    protected static function check_mime($file) {
        if (static::$config['mime_types_filter_active'] == true) {
            foreach (static::$config['allowed_mime_types'] as $mime => $ext) {
                if ($file["type"] == $mime) {
                    return;
                }
            }
            self::report_hack('invalid file upload ' . $file["type"]);
            self::exit_env('MIME_FILE_UPLOAD ' . $file["type"]);
        }
    }

    /**
     * hlock::file_upload_protection()
     * 
     * @return void
     */
    public static function file_upload_protection() {
        if (isset($_FILES)) {
            foreach ($_FILES as $key => $row) {
                $ext = end((explode(".", $_FILES[$key]["name"])));
                if (!is_array($_FILES[$key]["name"])) {
                    self::check_filename($_FILES[$key]["name"]);
                    self::check_mime($row);
                }
                else {
                    foreach ($_FILES[$key]['name'] as $keya => $row2) {
                        self::check_filename($_FILES[$key]["name"][$keya]);
                        self::check_mime($row2);
                    }
                }
            }
        }
    }

    /**
     * hlock::run()
     * 
     * @return void
     */
    public static function run($path = "") {
        self::set_root($path);
        self::auto_detect_system();

        if ($handle = opendir(static::$config['hpath'])) {
            while (false !== ($file = readdir($handle))) {
                if ((integer)(time() - filemtime(static::$config['hpath'] . $file)) > (static::$config['hcache_lifetime_hours'] * 3600) && $file !== '.' && $file !== '..') {
                    @unlink(static::$config['hpath'] . $file);
                }
            }
        }

        $fname = (strstr($_SERVER['HTTP_USER_AGENT'], 'bot')) ? $_SERVER['HTTP_USER_AGENT'] : $_SERVER['HTTP_USER_AGENT'] . self::get_the_ip();
        $hfile = static::$config['hpath'] . md5($fname);
        $hcount = 0;
        if (is_file($hfile)) {
            $arr = explode(PHP_EOL, file_get_contents($hfile));
            $hcount = (int)$arr[0];
            $hcount++;
        }
        file_put_contents($hfile, implode(PHP_EOL, array(
            $hcount,
            $_SERVER['HTTP_USER_AGENT'],
            self::get_the_ip(),
            date('Y-m-d H:i:s'),
            )));

        self::file_upload_protection();
        self::block_bad_bots();
        self::block_bad_ips();
        self::detect_injection();
        self::clear_blocked();
        self::block_ips_and_bots_from_blacklist();
        #self::check_agent();

        if (isset($_GET['hlock'])) {
            $arr = array();
            $result = self::read_logs();
            self::echo_table($result['hour_log'], $result['hour_log_count'] . ' Clients (last hour)');
            self::echo_table($result['blocked_bots'], 'Bad Bot blocked list');
            die();
        }
    }

    /**
     * hlock::block_ips_and_bots_from_blacklist()
     * 
     * @return void
     */
    private static function block_ips_and_bots_from_blacklist() {
        $user_agent = self::get_user_agent();
        $json = json_decode(self::get_black_list(), true);

        # check bad IPs
        $json['badips'] = (array )$json['badips'];
        if (isset($json['badips'][self::get_the_ip()])) {
            self::exit_env('BLACK_LIST_IP' . $row['i_ip']);
        }

        #check bots
        foreach ((array )$json['bots'] as $row) {
            $bot_key = trim(strtolower($row['b_bot']));
            if (!empty($bot_key) && strstr($user_agent, $bot_key)) {
                self::exit_env('BLACK_LIST_BOT');
            }
        }

    }

    /**
     * hlock::check_agent()
     * 
     * @return void
     */
    private static function check_agent() {
        # invalid USER AGENT
        $user_agent = self::get_user_agent();
        if (strlen($user_agent) < 2) {
            self::report_hack('invalid user agent');
            self::exit_env('USER_AGENT');
        }
    }

    /**
     * hlock::get_user_agent()
     * 
     * @return
     */
    public static function get_user_agent() {
        return isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 254) : '';
    }

    /**
     * hlock::read_logs()
     * 
     * @param mixed $k
     * @return void
     */
    public static function read_logs() {
        $k = 0;
        $result['hour_log'] = $result['blocked_bots'] = array();
        if ($handle = opendir(static::$config['hpath'])) {
            while (false !== ($file = readdir($handle))) {
                if ($file !== '.' && $file !== '..') {
                    $result['hour_log'][] = explode(PHP_EOL, file_get_contents(static::$config['hpath'] . $file));
                }
                $k++;
            }
        }
        if (is_file(static::$config['hlock_blocked_file'])) {
            $blocked = explode(PHP_EOL, file_get_contents(static::$config['hlock_blocked_file']));
            foreach ($blocked as $key => $line) {
                $result['blocked_bots'][] = explode("\t", $line);
            }
        }
        $result['hour_log_count'] = $k;
        return $result;
    }

    /**
     * hlock::read_lines_from_file()
     * 
     * @param mixed $file
     * @param mixed $maxLines
     * @param bool $reverse
     * @return
     */
    protected static function read_lines_from_file($file, $maxLines, $reverse = false) {
        $lines = file($file);
        if ($reverse) {
            $lines = array_reverse($lines);
        }
        $tmpArr = array();
        if ($maxLines > count($lines)) {
            return false;
        }

        for ($i = 0; $i < $maxLines; $i++) {
            array_push($tmpArr, $lines[$i]);
        }
        if ($reverse) {
            $tmpArr = array_reverse($tmpArr);
        }
        $out = "";
        for ($i = 0; $i < $maxLines; $i++) {
            $out .= $tmpArr[$i] . PHP_EOL;
        }
        return $out;
    }

    /**
     * hlock::clear_blocked()
     * 
     * @return void
     */
    protected static function clear_blocked() {
        if (is_file(static::$config['hlock_blocked_file']) && filesize(static::$config['hlock_blocked_file']) > 6000) {
            $lines = self::read_lines_from_file(static::$config['hlock_blocked_file'], static::$config['log_lines_count'], true);
            if ($lines !== false && is_string($lines))
                file_put_contents(static::$config['hlock_blocked_file'], $lines);
        }
    }

    /**
     * hlock::block_bad_bots()
     * 
     * @return void
     */
    protected static function block_bad_bots() {
        $badbots = self::get_bad_bots();
        if ($_SERVER['HTTP_USER_AGENT'] != str_ireplace($badbots, '*', $_SERVER['HTTP_USER_AGENT'])) {
            $fp = fopen(static::$config['hlock_blocked_file'], 'a+');
            fwrite($fp, implode("\t", array(
                date('Y-m-d H:i:s'),
                $_SERVER['HTTP_USER_AGENT'],
                'AGENT',
                self::get_the_ip())) . PHP_EOL);
            fclose($fp);
            self::exit_env('BOT');
        }
    }

    /**
     * hlock::block()
     * 
     * @return void
     */
    protected static function exit_env($reason = "") {
        header('HTTP/1.0 403 Forbidden');
        die('Bad agent [' . $reason . ']');
    }

    /**
     * hlock::block_bad_ips()
     * 
     * @return void
     */
    protected static function block_bad_ips() {
        $badips = self::get_bad_ips();
        if (in_array(self::get_the_ip(), $badips)) {
            $fp = fopen(static::$config['hlock_blocked_file'], 'a+');
            fwrite($fp, implode("\t", array(
                date('Y-m-d H:i:s'),
                $_SERVER['HTTP_USER_AGENT'],
                'IP',
                self::get_the_ip())) . PHP_EOL);
            fclose($fp);
            self::exit_env('IP');
        }
    }

    /**
     * hlock::get_bad_bots()
     * 
     * @return
     */
    protected static function get_bad_bots() {
        if (is_file(static::$config['badbots_file'])) {
            return explode(PHP_EOL, file_get_contents(static::$config['badbots_file']));
        }
        else
            return array();
    }

    /**
     * hlock::get_bad_ips()
     * 
     * @return
     */
    protected static function get_bad_ips() {
        if (is_file(static::$config['badips_file'])) {
            return explode(PHP_EOL, file_get_contents(static::$config['badips_file']));
        }
        else
            return array();
    }

    /**
     * hlock::echo_table()
     * 
     * @param mixed $table
     * @param mixed $title
     * @return void
     */
    protected static function echo_table($table, $title) {
        echo '<h3>' . $title . '</h3><table>';
        foreach ((array )$table as $key => $row) {
            echo '<tr>';
            foreach ($row as $value) {
                echo '<td>' . $value . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * hlock::get_backend()
     * 
     * @return
     */
    public function get_backend() {
        self::set_root($path);
        self::auto_detect_system();
        return array(
            'bad_ips' => (implode(PHP_EOL, self::get_bad_ips())),
            'bad_bots' => (implode(PHP_EOL, self::get_bad_bots())),
            );
    }

    /**
     * hlock::save()
     * 
     * @return void
     */
    public function save() {
        $ip_list = array();
        $FORM = (array )$_POST['FORM'];
        $arr = explode(PHP_EOL, stripslashes($FORM['bad_ips']));
        foreach ($arr as $ip) {
            $ip = trim($ip);
            if (self::is_valid_ip($ip)) {
                $ip_list[] = $ip;
            }
        }
        $ip_list = array_unique($ip_list);
        file_put_contents(static::$config['badips_file'], trim(implode(PHP_EOL, $ip_list)));
        file_put_contents(static::$config['badbots_file'], stripslashes($FORM['bad_bots']));
    }

    /**
     * hlock::add_ip()
     * 
     * @param mixed $ip
     * @return void
     */
    public static function add_ip($ip) {
        $ip = trim($ip);
        if (self::is_valid_ip($ip)) {
            $ip_list = self::get_bad_ips();
            $ip_list[] = trim($ip);
            $ip_list = array_unique($ip_list);
            file_put_contents(static::$config['badips_file'], implode(PHP_EOL, $ip_list));
        }
    }

    /**
     * hlock::remove_ip()
     * 
     * @param mixed $ip
     * @return void
     */
    public static function remove_ip($ip) {
        $ip_list = self::get_bad_ips();
        $ip_list = array_diff($ip_list, array($ip));
        file_put_contents(static::$config['badips_file'], implode(PHP_EOL, $ip_list));
    }

    /**
     * hlock::is_valid_ip()
     * 
     * @param mixed $ip
     * @return
     */
    public static function is_valid_ip($ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP) && !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return false;
        }
        return true;
    }

    /**
     * hlock::get_query_string()
     * 
     * @return
     */
    private static function get_query_string() {
        return $_SERVER['QUERY_STRING'];
    }

    /**
     * hlock::detect_injection()
     * 
     * @return void
     */
    public static function detect_injection() {
        $cracktrack = self::get_query_string();
        $json = json_decode(self::get_black_list(), true);
        foreach ((array )$json['sqlinject'] as $row) {
            $wormprotector[] = $row['i_term'];
        }

        $checkworm = str_ireplace($wormprotector, '*', $cracktrack);
        if ($cracktrack != $checkworm) {
            self::add_ip(self::get_the_ip());
            self::report_hack('SQL Injection blocked');
            if (filter_var(static::$email, FILTER_VALIDATE_EMAIL)) {
                $mail_msg = 'Hacking blocked [SQLINJECTION]: ' . PHP_EOL;
                $arr = array(
                    'IP' => self::get_the_ip(),
                    'Host' => $_SERVER['HTTP_HOST'],
                    'Trace' => 'https://www.ip-tracker.org/locator/ip-lookup.php?ip=' . self::get_the_ip(),
                    'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
                    'cracktrack' => $cracktrack,
                    "Hacked" => $checkworm);
                foreach ($arr as $key => $value) {
                    $mail_msg .= $key . ":\t" . $value . PHP_EOL;
                }
                $header = 'From: ' . static::$config['email'] . "\r\n" . 'Reply-To: ' . static::$config['email'] . "\r\n" . 'X-Mailer: PHP/' . phpversion();
                mail(static::$config['email'], 'IP blocked: [SQLINJECTION] ' . $_SERVER['HTTP_HOST'], $mail_msg, $header, '-f' . static::$config['email']);
            }
            self::exit_env('INJECT');
        }
    }

    /**
     * hlock::report_hack()
     * 
     * @param mixed $type_info
     * @return void
     */
    private static function report_hack($type_info) {
        $user_agent = self::get_user_agent();
        $arr = array(
            'FORM[h_type]' => $type_info,
            'FORM[h_domain]' => $_SERVER['HTTP_HOST'],
            'FORM[h_ip]' => self::get_the_ip(),
            'FORM[h_url]' => base64_encode($_SERVER['PHP_SELF'] . '###' . $_SERVER['QUERY_STRING'] . '###' . http_build_query($_REQUEST)),
            'cmd' => 'log_hacking',
            'FORM_IP[b_iphash]' => md5(self::get_the_ip()),
            'FORM_IP[b_ua]' => $user_agent,
            'FORM_IP[b_ip]' => self::get_the_ip(),
            );
        self::curl_get_data('https://www.keimeno.de/report-hack.html', $arr);
    }

    /**
     * hlock::get_black_list()
     * 
     * @return void
     */
    public static function get_black_list() {
        if (is_file(static::$config['hlock_blacklist']) && (integer)(time() - filemtime(static::$config['hlock_blacklist'])) > (static::$config['blacklist_lifetime_hours'] *
            3600)) {
            @unlink(static::$config['hlock_blacklist']);
        }

        if (!is_file(static::$config['hlock_blacklist'])) {
            self::curl_get_data_to_file('https://www.keimeno.de/report-hack.html?cmd=get_black_iplist&FORM[host]=' . $_SERVER['HTTP_HOST'], static::$config['hlock_blacklist']);
        }
        return file_get_contents(static::$config['hlock_blacklist']);
    }

    /**
     * hlock::curl_get_data()
     * 
     * @param mixed $url
     * @param mixed $vars
     * @return
     */
    private static function curl_get_data($url, $vars = array()) {
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $url);
        if (is_array($vars) && count($vars) > 0) {
            curl_setopt($ch, CURLOPT_POST, 1);
            self::http_build_query_for_curl($vars, $curl_vars);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_vars);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * hlock::http_build_query_for_curl()
     * 
     * @param mixed $arrays
     * @param mixed $new
     * @param mixed $prefix
     * @return void
     */
    private static function http_build_query_for_curl($arrays, &$new = array(), $prefix = null) {
        if (is_object($arrays)) {
            $arrays = get_object_vars($arrays);
        }
        foreach ($arrays as $key => $value) {
            $k = isset($prefix) ? $prefix . '[' . $key . ']' : $key;
            if (is_array($value) or is_object($value)) {
                self::http_build_query_for_curl($value, $new, $k);
            }
            else {
                $new[$k] = $value;
            }
        }
    }

    /**
     * hlock::anonymizing_ip()
     * 
     * @param mixed $ip
     * @return
     */
    private static function anonymizing_ip($ip) {
        if (strpos($ip, ".") == true) {
            return preg_replace('#(?:\.\d+){1}$#', '.0', $ip);
        }
        else {
            return preg_replace('~[0-9]*:[0-9]+$~', 'XXXX:XXXX', $ip);
        }
    }

    /**
     * hlock::curl_get_data_to_file()
     * 
     * @param mixed $url
     * @param mixed $local_file
     * @return
     */
    public static function curl_get_data_to_file($url, $local_file) {
        $fp = fopen($local_file, 'w');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        #        echo $data;die; $failure = (strstr($data, '302 Found'));
        if ($data == false) {
            @unlink($local_file);
            return false;
        }
        if (filesize($local_file) < 10000) {
            if (strstr(file_get_contents($local_file), '302 Found')) {
                @unlink($local_file);
                return false;
            }
        }
        return true;
    }

}
