<?php

namespace My;

class General
{

    const SITE_DOMAIN_FULL = 'http://sharevideoclip.com';
    const TITLE_META = " - Sharevideoclip.com";
    const SITE_AUTH = "sharevideoclip.com";
    const SITE_DOMAIN = 'sharevideoclip.com';
    const SITE_SLOGAN = 'Share hot videos, funny videos, movie music';
    const SITE_HOTLINE = '(+84) 97 353 1618';
    const KEYWORD_DEFAULT = 'Sharevideoclip.com, share videos clip, video clip, video hot, clip hot, general video clip, good clip, good video,funny video, funny clip, video music, video film, sport video, football video, gaming video';
    const DESCRIPTION_DEFAULT = 'Sharevideoclip.com - Share hot videos, funny videos, movie music, top videos youtube, top social videos';
    const SITE_IMAGES_DEFAULT = 'http://static.sharevideoclip.com/fe/images/img-intro-1200x630.jpg';
    const SITE_SOCIAL = 'https://www.facebook.com/Share-Video-Community-616628808543081/';
    const SITE_LOCATION = "";
    const MEMBER = 5;
    const ADMINISTRATOR = 2;
    const MODERATOR = 3;
    const SEO = 4;
    const SUPPORTER = 5;
    const EDITOR = 6;
    const MS = 0;
    const MR = 1;
    const MRS = 2;
    //define email admin 
    const EMAIL_SUPPORT = 'ngocchien01@gmail.com';
    const EMAIL_BCC = 'ngocchien01@gmail.com';
    const EMAIL_SYS = 'ngocchien01@gmail.com';
    const EMAIL_SYS_PASSWORD = 'chien123';
    //define host mail
    const HOST_MAIL = 'smtp.gmail.com';

    const SEND_MAIL_MESSAGES = 1;
    //define social url
    const SOCIAL_FACEBOOK_URL = 'https://www.facebook.com/Share-Video-Community-616628808543081/';
    const SOCIAL_GOOGLE_PLUS_URL = 'https://www.facebook.com/Share-Video-Community-616628808543081/';
    const SOCIAL_TWITTER_URL = 'https://www.facebook.com/Share-Video-Community-616628808543081/';

    const REDIS_KEY_CONT_HOME_PAGE = 'key_cont_home_page';
    const REDIS_KEY_HOT_CONT_HOME_PAGE = 'key_cont_hot_home_page';

    const REDIS_KEY_CONTENT_TOP_DAY = 'key_content_top_day';
    const REDIS_KEY_CONTENT_TOP_WEEK = 'key_content_top_week';

    const REDIS_KEY_LIST_CATEGORY = 'cate:list';

    static $foreground_colors = array(
        'black' => '0;30', 'dark_gray' => '1;30',
        'blue' => '0;34', 'light_blue' => '1;34',
        'green' => '0;32', 'light_green' => '1;32',
        'cyan' => '0;36', 'light_cyan' => '1;36',
        'red' => '0;31', 'light_red' => '1;31',
        'purple' => '0;35', 'light_purple' => '1;35',
        'brown' => '0;33', 'yellow' => '1;33',
        'light_gray' => '0;37', 'white' => '1;37',
    );
    static $background_colors = array(
        'black' => '40', 'red' => '41',
        'green' => '42', 'yellow' => '43',
        'blue' => '44', 'magenta' => '45',
        'cyan' => '46', 'light_gray' => '47',
    );

    static $config_fb = array(
        'appId' => '1295525083828424',
        'secret' => '4cf1d6344a439929cff02521bfa553a0',
        'fb_id' => 616628808543081,
//        'access_token' => 'EAAJTaWjIMUkBAPzPyrhRFR85wEM6BRAxZCgaiwwkvuuZCRDTdoUvlusOj0a1KOZB4xJ6ZBMtig6F8TMQ8VWhXG6W8pGwZBUhyR6vB0Bp9P5wyIpnlfsF90XoXtXEqkHRlEZC8NdF2xEXHysoOZACEhAcv62Ei4nMxAZD'
    );

    static $face_traffic = [
        'levan_token' => 'EAAJTaWjIMUkBAFkTRJwvz2An4CMfLIpd2FX0YgdzHexZBIXH2C3ZCXEqvfLosMvRkJKtoCeliLfDDSaip9B7DeXMZBeZB9n6Kh1lNw9g40jMd1aUpFaDLupo6c9D3YZCPx2yCy004JQOnIpwhe71gYZC8XTQ4ZBorQZD',
        'huyentrang_token' => 'EAAJTaWjIMUkBACrRIxxawy8vcakKyZAitwDvvQQSuybcStckIgDJEQ9ZCJAZCcl19Ycoj1mES0JUjCQ2Cryxfq5h8xMocfafmRDgO3kswsJRlsiNrqGETFXUZCMivbm9pndcUfrZAg2aYErsG60SrxWA9Injvh9IZD',
        'mannhi_token' => 'EAAJTaWjIMUkBAMXS4jPADsu8Tfc8rzwpNmBFv7VZBuQkbom2X2Y5JvYZBGz821rUYlFHLn6xfE2mzf0XZBTna1pLDHRv4iP6ZADEf9D4LfLAyZAstbAZAzwfYjCkovdrD5pSB02A9AH2ZAzawbHRdko0yyqTAzMASoZD'
    ];

    static $google_config = [
        'key' => 'AIzaSyD6ENUAZfNMZNyiQZrSS9dI80XoGywPz6o',
        'client_id' => '763767273741-aove72fd06bocnk4upkljd6unv38o5bm.apps.googleusercontent.com',
        'client_secret' => 'mUVGFKCPsGBNh8tzVUvxT9mi'
    ];

//    static $google_config = [
//        'key' => 'AIzaSyDW0szgrecdX94Q5s1jAYhYE8LpdCFvjnc',
//        'client_id' => '47246308384-09dceuo2a8usraklmtl6mgbfi0q5ljp8.apps.googleusercontent.com',
//        'client_secret' => 'd3NNefkt3ETgbGXdap0hIKLv'
//    ];
//    static $google_config = [
//        'key' => 'AIzaSyBlTq9Ah3zwzATAT0C76Fq7wVz0aE6wazM',
//        'client_id' => '305277173466-i7u7cmv0a7gqco2rj86a9p99jbokp9lq.apps.googleusercontent.com',
//        'client_secret' => 'yuNS6kJUsU69NX7rPXRIrU4C'
//    ];

    static $cate_videos = [26, 27, 28, 29, 30, 31, 32, 33, 34];

    public static function getSlug($string, $maxLength = 255, $separator = '-')
    {
        $arrCharFrom = array("ạ", "á", "à", "ả", "ã", "Ạ", "Á", "À", "Ả", "Ã", "â", "ậ", "ấ", "ầ", "ẩ", "ẫ", "Â", "Ậ", "Ấ", "Ầ", "Ẩ", "Ẫ", "ă", "ặ", "ắ", "ằ", "ẳ", "ẵ", "ẫ", "Ă", "Ắ", "Ằ", "Ẳ", "Ẵ", "Ặ", "Ẵ", "ê", "ẹ", "é", "è", "ẻ", "ẽ", "Ê", "Ẹ", "É", "È", "Ẻ", "Ẽ", "ế", "ề", "ể", "ễ", "ệ", "Ế", "Ề", "Ể", "Ễ", "Ệ", "ọ", "ộ", "ổ", "ỗ", "ố", "ồ", "Ọ", "Ộ", "Ổ", "Ỗ", "Ố", "Ồ", "Ô", "ô", "ó", "ò", "ỏ", "õ", "Ó", "Ò", "Ỏ", "Õ", "ơ", "ợ", "ớ", "ờ", "ở", "ỡ", "Ơ", "Ợ", "Ớ", "Ờ", "Ở", "Ỡ", "ụ", "ư", "ứ", "ừ", "ử", "ữ", "ự", "Ụ", "Ư", "Ứ", "Ừ", "Ử", "Ữ", "Ự", "ú", "ù", "ủ", "ũ", "Ú", "Ù", "Ủ", "Ũ", "ị", "í", "ì", "ỉ", "ĩ", "Ị", "Í", "Ì", "Ỉ", "Ĩ", "ỵ", "ý", "ỳ", "ỷ", "ỹ", "Ỵ", "Ý", "Ỳ", "Ỷ", "Ỹ", "đ", "Đ");
        $arrCharEnd = array("a", "a", "a", "a", "a", "A", "A", "A", "A", "A", "a", "a", "a", "a", "a", "a", "A", "A", "A", "A", "A", "A", "a", "a", "a", "a", "a", "a", "a", "A", "A", "A", "A", "A", "A", "A", "e", "e", "e", "e", "e", "e", "E", "E", "E", "E", "E", "E", "e", "e", "e", "e", "e", "E", "E", "E", "E", "E", "o", "o", "o", "o", "o", "o", "O", "O", "O", "O", "O", "O", "O", "o", "o", "o", "o", "o", "O", "O", "O", "O", "o", "o", "o", "o", "o", "o", "O", "O", "O'", "O", "O", "O", "u", "u", "u", "u", "u", "u", "u", "U", "U", "U", "U", "U", "U", "U", "u", "u", "u", "u", "U", "U", "U", "U", "i", "i", "i", "i", "i", "I", "I", "I", "I", "I", "y", "y", "y", "y", "y", "Y", "Y", "Y", "Y", "Y", "d", "D");
        $arrCharnonAllowed = array("©", "®", "Æ", "Ç", "Å", "Ç", "๏", "๏̯͡๏", "Î", "Ø", "Û", "Þ", "ß", "å", "", "¼", "æ", "ð", "ñ", "ø", "û", "!", "ñ", "[", "\"", "$", "%", "'", "(", ")", "♥", "(", "+", "*", "/", "\\", ",", ":", "¯", "", "+", ";", "<", ">", "=", "?", "@", "`", "¶", "[", "]", "^", "~", "`", "|", "", "_", "?", "*", "{", "}", "€", "�", "ƒ", "„", "", "…", "‚", "†", "‡", "ˆ", "‰", "ø", "´", "Š", "‹", "Œ", "�", "Ž", "�", "ॐ", "۩", "�", "‘", "’", "“", "”", "•", "۞", "๏", "—", "˜", "™", "š", "›", "œ", "�", "ž", "Ÿ", "¡", "¢", "£", "¤", "¥", "¦", "§", "¨", "«", "¬", "¯", "°", "±", "²", "³", "´", "µ", "¶", "¸", "¹", "º", "»", "¼", "¾", "¿", "Å", "Æ", "Ç", "?", "×", "Ø", "Û", "Þ", "ß", "å", "æ", "ç", "ï", "ð", "ñ", "÷", "ø", "ÿ", "þ", "û", "½", "☺", "☻", "♥", "♦", "♣", "♠", "•", "░", "◘", "○", "◙", "♂", "♀", "♪", "♫", "☼", "►", "◄", "↕", "‼", "¶", "§", "▬", "↨", "↑", "↓", "←", "←", "↔", "▲", "▼", "⌂", "¢", "→", "¥", "ƒ", "ª", "º", "▒", "▓", "│", "┤", "╡", "╢", "╖", "╕", "╣", "║", "╗", "╝", "╜", "╛", "┐", "└", "┴", "┬", "├", "─", "┼", "╞", "╟", "╚", "╔", "╩", "╦", "╠", "═", "╬", "╧", "╨", "╤", "╥", "╙", "╘", "╒", "╓", "╫", "╪", "┘", "┌", "█", "▄", "▌", "▐", "▀", "α", "Γ", "π", "Σ", "σ", "µ", "τ", "Φ", "Θ", "Ω", "δ", "∞", "φ", "ε", "∩", "≡", "±", "≥", "≤", "⌠", "⌡", "≈", "°", "∙", "·", "√", "ⁿ", "²", "■", "¾", "×", "Ø", "Þ", "ð", "ღ", "ஐ", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "•", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "•", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "Ƹ", 'Ӝ', 'Ʒ', "★", "●", "♡", "ஜ", "ܨ");
        $string = str_replace($arrCharFrom, $arrCharEnd, $string);
        $finalString = str_replace($arrCharnonAllowed, '', $string);
        $url = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $finalString);
        $url = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $url);
        $url = trim(substr(strtolower($url), 0, $maxLength));
        $url = preg_replace("/[\/_|+ -]+/", $separator, $url);
        return $url;
    }

    //permission
    public static function getRole($roleID)
    {
        $arrRole = self::listRole();
        if (isset($arrRole[$roleID])) {
            return $arrRole[$roleID];
        }

        return false;
    }

    public static function getGender()
    {
        return array(
            self::MR => 'Nam',
            self::MS => 'Nữ',
            self::MRS => 'Không xác định',
        );
    }

    public static function listRole()
    {
        return array(
            self::ADMINISTRATOR => 'Administrator',
            self::MODERATOR => 'Moderator',
            self::EDITOR => 'Editor',
            self::SEO => 'SEO',
            self::SUPPORTER => 'Supporter',
            self::MEMBER => 'Người dùng thường',
        );
    }

    // Returns colored string
    public static function getColoredString($string, $foreground_color = null, $background_color = null)
    {
        $colored_string = "";

        // Check if given foreground color found
        if (isset(self::$foreground_colors[$foreground_color])) {
            $colored_string .= "\033[" . self::$foreground_colors[$foreground_color] . "m";
        }
        // Check if given background color found
        if (isset(self::$background_colors[$background_color])) {
            $colored_string .= "\033[" . self::$background_colors[$background_color] . "m";
        }

        // Add string and end coloring
        $colored_string .= trim($string) . "\033[0m\n\n";

        return $colored_string;
    }

    // Returns all foreground color names
    public static function getForegroundColors()
    {
        return array_keys(self::$foreground_colors);
    }

    // Returns all background color names
    public static function getBackgroundColors()
    {
        return array_keys(self::$background_colors);
    }

    public static function randomDigits($length = 6)
    {
        $characters = '0123456789';
        $string = '';
        $strLeng = strlen($characters) - 1;
        for ($p = 0; $p < $length - 1; $p++) {
            $string .= $characters [mt_rand(0, $strLeng)];
        }
        return rand(1, 9) . $string;
    }

    /**
     * Upload images
     *
     * @param array $arrFile array images to upload
     * @param string $strControllerName controller name
     * @return array
     */
    public static function ImageUpload($arrFile = array(), $strControllerName = '')
    {
        $arrResult = array();
        if (empty($arrFile)) {
            return $arrResult;
        }
        $tmp = 1;
        $arrResult = array();
        $strTime = date('Y') . '/' . date('m') . '/' . date('d') . '/';
        $strFolderType = UPLOAD_PATH . $strControllerName;
        if (!is_dir($strFolderType)) {
            mkdir($strFolderType, 0777, true);
        }

        if (!is_writable($strFolderType) || !is_readable($strFolderType)) {
            chmod($strFolderType, 0777);
        }
        $strFolderByDate = $strFolderType . '/' . $strTime;
        $strFolderThumb = $strFolderByDate . 'thumbs/';
        if (!is_dir($strFolderByDate)) {
            mkdir($strFolderByDate, 0777, true);
            chmod($strFolderByDate, 0777);
            mkdir($strFolderThumb, 0777, true);
            chmod($strFolderThumb, 0777);
        }

        $arrFile = $arrFile[0] ? $arrFile : array($arrFile);
        $adapter = new \Zend\File\Transfer\Adapter\Http();
        $is_image = new \Zend\Validator\File\IsImage();
        $size = new \Zend\Validator\File\Size(array('max' => 2097152)); //2MB
        $total = new \Zend\Validator\File\Count(array('min' => 0, 'max' => 6));
        foreach ($arrFile as $k => $file) {
            $adapter->setValidators(array($size, $is_image, $total), $file['name']);
            $strExtension = pathinfo($strFolderByDate . $file['name'], PATHINFO_EXTENSION);
            if ($adapter->isValid($file['name'])) {
                $adapter->setDestination($strFolderByDate);
                $newFileName = microtime(true) . '.' . $strExtension;
                $adapter->addFilter('File\Rename', array(
                    'target' => $strFolderByDate . $newFileName,
                    'overwrite' => true,
                ));
                if ($adapter->receive($file['name'])) {
                    $arrSourceImage = UPLOAD_URL . $strControllerName . '/' . $strTime . $newFileName;
                }

                $arrThumb = self::getThumbSize($strControllerName);
                $serviceImage = new \Intervention\Image\ImageManager();

                foreach ($arrThumb as $thumbSize) {
                    list($width, $height) = explode('x', $thumbSize);
                    $thumbFileDir = $strFolderThumb . $thumbSize . '/';

                    if (!is_dir($thumbFileDir)) {
                        mkdir($thumbFileDir, 0777, true);
                        chmod($thumbFileDir, 0777);
                    }
                    $image = $serviceImage->make($strFolderByDate . $newFileName)->fit($width, $height);
                    $resultThumb = $image->save($thumbFileDir . $newFileName);

                    if ($resultThumb) {
                        $arrThumbImage[$thumbSize] = UPLOAD_URL . $strControllerName . '/' . $strTime . 'thumbs/' . $thumbSize . '/' . $newFileName;
                    }
                }
                array_push($arrResult, array('sourceImage' => $arrSourceImage, 'thumbImage' => $arrThumbImage));
            }
        }
        return $arrResult;
    }

    /**
     * Send Email to users
     * @param string|array $email email list
     * @param String $strTitle email title
     * @param String $strMessage email message
     * @return Boolean
     */
    public static function sendMail($email, $strTitle, $strMessage)
    {

        try {
            if (empty($email) || empty($strTitle) || empty($strMessage)) {
                $filename = WEB_ROOT . '/data/json_log.txt';
                $strError = " Ko co email, hoac ko co title, hoac ko co body \n";
                file_put_contents($filename, $strError, FILE_APPEND);
                return false;
            }

            //parse to array
            $arrEmail = (array)$email;

            $html = new \Zend\Mime\Part($strMessage);
            $html->type = \Zend\Mime\Mime::TYPE_HTML;
            $html->charset = 'utf-8';

            $body = new \Zend\Mime\Message();
            $body->setParts(array($html));

            $mail = new \Zend\Mail\Message();
            $mail->setSubject($strTitle)
                ->addFrom(self::EMAIL_SYS, self::SITE_AUTH)
                ->addReplyTo(self::EMAIL_SYS)
                ->setBody($body);

            $mail->addTo($arrEmail);

            if ($mail->isValid()) {

                $smtpOptions = new \Zend\Mail\Transport\SmtpOptions();
                $smtpOptions->setHost(self::HOST_MAIL)
                    ->setPort(465)
                    ->setConnectionClass('login')
                    ->setConnectionConfig(
                        array(
                            'username' => self::EMAIL_SYS,
                            'password' => self::EMAIL_SYS_PASSWORD,
                            'ssl' => 'ssl'
                        )
                    );
                $transport = new \Zend\Mail\Transport\Smtp($smtpOptions);
                $transport->send($mail);
                return true;
            }
        } catch (\Exception $exc) {
            echo '<pre>';
            print_r($exc->getMessage());
            echo '</pre>';
            die();
        }
    }

    public static function resizeImages($strControllerName, $pathFile, $fileName)
    {

        $arrThumb = self::getThumbSize($strControllerName);
        $serviceImage = new \Intervention\Image\ImageManager();

        foreach ($arrThumb as $thumbSize) {
            list($width, $height) = explode('x', $thumbSize);
            $thumbFileDir = UPLOAD_PATH . $strControllerName . '/' . $thumbSize . '/';

            if (!is_dir($thumbFileDir)) {
                mkdir($thumbFileDir, 0777, true);
                chmod($thumbFileDir, 0777);
            }
            try {
                $image = $serviceImage->make($pathFile)->fit($width, $height);
                $resultThumb = $image->save($thumbFileDir . $fileName);
            } catch (\Exception $exc) {
                echo $exc->getMessage();
                continue;
            }

            if ($resultThumb) {
                $arrThumbImage[$thumbSize] = UPLOAD_URL . $strControllerName . '/' . $thumbSize . '/' . $fileName;
            }
        }
        $arrResult = [
            'sourceImage' => UPLOAD_URL . $strControllerName . '/' . $fileName,
            'thumbImage' => $arrThumbImage
        ];
        return $arrResult;
    }

    /**
     * Get thumb image size for user, topic, category, banner controller
     *
     * @param String $strControllerName
     * @return Array
     * @throws \Exception
     */
    public static function getThumbSize($strControllerName = '')
    {
        if ($strControllerName) {
            switch ($strControllerName) {
                case 'content':
                    return array('91x55', '172x104', '371x177', '507x270', '770x318');
                case 'user':
                    return array('150x150', '30x30', '120x120');
                default:
                    return array();
            }
        } else {
            throw new \Zend\Http\Exception('Controller name cannot be empty');
        }
    }

    /**
     * Get Elasticsearch Config
     * @return \Elastica\Client
     */
    public static function getSearchConfig()
    {
        $port = 9200;
        $client = new \Elastica\Client(
            array('servers' => array(
                array('host' => 'localhost', 'port' => $port),
            )
            ));
        return $client;
    }

    /**
     * Get Client config
     * @return \GearmanClient
     */
    public static function getClientConfig()
    {
        $client = new \GearmanClient();
        $client->addServer('127.0.0.1', 4730);
        return $client;
    }

    /**
     * Get worker config
     * @return \GearmanWorker
     */
    public static function getWorkerConfig()
    {
        $worker = new \GearmanWorker();
        $worker->addServer('127.0.0.1', 4730);
        return $worker;
    }

    /**
     * Get Redis config for pageview, comment, notification, banned user ... etc
     * @param String $strType
     */
    public static function getRedisConfig()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379, 15);

        return $redis;
    }

    /**
     * Split long String
     *
     * @param string $strText
     * @param int $totalSplit
     * @return array
     */
    public static function splitWords($strText, $totalSplit = 2)
    {
        $arrWords = explode(' ', $strText);
        $result = array();
        $icnt = count($arrWords) - ($totalSplit - 1);
        for ($i = 0; $i < $icnt; $i++) {
            $str = '';
            for ($o = 0; $o < $totalSplit; $o++) {
                $str .= $arrWords[$i + $o] . ' ';
            }
            array_push($result, trim($str));
        }
        return $result;
    }

    public static function toPrettyTime($seconds)
    {
        $day = 24 * 60 * 60;
        $hour = 60 * 60;
        $minute = 60;

        $d = floor($seconds / $day);
        $h = floor(($seconds % $day) / $hour);
        $m = floor(($seconds % ($day * $hour)) / $minute);

        if ($d > 0) {
            return $d . ' ngày';
        } elseif ($h > 0) {
            return $h . ' giờ';
        } else {
            return $m . ' phút';
        }
    }

    public static function formatPrice($price)
    {
        list($priceEven, $priceOdd) = explode('.', $price);
        if (empty($priceOdd)) {
            return number_format($price);
        }
        return number_format($priceEven) . '.' . $priceOdd;
    }

    public static function dateToTimestamp($day, $month, $year, $hour = 0, $minute = 0, $second = 0)
    {
        return mktime($hour, $minute, $second, $month, $day, $year);
    }

    public static function dateRange($first, $last, $step = '+1 day', $format = 'Y/m/d')
    {
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while ($current <= $last) {
            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }
        return $dates;
    }

    public static function generateCaptcha($maxWordLength = 4, $width = 100, $height = 70)
    {
        if (!is_writable(CAPTCHA_PATH) || !is_dir(CAPTCHA_PATH)) {
            throw new \Exception(CAPTCHA_PATH . ' does not exist or cannot writable');
        }
        $captcha = new \Zend\Captcha\Image();
        $captcha->setWordlen($maxWordLength);
        $captcha->setWidth($width)->setHeight($height);
        $captcha->setFont(FRONTEND_FONT_PATH . 'arial.ttf');
        $captcha->setImgDir(CAPTCHA_PATH)->setImgUrl(CAPTCHA_URL);
        $captcha->setDotNoiseLevel(0)->setLineNoiseLevel(0);
        $captcha->setFontSize(15);
        $captcha->setExpiration(60 * 15); //session captcha expire in 15ms

        $captcha->generate();
        $id = $captcha->getId();

        $ses = new \Zend\Session\Container('Zend_Form_Captcha_' . $id);
        $captchaIterator = $ses->getIterator();
        return array(
            'id' => $id,
            'word' => $captchaIterator['word'],
            'url' => $captcha->getImgUrl() . $id . '.png'
        );
    }

    public static function escapeJsonString($value)
    { # list from www.json.org: (\b backspace, \f formfeed)
        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c", "'");
        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b", "\\'");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }

    public static function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function crawler($strURL = '', $strCookiePath = '', $arrHeader = array(), $arrData = array())
    {
        try {
            if (empty($strURL)) {
                return false;
                throw new \Zend\Http\Exception('URL cannot be empty');
            }
            $crawler = curl_init($strURL);

            if ($arrHeader) {
                curl_setopt($crawler, CURLOPT_HTTPHEADER, $arrHeader);
            }

            if ($strCookiePath) {
                curl_setopt($crawler, CURLOPT_COOKIEJAR, $strCookiePath);
                curl_setopt($crawler, CURLOPT_COOKIEFILE, $strCookiePath);
                curl_setopt($crawler, CURLOPT_COOKIE, session_name() . '=' . session_id());
            }
            curl_setopt($crawler, CURLOPT_COOKIESESSION, TRUE);
            curl_setopt($crawler, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($crawler, CURLOPT_DNS_CACHE_TIMEOUT, 0);
            curl_setopt($crawler, CURLOPT_MAXREDIRS, 5);
            curl_setopt($crawler, CURLOPT_FRESH_CONNECT, TRUE);
            curl_setopt($crawler, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36');
            curl_setopt($crawler, CURLOPT_RETURNTRANSFER, TRUE);
            $arrData ? curl_setopt($crawler, CURLOPT_POSTFIELDS, $arrData) : '';
            curl_setopt($crawler, CURLOPT_SSL_VERIFYPEER, FALSE);

            $data = curl_exec($crawler);
            curl_close($crawler);
            return $data;
        } catch (\Zend\Http\Exception $exc) {
            die('<b>' . $exc->getMessage() . '</b>' . '<p></p>' . $exc->getTraceAsString());
        }
    }

    public static function sortArray($records, $field, $reverse = false)
    {
        $hash = array();

        foreach ($records as $record) {
            $hash[$record[$field]] = $record;
        }

        ($reverse) ? krsort($hash) : ksort($hash);

        $records = array();

        foreach ($hash as $record) {
            $records[] = $record;
        }

        return $records;
    }

    /**
     * Detect time of the day
     * @return string
     */
    public static function detectTimeOfDay()
    {
        $time = date("H");
        if ($time < "12") {
            echo "buổi sáng vui vẻ và hạnh phúc.";
        } else
            if ($time >= "12" && $time < "18") {
                echo "buổi chiều vui vẻ và hạnh phúc.";
            } else
                if ($time >= "18" && $time < "22") {
                    echo "buổi tối vui vẻ và hạnh phúc.";
                } else
                    if ($time >= "22") {
                        echo "buổi tối ngon giấc.";
                    }
    }

    /**
     * Set FlashMessenger
     * @param string $namespace
     * @param string $message
     */
    public static function setFlashMessenger($namespace = '', $message = '')
    {
        $messenger = new \Zend\Mvc\Controller\Plugin\FlashMessenger();
        $messenger->setNamespace($namespace)->addMessage($message);
    }

    public static function formatDateString($date)
    {
        $current = time() - $date;

        if ($current < 60) {
            return $current . " second ago";
        } else {
            $minute = round($current / 60);
            if ($minute < 60) {
                return $minute . " minutes ago";
            } else {
                $house = round($minute / 60);
                if ($house < 24) {
                    return $house . " hour ago";
                } else {
                    $day = round($house / 24);
                    if ($day < 2) {
                        return $day . " days ago " . date("H:i", $date);
                    } else {
                        return date("d/m/Y H:i", $date);
                    }
                }
            }
        }
    }

    public static function clean($str = "")
    {
        $vowels = array('/', '\\', ':', ';', '!', '#', '$', '%', '^', '*', '(', ')', '=', '{', '}', '[', ']', '"', "'", '<', '>', '?', '~', '`', '&');
        return str_replace($vowels, "", $str);
    }

    public static function cleanArray($array = "")
    {
        $arrReturn = [];
        foreach ($array as $key => $value) {
            $arrReturn[$key] = self::clean($value);
        }
        return $arrReturn;
    }

    public static function createLogs($arrParamsRoute, $arrParams, $intId)
    {
        $arrData = array(
            'user_id' => UID,
            'module' => $arrParamsRoute['module'],
            'controller' => $arrParamsRoute['__CONTROLLER__'],
            'action' => $arrParamsRoute['action'],
            'created_date' => time(),
            'log_content' => json_encode($arrParams),
            'table_id' => $intId
        );

        return $arrData;
    }

    public static function formatDateTime($time)
    {
        $temp = date('d/m/Y/H/i/s', $time);
        list($day, $month, $year, $hour, $min, $second) = explode('/', $temp);
        $strReturn = 'Ngày ' . $day . ' tháng ' . $month . ' năm ' . $year . ' - ' . $hour . ' giờ ' . $min . ' phút ' . $second . ' giây';
        return $strReturn;
    }

    /**
     * get_meta_tags UTF-8 content
     * @static
     * @param $url
     * @return string
     */
    public static function writeLog($fileName = '', $arrParam = array())
    {
        date_default_timezone_set('Asia/Saigon');

        $log = new \My\Logging();

        $log->lfile(LOG_FOLDER . '/Share_Log_' . $fileName);

        $arrParam['Time'] = date('H:i:s');

        $log->lwrite(json_encode($arrParam), 'Data', true);

        $log->lclose();

    }

    public static function crawlerImage($url, $name, $size, $controller = 'content')
    {
        if (!$url || !$name || !$size) {
            return false;
        }
        $extension = end(explode('.', end(explode('/', $url))));
        $name = self::getSlug($name) . '.' . $extension;

        $strFolderType = UPLOAD_PATH . $controller;

        //create folder
        if (!is_dir($strFolderType)) {
            mkdir($strFolderType, 0777, true);
        }

        //set permission
        if (!is_writable($strFolderType) || !is_readable($strFolderType)) {
            chmod($strFolderType, 0777);
        }

        $strTime = date('Y') . '/' . date('m') . '/' . date('d') . '/';
        $strFolderByDate = $strFolderType . '/' . $strTime;

        if (!is_dir($strFolderByDate)) {
            mkdir($strFolderByDate, 0777, true);
            chmod($strFolderByDate, 0777);
        }

        $folderPath = $strFolderByDate . $size . '/';

        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
            chmod($folderPath, 0777);
        }

        file_put_contents($folderPath . $name, General::crawler($url));

        if (!file_exists($folderPath . $name)) {
            return false;
        }

        return '/' . $controller . '/' . $strTime . $size . '/' . $name;
    }

    public static function formatDurationLength($youtube_time)
    {
        preg_match_all('/(\d+)/', $youtube_time, $parts);

        // Put in zeros if we have less than 3 numbers.
        if (count($parts[0]) == 1) {
            array_unshift($parts[0], "0", "0");
        } elseif (count($parts[0]) == 2) {
            array_unshift($parts[0], "0");
        }

        $sec_init = $parts[0][2];
        $seconds = $sec_init % 60;
        $seconds_overflow = floor($sec_init / 60);

        $min_init = $parts[0][1] + $seconds_overflow;
        $minutes = ($min_init) % 60;
        $minutes_overflow = floor(($min_init) / 60);

        $hours = $parts[0][0] + $minutes_overflow;

        if ($hours != 0)
            return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
        else
            return str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
    }

    public static function bot_detected()
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
