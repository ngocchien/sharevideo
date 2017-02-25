<?php
namespace My;
/**
 * Class Adx_Utils
 */
class Utils
{

    /**
     * file_get_content UTF-8 content
     * @static
     * @param $url
     * @return string
     */
    public static function fileGetContents($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        return $result;
    }

    /**
     * Generate random string
     * @static
     * @param $length
     * @return string
     */
    public static function randString($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        $size = Utils::str_len($chars);
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        }

        return $str;
    }

    /**
     * @param $array
     * @param $newKey
     * @param $oldKey
     * @return mixed
     */
    function changeKeyName($array, $newKey, $oldKey)
    {
        foreach ($array as $key => $value) {
            if (is_array($value))
                $array[$key] = changekeyname($value, $newKey, $oldKey);
            else {
                $array[$newKey] = $array[$oldKey];
            }
        }
        unset($array[$oldKey]);
        return $array;
    }

    /**
     * Format number
     * @param float $number
     * @return float
     */
    public static function numberFormat($number)
    {
        return $number > 0 ? number_format($number, 0, '.', ',') : 0;
    }

    /**
     * @param $number
     * @return int|mixed
     */
    public static function numberFormatRevenue($number)
    {
        return $number > 0 ? preg_replace("/\.?0*$/", '', number_format($number, 5, '.', ',')) : 0;
    }

    /**
     * HTML entities
     * @param string $string
     * @return string
     */
    public static function htmlEntities($string)
    {
        return str_replace(array("<", ">", '"', '\''), array("&lt;", "&gt;", "&quot;", "&#039;"), $string);
    }

    /**
     * HTML entities decode
     * @param string $string
     * @return string
     */
    public static function htmlEntitiesDecode($string)
    {
        return str_replace(array("&amp;", "&lt;", "&gt;", "&quot;", "&#039;"), array('&', "<", ">", '"', '\''), $string);
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

        $log->lfile(LOG_FOLDER . '/ADX_' . $fileName);

        $arrParam['Time'] = date('H:i:s');

        $log->lwrite(json_encode($arrParam), 'Data', true);

        $log->lclose();

    }

    /**
     * get_meta_tags UTF-8 content
     * @static
     * @param $url
     * @return string
     */
    public static function autoReconnection($model, $function, $param, $fileName = '')
    {
        $result = array();
        $message = array();
        $i = 1;
        $parameter = is_array($param) ? json_encode($param[0]) : $param;

        do {
            $error = 1;
            try {
                $result = call_user_func_array(array($model, $function), $param);
            } catch (Adx_Exception $e) {
                $error = 0;
                if ($i == 4) {
                    $message = array('function' => $function, 'parameter' => $parameter, 'error' => $e->getMessage()); //" Connection DB unsuccessful with function: ". $function . " parameter: " . $parameter . " error: ". $e->getMessage();
                    break;
                }
            }

            if (($result == null || $result) && $error == 1) {
                $message = array('function' => $function, 'parameter' => $parameter, 'error' => ''); //, 'result' => $result" Connection DB success with function: ". $function . " parameter: " . $parameter;
                $i = 5;
            } else {
                //Close all DB connections
                Adx_Db::closeAllConnections();

                sleep(3);

                echo 'auto mysql reconnection: ' . $i . "\n";
                $i++;
            }

        } while ($i < 5);

        if ($error == 0 && !empty($fileName)) {
            Adx_Utils::writeLog($fileName, $message);
        }

        if (isset($result['rows'])) {
            return array('message' => $message, 'error' => $error, 'rows' => $result['rows'], 'type' => 1);
        }

        return array('message' => $message, 'error' => $error, 'rows' => $result, 'type' => 2);
    }

    /**
     * get_meta_tags UTF-8 content
     * @static
     * @param $url
     * @return string
     */
    public static function autoReconnectionProcess($model, $function, $param, $fileName = '', $debug = true)
    {
        $result = array();
        $message = array();
        $i = 1;
        $parameter = is_array($param) ? json_encode($param) : $param;

        $allInstances = Adx_Db::getAllInstances();

        do {
            $error = 1;

            try {
                $result = call_user_func_array(array($model, $function), array($param));
            } catch (Exception $e) {
                $error = 0;

                if ($i == 4) {
                    $message = array(
                        'model' => $model,
                        'function' => $function,
                        'parameter' => $parameter,
                        'error' => $e->getMessage(),
                        'instancesFirst' => $allInstances,
                        'instancesLast' => Adx_Db::getAllInstances()
                    );
                    break;
                }
            }

            if ($error == 1) {
                $message = array(
                    'model' => $model,
                    'function' => $function,
                    'parameter' => $parameter,
                    'data' => isset($result['rows']) ? !empty($result['rows']) ? 'YES' : 'NO' : !empty($result) ? 'YES' : 'NO',
                    'error' => '',
                    'instancesFirst' => $allInstances,
                    'instancesLast' => Adx_Db::getAllInstances()
                );
                break;
            } else {
                //Close all DB connections
                Adx_Db::closeAllConnections();

                sleep(2);

                if ($debug) {
                    echo "Auto Mysql Reconnection " . $i . " Model:" . $model . " Function:" . $function . " Params:" . $parameter . "\n";
                }

                $i++;
            }

        } while ($i < 5);

        if ($error == 0 && !empty($fileName)) {
            Adx_Utils::writeLog($fileName, $message);
        }

        if (isset($result['rows'])) {
            return array('message' => $message, 'error' => $error, 'rows' => $result['rows'], 'type' => 1);
        }

        return array('message' => $message, 'error' => $error, 'rows' => $result, 'type' => 2);
    }

    /**
     * get_meta_tags UTF-8 content
     * @static
     * @param $url
     * @return string
     */
    public static function autoReconnectionRedis($model, $function, $param, $fileName = '')
    {
        $result = array();
        $message = array();
        $i = 1;
        $parameter = is_array($param) ? json_encode($param[0]) : $param;
        $instance = $param[0];

        do {
            $error = 1;
            $redis = 0;
            try {
                $result = call_user_func_array(array($model, $function), $param);
                $redis = $result->ping();
            } catch (Exception $e) {
                $error = 0;
                if ($i == 4) {
                    $message = array('function' => $function, 'parameter' => $parameter, 'error' => $e->getMessage());
                    break;
                }
            }
            if ($redis && $error == 1) {
                $message = array('function' => $function, 'parameter' => $parameter, 'error' => '');
                $i = 5;
            } else {
                //Close instance Redis connections
                Adx_Nosql_Redis::closeConnection($instance);

                sleep(3);

                echo 'Auto reconnection redis: ' . $i . "\n";
                $i++;
            }

        } while ($i < 5);

        if ($error == 0 && !empty($fileName)) {
            Adx_Utils::writeLog($fileName, $message);
        }

        return array('message' => $message, 'error' => $error, 'obj' => $result);
    }

    /**
     * get_meta_tags UTF-8 content
     * @static
     * @param $url
     * @return string
     */
    public static function autoReconnectionFunctionRedis($instance = 'delivery', $function = '', $params = array(), $fileName = '', $dbNumber = null)
    {

        $message = array();
        $result = array();
        $i = 1;

        $result = Adx_Utils::autoReconnectionRedis('Adx_Nosql_Redis', 'getInstance', array($instance));

        if (($result['error'] == 0 || empty($result['obj'])) && !empty($fileName)) {
            Adx_Utils::writeLog($fileName, $message);

            return $result;
        }

        $redis = $result['obj'];

        do {
            $error = 1;

            try {
                if (empty($dbNumber)) {
                    call_user_func_array(array($redis, 'SELECT'), array(0));
                } else {
                    call_user_func_array(array($redis, 'SELECT'), array($dbNumber));
                }

                $result = call_user_func_array(array($redis, $function), $params);

            } catch (Exception $e) {
                $error = 0;

                if ($i == 4) {
                    $message = array('function' => $function, 'parameter' => $params, 'rows' => $result, 'error' => 0);
                    break;
                }
            }

            if ($error == 1) {
                $message = array('function' => $function, 'parameter' => $params, 'rows' => $result, 'error' => 1);
                $i = 5;
            } else {
                //Close instance Redis connections
                Adx_Nosql_Redis::closeConnection($instance);

                sleep(3);

                echo 'Auto function reconnection: ' . $i . "\n";
                $i++;
            }

        } while ($i < 5);

        return $message;
    }

    /**
     * run Job
     * @static
     * @param strings
     * @return string
     */
    public static function runJob($class = '', $function = '', $priority = 'doTask', $workload = '', $param = array())
    {
        //add param job
        $param['job'] = array(
            'class' => $class,
            'function' => $function,
            'workload' => $workload
        );
        //job Param
        $jobParams = array();
        $jobParams['class'] = $class;
        $jobParams['function'] = $function;
        $jobParams['args'] = array_merge(array(
            'site_url_global' => (defined('SITE_URL') ? SITE_URL : ''),
            'static_url_global' => (defined('STATIC_URL') ? STATIC_URL : ''),
            'upload_url_global' => (defined('UPLOAD_URL') ? UPLOAD_URL : '')
        ), $param);

        //Create job client
        $jobClient = Adx_Job_Client::getInstance();

        //Register job
        try {
            $result = call_user_func_array(array($jobClient, $priority), array(Adx_Job_Client::getFunction($workload), $jobParams));
        } catch (Adx_Exception $e) {
            return array('parameter' => json_encode($jobParams), 'message' => $e->getMessage(), 'error' => 0);
        }

        return array('parameter' => json_encode($jobParams), 'message' => 'success', 'error' => 1, 'result' => $result);
    }

    /**
     * error messenger
     * @static
     * @param $url
     * @return string
     */
    public static function errorMessenger($class = '', $function = '', $line = 0, $result = null, $fileName = 'Messenger')
    {
        //Write Log
        Adx_Utils::writeLog(
            $fileName,
            array(
                'class' => $class,
                'function' => $function,
                'line' => $line,
                'result' => $result
            )
        );
        //Echo
        if (!empty($result['rows'])) {
            $str = " --> Messenger: ";
        } else {
            $str = " --> Warning: ";
        }
        echo "\n";
        echo "$str";
        if (is_array($result)) {
            echo json_encode(array(
                'Class' => $class,
                'Function' => $function,
                'Line' => $line,
                'Param' => array(
                    'function' => isset($result['message']['function']) ? $result['message']['function'] : 'Function Empty',
                    'parameter' => isset($result['message']['parameter']) ? $result['message']['parameter'] : 'Param Empty',
                    'error' => isset($result['error']) ? $result['error'] : 'Error Unknown',
                    'rows' => isset($result['rows']) ? $result['rows'] : 'Data Empty',
                )
            ));
        } else {
            echo json_encode($result);
        }
        echo "\n";
        echo " |";
    }

    /**
     * get_meta_tags UTF-8 content
     * @static
     * @param $url
     * @return string
     */
    public static function getMetaTags($url, $tagName = null)
    {
        $tags = @get_meta_tags($url);

        return empty($tagName) ? $tags : @$tags[$tagName];
    }

    /**
     * Format Date
     * @static
     * @param $url
     * @return string
     */
    public static function formatDate($dateTime, $characterExp = "/", $characterImp = "-", $index = 0)
    {
        $date = explode($characterExp, $dateTime);
        if ($index > 0) {
            return $date[$index - 1];
        }
        return implode($characterImp, array_reverse($date));
    }

    public static function swapDate($dateTime, $characterExp = "-", $characterImp = "-")
    {
        $date = explode($characterExp, $dateTime);
        return implode($characterImp, array_reverse($date));
    }

    /**
     * @param int $isDate
     * @param int $isFromDate
     * @param int $numDays
     * @param int $showTime
     * @param string $unit
     * @param string $format
     * @return string
     */
    public static function getDate($isDate = 0, $isFromDate = 1, $numDays = 0, $showTime = 1, $unit = 'days', $format = 'Y-m-d')
    {
        if (is_null($isDate)) {
            return null;
        }

        $now = date($format);
        if ($numDays == 0) {
            return (!empty($isDate) ? $isDate : $now) . ($showTime == 1 ? $isFromDate == 1 ? ' 00:00:00' : ' 23:59:59' : null);
        }
        $date = new DateTime(!empty($isDate) ? $isDate : $now);
        $date->add(DateInterval::createFromDateString($numDays . ' ' . $unit));
        return $date->format($format) . ($showTime == 1 ? $isFromDate == 1 ? ' 00:00:00' : ' 23:59:59' : null);
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param string $step
     * @param string $format
     * @return array
     */
    public static function getRangeDate($fromDate, $toDate, $step = '+1 day', $format = 'Y-m-d')
    {
        $dates = array();

        $current = strtotime($fromDate);
        $to = strtotime($toDate);

        while ($current <= $to) {
            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }

    /**
     * @param $budgetOrder
     * @param $inventoryRevenue
     * @return float|int
     */
    public static function getBudgetActual($budgetOrder, $inventoryRevenue)
    {
        if (intval($budgetOrder) == 0) {
            return 0;
        }
        $inventory = floor($budgetOrder / $inventoryRevenue);
        $budgetActual = $inventory * $inventoryRevenue;
        return ($budgetOrder == $budgetActual) ? $budgetOrder : $budgetActual;
    }

    /**
     * get_meta_tags UTF-8 content
     * @static
     * @param $url
     * @return string
     */
    public static function checkEmail($mail_address)
    {
        $pattern = "/^[\w-]+(\.[\w-]+)*@";

        $pattern .= "([0-9a-z][0-9a-z-]*[0-9a-z]\.)+([a-z]{2,4})$/i";

        if (preg_match($pattern, $mail_address)) {
            $parts = explode("@", $mail_address);

            if (checkdnsrr($parts[1], "MX")) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param $string
     * @param $key
     * @return string
     */
    public static function encode($string, $key)
    {
        $j = 0;
        $hash = '';
        $key = sha1($key);
        $strLen = Adx_Utils::str_len($string);
        $keyLen = Adx_Utils::str_len($key);
        for ($i = 0; $i < $strLen; $i++) {
            $ordStr = ord(substr($string, $i, 1));
            if ($j == $keyLen) {
                $j = 0;
            }
            $ordKey = ord(substr($key, $j, 1));
            $j++;
            $hash .= strrev(base_convert(dechex($ordStr + $ordKey), 16, 36));
        }
        return $hash;
    }

    /**
     * @param $string
     * @param $key
     * @return string
     */
    public static function decode($string, $key)
    {
        $j = 0;
        $hash = '';
        $key = sha1($key);
        $strLen = Adx_Utils::str_len($string);
        $keyLen = Adx_Utils::str_len($key);
        for ($i = 0; $i < $strLen; $i += 2) {
            $ordStr = hexdec(base_convert(strrev(substr($string, $i, 2)), 36, 16));
            if ($j == $keyLen) {
                $j = 0;
            }
            $ordKey = ord(substr($key, $j, 1));
            $j++;
            $hash .= chr($ordStr - $ordKey);
        }
        return $hash;
    }

    /**
     * @param $number
     * @param int $precision
     * @param string $mode
     * @return float|int
     */
    public static function round($number, $precision = 0, $mode = 'PHP_ROUND_HALF_UP')
    {
        return $number > 0 ? round($number, $precision, $mode) : 0;
    }

    /**
     * @param $url
     */
    public static function clearUrl($url)
    {
        $domain = '';
        if (empty($url)) {
            return $domain;
        }

        $raw_url = parse_url($url);


        if (isset($raw_url['scheme'])) {
            $domain = $raw_url['host'];
        } else {
            $raw_url = explode('/', $raw_url['path']);
            $domain = $raw_url[0];
        }
        //$domain_only = str_replace ('www.','', $raw_url);

        return $domain;
    }

    /**
     * @param $campaignName
     * @param $data
     * @return string
     */
    public static function buildCampaignNameCopy($campaignName, $data)
    {
        $copyNumber = 1;
        if (empty($data)) {
            return array('campaignName' => 'Copy - ' . $campaignName, 'copyNumber' => $copyNumber);
        }

        $data = explode(',', trim($data));

        for ($i = 1; $i <= count($data); $i++) {
            if (!in_array($i, $data)) {
                break;
            }
        }

        array_push($data, $i);
        asort($data);

        $copyNumber = implode(',', $data);

        if ($i == 1) {
            return array('campaignName' => 'Copy - ' . $campaignName, 'copyNumber' => $copyNumber);
        }
        return array('campaignName' => 'Copy (' . $i . ') - ' . $campaignName, 'copyNumber' => $copyNumber);
    }

    /**
     * @param $campaignName
     * @param $parentCampaignShowNumber
     * @return array
     */
    public static function checkCampaignName($campaignName, $parentCampaignShowNumber)
    {
        $result = array('digit' => 0, 'copyNumber' => '');

        if (empty($campaignName) || empty($parentCampaignShowNumber)) {
            return $result;
        }

        preg_match('/(?P<name>\w+)(\s)[(](?P<digit>\d+)[)]/', trim($campaignName), $matches);

        if (empty($matches['digit'])) {
            $matches['digit'] = 1;
        }

        $data = explode(',', $parentCampaignShowNumber);

        if (empty($data)) {
            return $result;
        }

        $data = array_values(array_diff($data, array($matches['digit'])));
        asort($data);
        $copyNumber = implode(',', $data);

        return array('digit' => 1, 'copyNumber' => $copyNumber);
    }

    /**
     * @param $start
     * @param $end
     * @return int
     */
    public static function getDiffDays($start, $end)
    {
        $startDate = explode('/', $start);
        $startTimeStamp = strtotime($startDate[2] . '-' . $startDate[1] . '-' . $startDate[0]);
        $endDate = explode('/', $end);
        $endTimeStamp = strtotime($endDate[2] . '-' . $endDate[1] . '-' . $endDate[0]);

        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = intval($timeDiff / 86400) + 1; // 86400 seconds in one day

        return $numberDays;
    }

    /**
     * @return float
     */
    public static function startTimer()
    {
        return (float)array_sum(explode(' ', microtime()));
    }

    /**
     * @param $startTime
     * @return string
     */
    public static function endTimer($startTime)
    {
        $endTimer = (float)array_sum(explode(' ', microtime()));
        return (sprintf("%.4f", ($endTimer - $startTime)) . " seconds");
    }


    /**
     * @param $urlLocks
     * @param $urlCreative
     * @return bool
     */
    public static function checkLockDomain($urlLocks, $urlCreative)
    {
        if (empty($urlLocks)) {
            return false;
        }
        /*
        $parseUrl = parse_url(strtolower($urlCreative));

        $hostName = '';

        if (isset($parseUrl['host'])) {
            $hostName = $parseUrl['host'];
        } elseif (isset($parseUrl['path'])) {
            $hostName = trim(str_replace("www.", "", $parseUrl['path']));
        }

        if (empty($hostName)) {
            return false;
        }

        foreach ($urlLocks as $urlLock) {
            $path = strtolower($urlLock);
            $expUrlLock = explode("*", $path);
            if (count($expUrlLock) > 1) {
                //$host = count(explode(".", $expUrlLock[1])) > 2 ? substr($expUrlLock[1], 1) : $expUrlLock[1];
                $host = $expUrlLock[1];
                if (empty($host)) {
                    continue;
                }

                $pattern = '/' . trim($host) . '/';
            } else {
                $host = '';
                $parseUrl = parse_url($path);
                if (isset($parseUrl['host'])) {
                    $host = str_replace("www.", "", $parseUrl['host']);
                } elseif (isset($parseUrl['path'])) {
                    $expUrlLock = explode("/", $path);
                    $host = str_replace("www.", "", $expUrlLock[0]);
                }

                if (empty($host)) {
                    continue;
                }

                $pattern = '/^' . trim($host) . '/';
            }

            if (preg_match($pattern, $hostName)) {
                return true;
            }
        }
        */
        //Nếu text key có tồn tại trong url
        if (strpos($urlCreative, strtolower($urlLocks[0])) !== false) {
            return true;
        }

        return false;
    }

    /*
     * @param $date format YYYY-mm-dd
     */
    public static function convertDateToDisplay($date, $format = "d/m/Y")
    {
        if (empty($date)) {
            return '';
        }

        return date($format, strtotime($date));
    }

    /**
     * @param $params
     * @return array
     */
    public static function cropImage($params)
    {
        // in case copy creative must be replace upload url => upload path
        $need_crop = true;
        if (strpos($params['imageSource'], UPLOAD_URL) !== false) {
            $need_crop = false;
            $params['imageSource'] = str_replace(UPLOAD_URL, UPLOAD_PATH, $params['imageSource']);
        }

        if(isset($params['need_crop']) && $params['need_crop'] == false) {
            $need_crop = false;
        }

        $tokens = pathinfo($params['imageSource']);
        $filename = $tokens['filename'];

        if (Adx_Utils::str_len($filename) !== 32) {
            $filename = md5($filename . uniqid());
        }

        $basename = $filename . '.png';

        $thumb = new Imagick();
        $thumb->readImage($params["imageSource"]);
        if (isset($params['transparent'])) {
            $thumb->setImageBackgroundColor('transparent');
        } else {
            $thumb->setImageBackgroundColor('white');
        }
        if ($need_crop) {
            $thumb->cropimage($params["selectorW"], $params["selectorH"], $params["selectorX"], $params["selectorY"]);

            if ($params["selectorW"] > $params["selectorH"]) {
                $max = $params["selectorW"];
                $min = round(($params["selectorW"] / $params["imageW"]) * $params["imageH"]);
            } else {
                $min = $params["selectorH"];
                $max = round(($params["selectorH"] / $params["imageH"]) * $params["imageW"]);
            }

            $thumb->extentImage($max, $min, -($max - $params["selectorW"]) / 2, -($min - $params["selectorH"]) / 2);
            $thumb->resizeimage($params["imageW"], $params["imageH"], null, 1);
        }

        $date = date('Y/m/d');

        $uploadDir = UPLOAD_PATH . '/images/' . $date;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
            chown($uploadDir, 'ad-user');
        }


        $thumb->setImageFormat("png32");
        $thumb->writeImage($uploadDir . '/' . $basename);

        $thumb->clear();
        $thumb->destroy();
        $image_url = 'images/' . $date . '/' . $basename;
        $image_src = $uploadDir . '/' . $basename;

        return array(
            'image_src' => $uploadDir . '/' . $basename,
            'image_url' => $image_url,
            'dimensions' => array('width' => $params['imageW'], 'height' => $params['imageH']),
            'extension' => $tokens['extension'],
        );
    }

    /**
     * @param $params
     * @return array
     */
    public static function moveFile($params)
    {
        $file_info = pathinfo($params['fileSource']);
        $extension = strtolower($file_info['extension']);
        $filename = $file_info['filename'];

        //check extension
        switch ($extension) {
            case 'swf':
                $extFolder = 'video/swf/';
                break;
            case 'mp4':
                $extFolder = 'video/mp4/';
                break;
            default:
                $extFolder = 'images/';
                break;
        }
        //Create folder
        $date = date('Y/m/d');
        $uploadDir = UPLOAD_PATH . $extFolder . $date;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
            chown($uploadDir, 'ad-user');
        }
        $path = realpath($uploadDir);

        $hashed_name = md5($filename . uniqid()) . '.' . $extension;
        $target = $path . '/' . $hashed_name;

        try {
            //get file dimensions

            if ($extension == "mp4") {
                $convertedFile = str_replace('.mp4', 'c.mp4', $target);
                qtfaststart($target, $convertedFile);
                @unlink($target);
                $target = $convertedFile;
            }
            $params['fileSource'] = str_replace(UPLOAD_URL, UPLOAD_PATH, $params['fileSource']);
            @copy($params['fileSource'], $target);

            $dimensions = @getimagesize($target);
            if ($extension == "mp4") {
                $ffmpegInstance = new ffmpeg_movie($target);
                return array(
                    'status' => 1,
                    'dimensions' => array('width' => $ffmpegInstance->getFrameHeight(), 'height' => $ffmpegInstance->getFrameWidth()),
                    'extension' => $extension,
                    'file_url' => $extFolder . $date . '/' . str_replace('.mp4', 'c.mp4', $hashed_name)
                );
            }
            return array(
                'status' => 1,
                'dimensions' => array('width' => $dimensions[0], 'height' => $dimensions[1]),
                'extension' => $extension,
                'file_url' => $extFolder . $date . '/' . str_replace('.mp4', 'c.mp4', $hashed_name),
            );
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
    }

    /**
     * @param $clicks
     * @param $impressions
     * @return float|int
     */
    public static function getCTR($clicks, $impressions)
    {
        if ($impressions == 0) {
            return 0;
        }
        return round(($clicks / $impressions) * 100, 4);
    }

    /**
     * @return string
     */
    public static function makeVerifyKey()
    {
        $id = uniqid();
        $key = md5($id . 'adx.vn' . time());
        $chunks = str_split($key, 8);

        return implode('-', $chunks);
    }

    /**
     * @param $array
     * @return array
     */
    public static function mergeDuplicateArrayKey($array)
    {
        $result = array();
        foreach ($array as $key => $value) {
            $result[$value][] = $key;
        }
        return $result;
    }

    /**
     * @param $data
     * @param $key
     * @return mixed
     */
    public static function pluck($data, $key)
    {
        return array_reduce($data, function ($result, $array) use ($key) {
            isset($array[$key]) && $result[] = $array[$key];
            return $result;
        }, array());
    }

    /**
     * @param $data
     * @param array $default
     * @param $fromDate
     * @param $toDate
     * @param string $sort
     * @param string $step
     * @param string $format
     * @return array
     */
    public static function fillFullDate($data, $default = array(), $fromDate, $toDate, $sort = 'DESC', $step = '+1 day', $format = 'Y-m-d')
    {
        $dateRange = self::getRangeDate($fromDate, $toDate, $step, $format);
        $dataDates = self::pluck($data, 'DATE_NAME');

        foreach ($dateRange as $date) {
            if (!in_array($date, $dataDates)) {
                $data[] = array_merge($default, array(
                    'DATE_NAME' => $date
                ));
            }
        }

        /**
         * Sort the key
         */
        usort($data, function ($a, $b) {
            if ($a['DATE_NAME'] == $b['DATE_NAME']) {
                return 0;
            }

            return ($a['DATE_NAME'] < $b['DATE_NAME']) ? -1 : 1;

        });

        return $data;
    }

    /**
     * @param $array
     * @param $attr
     * @param $val
     * @param bool $strict
     * @return bool|int|null|string
     */
    public static function arraySearchInner($array, $attr, $val, $strict = FALSE)
    {
        // Error is input array is not an array
        if (!is_array($array))
            return FALSE;
        // Loop the array
        foreach ($array as $key => $inner) {
            // Error if inner item is not an array (you may want to remove this line)
            if (!is_array($inner))
                return FALSE;
            // Skip entries where search key is not present
            if (!isset($inner[$attr]))
                continue;
            if ($strict) {
                // Strict typing
                if ($inner[$attr] === $val)
                    return $key;
            } else {
                // Loose typing
                if ($inner[$attr] == $val)
                    return $key;
            }
        }
        // We didn't find it
        return NULL;
    }

    /**
     * @param $array
     * @param $field
     * @return array
     */
    public static function getMinMaxArray($array, $field)
    {
        $result = array();
        foreach ($array as $key => $value) {
            $result[] = $value[$field];
        }
        asort($result);

        return count($result) > 0 ? array('min' => current($result), 'max' => end($result)) : array('min' => 0, 'max' => 0);
    }

    /**
     * @param $val
     * @param $arr_min_max
     * @return mixed
     */
    public static function normalLine($val, $arr_min_max)
    {
        return (($val - $arr_min_max['min']) / ($arr_min_max['max'] - $arr_min_max['min']));
    }

    /**
     * @param $data
     * @param $sku
     * @param int $num_multiply
     * @return mixed
     */
    public static function changeScoreProduct($data, $sku, $num_multiply = 2)
    {
        //
        foreach ($data as &$item) {
            //
            if (!isset($item['itemId']) || $item['itemId'] != $sku) {
                continue;
            }
            //
            $item['value'] *= $num_multiply;
        }
        //
        usort($data, function ($a, $b) {
            if ((float)$a['value'] == (float)$b['value']) {
                return 0;
            } else {
                return (float)$a['value'] < (float)$b['value'] ? 1 : -1;
            }
        });
        //
        return $data;
    }

    public static function getHost($Address)
    {
        $parseUrl = parse_url(trim($Address));
        return @trim(@$parseUrl['host'] ? @$parseUrl['host'] : array_shift(explode('/', @$parseUrl['path'], 2)));
    }

    /**
     * @param $fileName
     * @return bool
     */
    public static function deleteFileFlag($fileName)
    {
        if (file_exists($fileName)) {
            unlink($fileName);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $fileName
     * @param $message
     * @return bool
     */
    public static function writeFileFlag($fileName, $message)
    {
        if (empty($fileName)) {
            return false;
        }

        $f = fopen($fileName, 'w');

        if (!$f) {
            return false;
        }

        fwrite($f, $message . "\n");

        fclose($f);

        return true;
    }

    /**
     * @param $number
     * @return bool|string
     */
    public static function convertNumberToWord($number)
    {
        if (!is_numeric($number)) {
            return false;
        }
        if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }
        if ($number == 0) {
            return "Không";
        }

        $negative = '';
        $decimal = '';
        if ($number < 0) {
            $negative = "Âm ";
            $number = abs($number);
        }
        if (strpos($number, '.') !== false) {
            $decimal = 'chấm ' . self::convertNumberToWord(explode('.', $number)[1]);
            $number = explode('.', $number)[0];
        }

        $Text = array("không", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín");
        $TextDonVi = array("", "nghìn", "triệu", "tỷ", "ngàn tỷ", "triệu tỷ", "tỷ tỷ");
        $textNumber = ""; //text kết quả
        $length = strlen($number); //Số ký tự của số tiền
        //Mảng lưu các phần tử có giá trị = 0; không đọc
        for ($i = 0; $i < $length; $i++) {
            $unread[$i] = 0;
        }

        for ($i = 0; $i < $length; $i++) {
            //Đọc ký tự từ cuối lên đầu
            $so = substr($number, $length - $i - 1, 1);
            //Nếu là số 0 và là chữ số thuộc $TextDonVi để bỏ các trường hợp đọc là 'không nghìn', 'không triệu', 'không tỷ', ...
            if (($so == 0) && ($i % 3 == 0) && ($unread[$i] == 0)) {
                //Tìm số j (index đầu tiên khác 0)
                for ($j = $i + 1; $j < $length; $j++) {
                    $so1 = substr($number, $length - $j - 1, 1);
                    if ($so1 != 0) {
                        break;
                    }
                }
                //Đưa các phần tử có giá trị = 0 vào vị trí mảng 0 tương ứng
                if (intval(($j - $i) / 3) > 0) {
                    for ($k = $i; $k < intval(($j - $i) / 3) * 3 + $i; $k++) {
                        $unread[$k] = 1;
                    }
                }
            }
        }

        for ($i = 0; $i < $length; $i++) {
            $so = substr($number, $length - $i - 1, 1);
            if ($unread[$i] == 1) {
                continue;
            }

            $thuocDonVi = $i % 3;
            switch ($thuocDonVi) {
                case 0:
                    $textNumber = $TextDonVi[$i / 3] . " " . $textNumber;
                    break;
                case 2:
                    $textNumber = 'trăm ' . $textNumber;
                    break;
                case 1:
                    $textNumber = 'mươi ' . $textNumber;
                    break;
            }
            $textNumber = $Text[$so] . " " . $textNumber;
        }

        //bắt buộc phải để đúng thứ tự
        $textNumber = str_replace("không mươi", "lẻ", $textNumber);
        $textNumber = str_replace("lẻ không", "", $textNumber);
        $textNumber = str_replace("mươi không", "mươi", $textNumber);
        $textNumber = str_replace("một mươi", "mười", $textNumber);
        $textNumber = str_replace("mươi năm", "mươi lăm", $textNumber);
        $textNumber = str_replace("mươi một", "mươi mốt", $textNumber);
        $textNumber = str_replace("mười năm", "mười lăm", $textNumber);

        return ucfirst(mb_strtolower($negative . $textNumber . $decimal, 'UTF-8'));
    }

    /*
     * $startDate type date (d/m/Y)
     * $endDate type date (d/m/Y)
     */
    public static function dateDiff($startDate, $endDate)
    {
        $fromDate = DateTime::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
        $toDate = DateTime::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

        try {
            $objFromDate = new DateTime($fromDate);
            $objToDate = new DateTime($toDate);
            $interval = $objFromDate->diff($objToDate);
            list($sign, $dateDiff) = explode(' ', $interval->format('%R %a'));
            return $sign . $dateDiff;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getGroupUnit($field, $groupBy)
    {
        if ($groupBy == 'hour') {
            return date("H:i d/m/Y", strtotime($field));
        } else if ($groupBy == 'week') {
            return date("\WW/Y", strtotime($field));
        } else if ($groupBy == 'month') {
            return date("\Mm/Y", strtotime($field));
        } else if ($groupBy == 'weekday') {
            return date("l", strtotime($field));
        } else {
            return date("d/m/Y H:i:s", strtotime($field));
        }
    }

    public static function parseGridFilter($filters)
    {
        $filters = json_decode($filters);

        $data = array();
        if (!empty($filters) && is_array($filters)) {
            foreach ($filters as $filter) {
                switch ($filter->op) {
                    case 'contains':
                        $data[$filter->field] = $filter->value != '' ? $filter->value : NULL;
                        break;
                    case 'equal':
                        if (in_array($filter->field, array('status'))) {
                            $data[$filter->field] = $filter->value != '' ? $filter->value : NULL;
                        } else {
                            $data['from_' . $filter->field] = $filter->value != '' ? $filter->value : NULL;
                            $data['to_' . $filter->field] = $filter->value != '' ? $filter->value : NULL;
                        }

                        break;
                    case 'less':
                        $data['to_' . $filter->field] = $filter->value != '' ? $filter->value : NULL;
                        break;
                    case 'greater':
                        $data['from_' . $filter->field] = $filter->value != '' ? $filter->value : NULL;
                        break;
                }
            }
        }
        return $data;
    }

    /**
     * @param $sort
     * @return int
     */
    public static function getOrder($sort, $obj)
    {
        $dataOrder['website'] = array('website_id' => 1, 'website_name' => 2, 'section_id' => 1, 'section_name' => 1);

        $params = array_merge(array(
            'impression_amount' => 20,
            'true_impression' => 21,
            'click_amount' => 22,
            'ctr' => 23,
            'true_ctr' => 24,
            'cpm' => 25,
            'cost' => 26
        ), (isset($dataOrder[$obj]) ? $dataOrder[$obj] : array()));
        return isset($params[$sort]) ? $params[$sort] : 1;
    }

    /**
     * @param $sort
     * @return int
     */
    public static function getAZ($sort)
    {
        return strtoupper($sort) == 'DESC' ? 1 : 0;
    }

    public static function getTimeAgo($timestamp)
    {
        $diff = time() - (int)$timestamp;

        if ($diff == 0)
            return 'Vài giây trước';

        $intervals = array
        (
            1 => array('năm', 31556926),
            $diff < 31556926 => array('tháng', 2628000),
            $diff < 2629744 => array('tuần', 604800),
            $diff < 604800 => array('ngày', 86400),
            $diff < 86400 => array('giờ', 3600),
            $diff < 3600 => array('phút', 60),
            $diff < 60 => array('giây', 1)
        );

        $value = floor($diff / $intervals[1][1]);
//         . ($value > 1 ? 'giây' : '')
        return 'cách đây ' . $value . ' ' . $intervals[1][0];
    }

    public static function cleanKeyword($keyword = '')
    {
        $keyword = trim($keyword);

        if (!self::checkEmail($keyword)) {
            //',*,(,),<,>,-,~,;,!,$,&,=,|,[,],{,}
            $removeKeyword = array(
                "/\'/",
//                "/\./",
                "/\'/",
                "/\,/",
                "/\(/",
                "/\)/",
                "/\*/",
                "/\>/",
                "/\</",
                "/\-/",
                "/\~/",
                "/\;/",
                "/\!/",
                "/\&/",
                "/\=/",
                "/\|/",
                "/\}/",
                "/\{/",
                "/\[/",
                "/\]/"
            );
            $keyword = str_replace("$", " ", $keyword);
            $keyword = preg_replace($removeKeyword, " ", $keyword);

            return preg_replace('/\n+|\t+|\s+/', ' ', $keyword);
        }

        return $keyword;
    }

    public static function formatStatic($result)
    {
        $result['click_amount'] = isset($result['click_amount_comp']) ? Adx_Utils::formatCompare($result['click_amount'], $result['click_amount_comp']) : number_format($result['click_amount']);
        $result['banner_amount'] = isset($result['banner_amount_comp']) ? Adx_Utils::formatCompare($result['banner_amount'], $result['banner_amount_comp']) : number_format($result['banner_amount']);
        $result['click_fraud'] = isset($result['click_fraud_comp']) ? Adx_Utils::formatCompare($result['click_fraud'], $result['click_fraud_comp']) : number_format($result['click_fraud']);
        $result['cpm'] = isset($result['cpm_comp']) ? Adx_Utils::formatCompare($result['cpm'], $result['cpm_comp']) : number_format($result['cpm']);
        $result['ctr'] = isset($result['ctr_comp']) ? Adx_Utils::formatCtrCompare($result['ctr'], $result['ctr_comp']) : round($result['ctr'], 2) . '%';
        $result['impression_amount'] = isset($result['impression_amount_comp']) ? Adx_Utils::formatCompare($result['impression_amount'], $result['impression_amount_comp']) : number_format($result['impression_amount']);
        $result['revenue_amount'] = isset($result['revenue_amount_comp']) ? Adx_Utils::formatCompare($result['revenue_amount'], $result['revenue_amount_comp']) : number_format($result['revenue_amount']);
        $result['true_ctr'] = isset($result['true_ctr_comp']) ? Adx_Utils::formatCtrCompare($result['true_ctr'], $result['true_ctr_comp']) : round($result['true_ctr'], 2) . '%';
        $result['true_impression'] = isset($result['true_impression_comp']) ? Adx_Utils::formatCompare($result['true_impression'], $result['true_impression_comp']) : number_format($result['true_impression']);

        //
        if (isset($result['pageview']))
            $result['pageview'] = isset($result['pageview_comp']) ? Adx_Utils::formatCompare($result['pageview'], $result['pageview_comp']) : number_format($result['pageview']);
        if (isset($result['cost']))
            $result['pageview'] = isset($result['pageview_comp']) ? Adx_Utils::formatCompare($result['pageview'], $result['pageview_comp']) : number_format($result['pageview']);

        return $result;
    }

    public static function formatCompare($value, $compare)
    {
        if ($compare - $value > 0) {
            return '<div style="text-align:left" class="dataCompare"><b style="font-size:14px">' . @number_format($value) . '</b><br/>' . @number_format($compare) . '&nbsp;&nbsp;<i class="ico ico-increase"></i>' . round((($compare - $value) / $value) * 100) . '%</div>';
        } elseif ($compare - $value < 0) {
            return '<div style="text-align:left" class="dataCompare"><b style="font-size:14px">' . @number_format($value) . '</b><br/>' . @number_format($compare) . '&nbsp;&nbsp;<i class="ico ico-decrease"></i>' . round((($value - $compare) / $value) * 100) . '%</div>';
        } else {
            return '<div style="text-align:left" class="dataCompare"><b style="font-size:14px">' . @number_format($value) . '</b><br/>' . @number_format($compare) . '</div>';
        }

    }

    public static function formatCtrCompare($value, $compare)
    {
        if ($compare - $value > 0) {
            return '<div style="text-align:left" class="dataCompare"><b style="font-size:14px">' . round($value, 2) . '%</b><br/>' . round($compare, 2) . '%&nbsp;&nbsp;<i class="ico ico-increase"></i>' . round($compare - $value, 2) . '%</div>';
        } elseif ($compare - $value < 0) {
            return '<div style="text-align:left" class="dataCompare"><b style="font-size:14px">' . round($value, 2) . '%</b><br/>' . round($compare, 2) . '%&nbsp;&nbsp;<i class="ico ico-decrease"></i>' . round($value - $compare, 2) . '%</div>';
        } else {
            return '<div style="text-align:left" class="dataCompare"><b style="font-size:14px">' . round($value, 2) . '%</b><br/>' . round($compare, 2) . '</div>';
        }

    }

    public static function getMinDate($to_date, $number = '-7', $unit = 'days', $format = 'd-m-Y')
    {
        if (empty($to_date) || (strtotime($to_date) - strtotime(date($format)) >= 0)) {
            return date($format, strtotime("$number $unit"));
        }
        return date($format, strtotime("$to_date $number $unit"));
    }

    /**
     * @param $data
     * @param string $field_min_name
     * @param string $field_max_name
     * @return array
     */
    public static function getMinMaxDate($data, $field_min_name = 'from_date', $field_max_name = 'to_date')
    {
        if (is_object($data)) {
            $arr_from_date = $data->pluck($field_min_name);
            $arr_to_date = $data->pluck($field_max_name);
            return array(
                'min_date' => min($arr_from_date),
                'max_date' => max($arr_to_date),
            );
        } elseif (is_array($data)) {
            return array(
                'min_date' => min(array_column($data, $field_min_name)),
                'max_date' => max(array_column($data, $field_max_name)),
            );
        }

        return array(
            'min_date' => '',
            'max_date' => '',
        );
    }

    /**
     * @param $from_date :Y-m-d
     * @param $to_date : Y-m-d
     * @param int $number
     * @param string $unit
     * @return array
     */
    public static function getDateToDateRange($from_date, $to_date, $number = '6', $unit = 'days', $limit = 6)
    {
        $from_date = explode(" ", $from_date)[0];
        $to_date = explode(" ", $to_date)[0];
        $today = date('Y-m-d');

        $min_max_date = array(
            'min_date' => $from_date . ' 00:00:00',
            'max_date' => $to_date . ' 23:59:59'
        );

        if (strtotime($to_date) < strtotime($today)) {
            return array_merge($min_max_date, array(
                'from_date' => strtotime($to_date . " -" . $number . " " . $unit) < strtotime($from_date) ?
                    $from_date : date('Y-m-d', strtotime($to_date . " -" . $number . " " . $unit)),
                'to_date' => $to_date,
            ));
        }

        if (strtotime($from_date) >= strtotime($today)) {
            return array_merge($min_max_date, array(
                'from_date' => $from_date,
                'to_date' => strtotime($from_date . " +" . $number . " " . $unit) > strtotime($to_date) ?
                    $to_date : date('Y-m-d', strtotime($from_date . " +" . $number . " " . $unit)),
            ));
        } elseif (strtotime($from_date . " +" . $number . " " . $unit) < strtotime($today)) {
            return array_merge($min_max_date, array(
                'from_date' => date('Y-m-d', strtotime($today . " -" . $number . " " . $unit)),
                'to_date' => date('Y-m-d', strtotime($today))
            ));
        } else {
            return array_merge($min_max_date, array(
                'from_date' => $from_date,
                'to_date' => strtotime($from_date . " +" . $number . " " . $unit) > strtotime($today) ?
                    date('Y-m-d', strtotime($today)) : date('Y-m-d', strtotime($from_date . " +" . $number . " " . $unit)),
            ));
        }
    }

    public static function clearKeyword($keyword, $html_special_chars = true, $strip_tags = true)
    {
        return htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8');
    }

    public static function str_len($string)
    {
        return strlen(utf8_decode($string));
    }

    public static function getNameFromNumber($num)
    {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return self::getNameFromNumber($num2 - 1) . $letter;
        } else {
            return $letter;
        }
    }

    public static function getWeekDay($date)
    {
        $weekday = date("l", strtotime($date));
        $weekday = strtolower($weekday);
        switch ($weekday) {
            case 'monday':
                $weekday = 'Thứ 2';
                break;
            case 'tuesday':
                $weekday = 'Thứ 3';
                break;
            case 'wednesday':
                $weekday = 'Thứ 4';
                break;
            case 'thursday':
                $weekday = 'Thứ 5';
                break;
            case 'friday':
                $weekday = 'Thứ 6';
                break;
            case 'saturday':
                $weekday = 'Thứ 7';
                break;
            default:
                $weekday = 'Chủ nhật';
                break;
        }
        return $weekday;
    }

    public static function checkHttpCode($url)
    {
        //
        $ch = curl_init($url);
        //
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_exec($ch);
        //
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //
        curl_close($ch);
        //
        return $http_code;
    }

    public static function antsSendSMS($message = '', $service = 'PHP', $func = 'push_all', $phone_list = '', $sound = 'teoroi')
    {
        /**
         * $func: push_all|push
         * $phone_list: 0909090909;0909080808 -> $func=push
         *
         * phone:
         * + A.Nghĩa: 0968789788
         * + A.Toàn: 0983512426
         * + Tiến: 0907714169
         * + Duân: 0935391479
         * + A.Tuấn: 0909644275
         * + Khâm: 0983063814
         * + Hùng: 01678142765
         * + A.Đen: 0935683567
         * + A.Móm: 0903769672
         * + C.Na: 0909518515
         */
        //http://push.ants.vn/ants/api?token=6d94d708479015a909&func=push_all&message=Your message here&sound=teoroi
        // create curl resource
        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, "http://push.ants.vn/ants/api?token=6d94d708479015a909&ip=203.162.76.49&func=" . $func . "&service=" . urlencode($service) . "&message=" . urlencode($message) . "&phone_list=" . urlencode($phone_list) . "&sound=" . $sound);
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $output contains the output string
        curl_exec($ch);
        // close curl resource to free up system resources
        curl_close($ch);
    }

    public static function get_img_dim($src)
    {
        $headers = array('Range: bytes=0-500000');
        //
        $ch = curl_init($src);
        //
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //
        $data = curl_exec($ch);
        //
        curl_close($ch);

        $img = @imagecreatefromstring($data);
        $colors = @imagecolorstotal($img);
        $width = @imagesx($img);
        $height = @imagesy($img);
        @imagedestroy($img);

        return array($width, $height, $colors);
    }

    public static function get_img_info($src)
    {
        //
        $ch = curl_init($src);
        //
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $src);
        curl_setopt($ch, CURLOPT_HEADER, true);
        //curl_setopt( $curl, CURLOPT_NOBODY, true );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //
        $data = str_replace(' ', '', curl_exec($ch));
        $size = @explode('Content-Length:', $data);
        //In the event Content-Length is not sent in the header
        if (array_key_exists(1, $size)) {
            $size = preg_split('/\s+/', $size[1]);
            $filesize = (int)$size[0];
        } else {
            $filesize = self::get_img_size($src);
        }
        //
        $type = explode('Content-Type:', $data);
        $type = preg_split('/\s+/', $type[1]);
        //
        if ($type[0] === 'image/gif') {
            $ext = 1;
        } elseif ($type[0] === 'image/jpeg' || $type[0] === 'image/jpg') {
            $ext = 2;
        } elseif ($type[0] === 'image/png') {
            $ext = 3;
        } else {
            $ext = 'N/A';
        }

        return array($ext, $type[0], $filesize);
    }

    public static function getImageSize($src)
    {
        $return_val = false;
        //
        $img_dim = self::get_img_dim($src);
        $img_info = self::get_img_info($src);
        //
        if ($img_dim[0] != '') {
            $return_val = array(
                'width' => $img_dim[0],
                'height' => $img_dim[1],
                'colors' => $img_dim[2],
                'mime' => $img_info[1],
                'size' => $img_info[2]
            );
        }

        return $return_val;
    }

    public static function downloadImage($image_url)
    {
        $type = explode(".", $image_url);
        $ext = strtolower($type[sizeof($type) - 1]);
        $extension = (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) ? "jpeg" : $ext;
        //
        $date = date('Y/m/d');
        //
        $upload_dir = UPLOAD_PATH . '/images/' . $date;
        //
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
            chown($upload_dir, 'ad-user');
        }
        //
        $upload_path = realpath($upload_dir);
        //
        $hashed_image_name = md5($image_url . uniqid()) . '.' . $extension;
        $path_storage = $upload_path . '/' . $hashed_image_name;
        //
        $fp = fopen($path_storage, 'w+');
        //
        $ch = curl_init($image_url);
        //
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
        //
        curl_exec($ch);
        //
        curl_close($ch);
        //
        fclose($fp);
        //
        return $path_storage;
    }

    public static function safeUrlEncode($txt)
    {
        // Skip all URL reserved characters plus dot, dash, underscore and tilde..
        $result = preg_replace_callback("/[^-\._~:\/\?#\\[\\]@!\$&'\(\)\*\+,;=]+/",
            function ($match) {
                // ..and encode the rest!
                return rawurlencode($match[0]);
            }, $txt);
        return ($result);
    }

    public static function multiDownloadResizeImageBackup($data, $num = 30)
    {
        //
        $dir_image = '/images/' . date('Y/m/d');
        //
        $upload_dir = UPLOAD_PATH . $dir_image;
        //
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
            chown($upload_dir, 'ad-user');
        }
        //
        $upload_path = realpath($upload_dir);

        //
        $data_slice = array_chunk($data, $num, true);

        // data to be returned
        $result = array();

        foreach ($data_slice as $item) {
            // array of curl handles
            $curly = array();

            //
            $image_info = array();

            // multi handle
            $mh = curl_multi_init();

            // loop through $data and create curl handles
            // then add them to the multi-handle
            foreach ($item as $id => $image_url) {
                //
                $type = explode(".", $image_url);
                $ext = strtolower($type[sizeof($type) - 1]);
                $extension = (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) ? "jpeg" : $ext;
                //
                $hashed_image_name = md5($image_url . uniqid()) . '.' . $extension;
                //
                $image_info[$id] = array(
                    'extension' => $extension,
                    'path_storage' => $upload_path . '/' . $hashed_image_name,
                    'dir_image' => $dir_image . '/' . $hashed_image_name
                );
                //
                $curly[$id] = curl_init();
                //
                curl_setopt($curly[$id], CURLOPT_URL, $image_url);
                curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curly[$id], CURLOPT_BINARYTRANSFER, 1);
                // curl_setopt($curly[$id], CURLOPT_TIMEOUT, 20);
                curl_setopt($curly[$id], CURLOPT_HEADER, 0);

                curl_multi_add_handle($mh, $curly[$id]);
            }

            // execute the handles
            $running = null;
            do {
                curl_multi_exec($mh, $running);
            } while ($running > 0);

            // get content and remove handles
            foreach ($curly as $id => $c) {
                $data = curl_multi_getcontent($c);

                if (empty($data)) {
                    $result[$id] = '';
                } else {
                    //
                    file_put_contents($image_info[$id]['path_storage'], $data);
                    /*
                    $fp = fopen($image_info[$id]['path_storage'], 'w');
                    fwrite($fp, $data);
                    fclose($fp);
                    */
                    //
                    $result[$id] = $image_info[$id]['dir_image'];
                }
                //
                curl_multi_remove_handle($mh, $c);
                //
                curl_close($c);
            }

            // all done
            curl_multi_close($mh);

        }

        return $result;
    }

    public static function multiDownloadResizeImage($data, $num = 30)
    {
        //
        $dir_image = '/dynamic/' . date('Y/m/d');
        //
        $upload_dir = UPLOAD_PATH . $dir_image;
        //
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
            chown($upload_dir, 'ad-user');
        }
        //
        $upload_path = realpath($upload_dir);

        // data to be returned
        $result = array();
        $max_width = 500;
        $max_height = 500;
        $quality = 90;

        if (count($data) < $num) {
            // array of curl handles
            $curly = array();
            //
            $image_info = array();
            // multi handle
            $mh = curl_multi_init();
            // loop through $data and create curl handles
            // then add them to the multi-handle
            foreach ($data as $id => $image_url) {
                //
                $type = explode(".", $image_url);
                $ext = strtolower($type[sizeof($type) - 1]);
                $extension = (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) ? "jpeg" : $ext;
                //
                $hashed_image_name = md5($image_url . uniqid()) . '.' . $extension;
                //
                $image_info[$id] = array(
                    'extension' => $extension,
                    'image_url' => $image_url,
                    'path_storage' => $upload_path . '/' . $hashed_image_name,
                    'dir_image' => $dir_image . '/' . $hashed_image_name
                );
                //
                $curly[$id] = curl_init();
                //
                curl_setopt($curly[$id], CURLOPT_URL, $image_url);
                curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curly[$id], CURLOPT_BINARYTRANSFER, 1);
                // curl_setopt($curly[$id], CURLOPT_TIMEOUT, 20);
                curl_setopt($curly[$id], CURLOPT_HEADER, 0);

                curl_multi_add_handle($mh, $curly[$id]);
            }

            // execute the handles
            $running = null;
            do {
                curl_multi_exec($mh, $running);
            } while ($running > 0);


            // get content and remove handles
            foreach ($curly as $id => $c) {
                $data = curl_multi_getcontent($c);

                if (empty($data)) {
                    $result[$id] = '';
                } else {
                    //
                    //file_put_contents($image_info[$id]['path_storage'], $data);
                    //
                    $new_image = imagecreatefromstring($data);
                    //
                    list($i_width, $i_height, $type_image) = getimagesize($image_info[$id]['image_url']);
                    //
                    $image_scale = min($max_width / $i_width, $max_height / $i_height);
                    $new_width = ceil($image_scale * $i_width);
                    $new_height = ceil($image_scale * $i_height);
                    //
                    $new_canves = imagecreatetruecolor($new_width, $new_height);
                    //
                    switch (strtolower(image_type_to_mime_type($type_image))) {
                        case 'image/png':
                            $function_copy = 'imagejpeg';
                            break;
                        case 'image/gif':
                            $function_copy = 'imagegif';
                            break;
                        default:
                            $function_copy = 'imagejpeg';
                    }
                    // Resize Image
                    imagecopyresampled($new_canves, $new_image, 0, 0, 0, 0, $new_width, $new_height, $i_width, $i_height);
                    //
                    $function_copy($new_canves, $image_info[$id]['path_storage'], $quality);
                    //
                    imagedestroy($new_image);
                    //
                    imagedestroy($new_canves);
                    /*
                    $fp = fopen($image_info[$id]['path_storage'], 'w');
                    fwrite($fp, $data);
                    fclose($fp);
                    */
                    //
                    $result[$id] = $image_info[$id]['dir_image'];
                }
                //
                curl_multi_remove_handle($mh, $c);
                //
                curl_close($c);
            }

            // all done
            curl_multi_close($mh);
        } else {
            //
            $data_slice = array_chunk($data, $num, true);

            foreach ($data_slice as $item) {
                // array of curl handles
                $curly = array();
                //
                $image_info = array();
                // multi handle
                $mh = curl_multi_init();
                // loop through $data and create curl handles
                // then add them to the multi-handle
                foreach ($item as $id => $image_url) {
                    //
                    $type = explode(".", $image_url);
                    $ext = strtolower($type[sizeof($type) - 1]);
                    $extension = (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) ? "jpeg" : $ext;
                    //
                    $hashed_image_name = md5($image_url . uniqid()) . '.' . $extension;
                    //
                    $image_info[$id] = array(
                        'extension' => $extension,
                        'image_url' => $image_url,
                        'path_storage' => $upload_path . '/' . $hashed_image_name,
                        'dir_image' => $dir_image . '/' . $hashed_image_name
                    );
                    //
                    $curly[$id] = curl_init();
                    //
                    curl_setopt($curly[$id], CURLOPT_URL, $image_url);
                    curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curly[$id], CURLOPT_BINARYTRANSFER, 1);
                    // curl_setopt($curly[$id], CURLOPT_TIMEOUT, 20);
                    curl_setopt($curly[$id], CURLOPT_HEADER, 0);

                    curl_multi_add_handle($mh, $curly[$id]);
                }

                // execute the handles
                $running = null;
                do {
                    curl_multi_exec($mh, $running);
                } while ($running > 0);


                // get content and remove handles
                foreach ($curly as $id => $c) {
                    $data = curl_multi_getcontent($c);

                    if (empty($data)) {
                        $result[$id] = '';
                    } else {
                        //
                        //file_put_contents($image_info[$id]['path_storage'], $data);
                        //
                        $new_image = imagecreatefromstring($data);
                        //
                        list($i_width, $i_height, $type_image) = getimagesize($image_info[$id]['image_url']);
                        //
                        $image_scale = min($max_width / $i_width, $max_height / $i_height);
                        $new_width = ceil($image_scale * $i_width);
                        $new_height = ceil($image_scale * $i_height);
                        //
                        $new_canves = imagecreatetruecolor($new_width, $new_height);
                        //
                        switch (strtolower(image_type_to_mime_type($type_image))) {
                            case 'image/png':
                                $function_copy = 'imagejpeg';
                                break;
                            case 'image/gif':
                                $function_copy = 'imagegif';
                                break;
                            default:
                                $function_copy = 'imagejpeg';
                        }
                        // Resize Image
                        imagecopyresampled($new_canves, $new_image, 0, 0, 0, 0, $new_width, $new_height, $i_width, $i_height);
                        //
                        $function_copy($new_canves, $image_info[$id]['path_storage'], $quality);
                        //
                        imagedestroy($new_image);
                        //
                        imagedestroy($new_canves);
                        /*
                        $fp = fopen($image_info[$id]['path_storage'], 'w');
                        fwrite($fp, $data);
                        fclose($fp);
                        */
                        //
                        $result[$id] = $image_info[$id]['dir_image'];
                    }
                    //
                    curl_multi_remove_handle($mh, $c);
                    //
                    curl_close($c);
                }

                // all done
                curl_multi_close($mh);
            }
        }
        //
        return $result;
    }

    public static function multiDownloadResizeImageCrawler($data_link_image = array())
    {
        /*
        //
        $upload_dir = UPLOAD_PATH . $dir_image;
        //
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
            chown($upload_dir, 'ad-user');
        }
        */
        //
        $current_date = date('Y/m/d');
        //
        $dir_image = '/dynamic/' . $current_date;
        //
        $upload_dir = (UPLOAD_PATH . '/dynamic');
        //
        $upload_path = ($upload_dir . '/' . $current_date);
        //
        if (!is_dir($upload_path)) {
            //
            mkdir($upload_path, 0755, true);
            //
            $process_user = posix_getpwuid(posix_geteuid());
            //
            if (isset($process_user['name']) && $process_user['name'] == 'root') {
                $arr_data = explode('/', $current_date);
                if (is_array($arr_data)) {
                    $item_path = '';
                    foreach ($arr_data as $item) {
                        $item_path .= $item . '/';
                        //
                        chown($upload_dir . '/' . $item_path, 'ad-user');
                    }
                }
            }
        }
        // data to be returned
        $result = array();
        $arr_link_image = array();
        $arr_dir_image = array();
        //
        foreach ($data_link_image as $index => $item_link_image) {
            //
            $type = explode(".", $item_link_image);
            $ext = strtolower($type[sizeof($type) - 1]);
            $extension = (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) ? "jpeg" : $ext;
            //
            $hashed_image_name = md5($item_link_image . uniqid()) . '.' . $extension;
            //
            $dir_image_full_path = $dir_image . '/' . $hashed_image_name;
            //
            $arr_link_image[] = $item_link_image;
            $arr_dir_image[] = $dir_image_full_path;
            //
            $result[$index] = $dir_image_full_path;
        }
        //
        Adx_Utils::runJob(
            'JobAdminProduct',
            'downloadLinkImage',
            'doHighBackgroundTask',
            'admin_process',
            array(
                'debug' => 'false',
                'actor' => __FUNCTION__,
                'link_image' => $arr_link_image,
                'dir_image' => $arr_dir_image
            )
        );
        //
        return $result;
    }

    public static function makeDirImageDynamic($data_product_id = array(), $data_link_image = array())
    {
        //
        $dir_image = '/dynamic/' . date('Y/m/d');
        //
        $upload_dir = UPLOAD_PATH . $dir_image;
        //
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
            chown($upload_dir, 'ad-user');
        }
        // data to be returned
        $result = array();
        //
        foreach ($data_link_image as $index => $item_link_image) {
            //
            $type = explode(".", $item_link_image);
            $ext = strtolower($type[sizeof($type) - 1]);
            $extension = (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) ? "jpeg" : $ext;
            //
            $hashed_image_name = md5($item_link_image . uniqid()) . '.' . $extension;
            //
            $result['product_id'][] = $data_product_id[$index];
            $result['link_image'][] = $item_link_image;
            $result['dir_image'][] = $dir_image . '/' . $hashed_image_name;
        }
        //
        return $result;
    }

    public static function makeDirImageAutomation($data_product_sku = array(), $data_link_image = array(), $data_size = array())
    {
        //images/2016/08/19/3bd13db44a2b58e9b0b7133dcd78ff20.png
        $dir_image_server = 'images/' . date('Y/m/d');
        //
        $upload_dir = UPLOAD_PATH . $dir_image_server;
        //
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
            chown($upload_dir, 'ad-user');
        }
        // data to be returned
        $result = array();

        //
//        foreach ($data_size as $item_size) {
//            foreach ($data_link_image as $index => $item_link_image) {
//                //
//                $dir_image = '';
//                $type = explode(".", $item_link_image);
//                $ext = strtolower($type[sizeof($type) - 1]);
//                $extension = (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) ? "jpeg" : $ext;
//                //
//                $hashed_image_name = md5($item_link_image . uniqid()) . '.' . $extension;
//                //
//                $sku = $data_product_sku[$index];
//                $dir_image = $dir_image_server . '/' . $hashed_image_name;
//                $link_image = $item_link_image;
//                //
//                $result['download']['link_image'][] = $link_image;
//                $result['download']['dir_image'][] = $dir_image;
//                $result['download']['size'][] = $item_size;
//                //
//                $result['info'][md5($link_image)] = array(
//                    'sku' => $sku,
//                    'size' => $item_size
//                );
//            }
//        }

        foreach ($data_product_sku as $size => $arr_product) {
            foreach ($arr_product as $sku => $product){
                $dir_image = '';
                $item_link_image = $product['url_img'];
                $type = explode(".", $item_link_image);
                $ext = strtolower($type[sizeof($type) - 1]);
                $extension = (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) ? "jpeg" : $ext;
                //
                $hashed_image_name = md5($item_link_image . uniqid()) . '.' . $extension;
                //
                $dir_image = $dir_image_server . '/' . $hashed_image_name;
                $link_image = $item_link_image;
                //
                $result['download']['link_image'][] = $link_image;
                $result['download']['dir_image'][] = $dir_image;
                $result['download']['size'][] = $size;
                //
                $key = md5($link_image) . '_' . $size;
                $result['info'][$key] = array(
                    'sku' => $product['sku'],
                    'size' => $size
                );
            }

        }

        //
        return $result;
    }

    public static function makeDirLogoAutomation($data_link_image = array(), $data_size = array())
    {
        //images/2016/08/19/3bd13db44a2b58e9b0b7133dcd78ff20.png
        $dir_image = 'images/' . date('Y/m/d');
        //
        $upload_dir = UPLOAD_PATH . $dir_image;
        //
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
            chown($upload_dir, 'ad-user');
        }
        // data to be returned
        $result = array();

        //
        foreach ($data_size as $item_size) {
            foreach ($data_link_image as $index => $item_link_image) {
                //
                $type = explode(".", $item_link_image);
                $ext = strtolower($type[sizeof($type) - 1]);
                $extension = (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) ? "jpeg" : $ext;
                //
                $hashed_image_name = md5($item_link_image . uniqid()) . '.' . $extension;
                //
                $dir_image = $dir_image . '/' . $hashed_image_name;
                $link_image = $item_link_image;
                //
                $result['download']['link_image'][] = $link_image;
                $result['download']['dir_image'][] = $dir_image;
                $result['download']['size'][] = $item_size;
                //
                $result['info'][$item_size] = $dir_image;
            }
        }
        //
        return $result;
    }

    public static function encodeUrlImage($url_image)
    {
        if (empty($url_image)) {
            return $url_image;
        }
        //
        $expl_url_image = explode('/', $url_image);
        //
        if (empty($expl_url_image)) {
            return $url_image;
        }
        //
        $end_expl_url_image = end($expl_url_image);
        //
        if (empty($end_expl_url_image)) {
            return $url_image;
        }
        //
        $encode_end_expl_url_image = rawurlencode($end_expl_url_image);
        //
        return str_replace($end_expl_url_image, $encode_end_expl_url_image, $url_image);
    }

    public static function downloadImageDynamic($data_link_image = array(), $data_dir_image = array(), $num = 30)
    {
        ini_set('memory_limit', '1024M');
        //ini_set('max_execution_time', '300');

        // data to be returned
        $result = array();
        $max_width = 500;
        $max_height = 500;
        $quality = 90;
        //
        if (count($data_link_image) < $num) {
            // array of curl handles
            $curly = array();
            //
            $image_info = array();
            // multi handle
            $mh = curl_multi_init();
            // loop through $data and create curl handles
            // then add them to the multi-handle
            foreach ($data_link_image as $id => $image_url) {
                //
                $fix_image_url = self::encodeUrlImage($image_url);
                //
                $dir_image_internal = $data_dir_image[$id];
                //
                $type = explode(".", $dir_image_internal);
                $ext = strtolower($type[sizeof($type) - 1]);
                $extension = (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) ? "jpeg" : $ext;
                //
                //
                $image_info[$id] = array(
                    'extension' => $extension,
                    'image_url' => $fix_image_url,
                    'path_storage' => UPLOAD_PATH . $dir_image_internal,
                    'dir_image' => $dir_image_internal
                );
                //
                $curly[$id] = curl_init();
                //
                curl_setopt($curly[$id], CURLOPT_URL, $fix_image_url);
                curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curly[$id], CURLOPT_BINARYTRANSFER, 1);
                // curl_setopt($curly[$id], CURLOPT_TIMEOUT, 20);
                curl_setopt($curly[$id], CURLOPT_HEADER, 0);

                curl_multi_add_handle($mh, $curly[$id]);
            }

            // execute the handles
            $running = null;
            do {
                curl_multi_exec($mh, $running);
            } while ($running > 0);


            // get content and remove handles
            foreach ($curly as $id => $c) {
                $data = curl_multi_getcontent($c);

                if (empty($data)) {
                    $result[$id] = '';
                } else {
                    //
                    //file_put_contents($image_info[$id]['path_storage'], $data);
                    $new_image = imagecreatefromstring($data);
                    //
                    list($i_width, $i_height, $type_image) = getimagesize($image_info[$id]['image_url']);
                    //
                    $image_scale = min($max_width / $i_width, $max_height / $i_height);
                    $new_width = ceil($image_scale * $i_width);
                    $new_height = ceil($image_scale * $i_height);
                    //
                    $new_canves = imagecreatetruecolor($new_width, $new_height);
                    //
                    switch (strtolower(image_type_to_mime_type($type_image))) {
                        case 'image/png':
                            $white = imagecolorallocate($new_canves, 255, 255, 255);
                            imagefill($new_canves, 0, 0, $white);
                            $function_copy = 'imagejpeg';
                            break;
                        case 'image/gif':
                            $function_copy = 'imagegif';
                            break;
                        default:
                            $function_copy = 'imagejpeg';
                    }
                    // Resize Image
                    imagecopyresampled($new_canves, $new_image, 0, 0, 0, 0, $new_width, $new_height, $i_width, $i_height);
                    // Enable interlancing
                    imageinterlace($new_canves, true);
                    //
                    $function_copy($new_canves, $image_info[$id]['path_storage'], $quality);
                    //
                    imagedestroy($new_canves);
                    //
                    imagedestroy($new_image);
                    /*
                    $fp = fopen($image_info[$id]['path_storage'], 'w');
                    fwrite($fp, $data);
                    fclose($fp);
                    */
                    //
                    $result[$id] = $image_info[$id]['dir_image'];
                }
                //
                curl_multi_remove_handle($mh, $c);
                //
                curl_close($c);
            }
            // all done
            curl_multi_close($mh);
        } else {
            //
            $data_slice_link_image = array_chunk($data_link_image, $num, true);
            $data_slice_dir_image = array_chunk($data_dir_image, $num, true);
            //
            foreach ($data_slice_link_image as $index_link_image => $item) {
                // array of curl handles
                $curly = array();
                //
                $image_info = array();
                // multi handle
                $mh = curl_multi_init();
                // loop through $data and create curl handles
                // then add them to the multi-handle
                foreach ($item as $id => $image_url) {
                    //
                    $fix_image_url = self::encodeUrlImage($image_url);
                    //
                    $dir_image_internal = $data_slice_dir_image[$index_link_image][$id];
                    //
                    $type = explode(".", $dir_image_internal);
                    $ext = strtolower($type[sizeof($type) - 1]);
                    $extension = (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) ? "jpeg" : $ext;
                    //
                    $image_info[$id] = array(
                        'extension' => $extension,
                        'image_url' => $fix_image_url,
                        'path_storage' => UPLOAD_PATH . $dir_image_internal,
                        'dir_image' => $dir_image_internal
                    );
                    //
                    $curly[$id] = curl_init();
                    //
                    curl_setopt($curly[$id], CURLOPT_URL, $fix_image_url);
                    curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curly[$id], CURLOPT_BINARYTRANSFER, 1);
                    // curl_setopt($curly[$id], CURLOPT_TIMEOUT, 20);
                    curl_setopt($curly[$id], CURLOPT_HEADER, 0);

                    curl_multi_add_handle($mh, $curly[$id]);
                }

                // execute the handles
                $running = null;
                do {
                    curl_multi_exec($mh, $running);
                } while ($running > 0);


                // get content and remove handles
                foreach ($curly as $id => $c) {
                    $data = curl_multi_getcontent($c);

                    if (empty($data)) {
                        $result[$id] = '';
                    } else {
                        //
                        //file_put_contents($image_info[$id]['path_storage'], $data);
                        $new_image = imagecreatefromstring($data);
                        //
                        list($i_width, $i_height, $type_image) = getimagesize($image_info[$id]['image_url']);
                        //
                        $image_scale = min($max_width / $i_width, $max_height / $i_height);
                        $new_width = ceil($image_scale * $i_width);
                        $new_height = ceil($image_scale * $i_height);
                        //
                        $new_canves = imagecreatetruecolor($new_width, $new_height);
                        //
                        switch (strtolower(image_type_to_mime_type($type_image))) {
                            case 'image/png':
                                $white = imagecolorallocate($new_canves, 255, 255, 255);
                                imagefill($new_canves, 0, 0, $white);
                                $function_copy = 'imagejpeg';
                                break;
                            case 'image/gif':
                                $function_copy = 'imagegif';
                                break;
                            default:
                                $function_copy = 'imagejpeg';
                        }
                        // Resize Image
                        imagecopyresampled($new_canves, $new_image, 0, 0, 0, 0, $new_width, $new_height, $i_width, $i_height);
                        // Enable interlancing
                        imageinterlace($new_canves, true);
                        //
                        $function_copy($new_canves, $image_info[$id]['path_storage'], $quality);
                        //
                        imagedestroy($new_canves);
                        //
                        imagedestroy($new_image);
                        /*
                        $fp = fopen($image_info[$id]['path_storage'], 'w');
                        fwrite($fp, $data);
                        fclose($fp);
                        */
                        //
                        $result[$id] = $image_info[$id]['dir_image'];
                    }
                    //
                    curl_multi_remove_handle($mh, $c);
                    //
                    curl_close($c);
                }
                // all done
                curl_multi_close($mh);
            }
        }
        //
        return $result;
    }

    public static function downloadImageAutomation($data_link_image = array(), $data_dir_image = array(), $data_size = array(), $num = 30)
    {
        ini_set('memory_limit', '1024M');
        //ini_set('max_execution_time', '300');

        // data to be returned
        $result = array();
        $quality = 90;
        //
        if (count($data_link_image) < $num) {
            // array of curl handles
            $curly = array();
            //
            $image_info = array();
            // multi handle
            $mh = curl_multi_init();
            // loop through $data and create curl handles
            // then add them to the multi-handle
            foreach ($data_link_image as $id => $image_url) {
                //
                $max_size = explode('x', $data_size[$id]);
                $max_width = $max_size[0];
                $max_height = $max_size[1];
                //
                $fix_image_url = self::encodeUrlImage($image_url);
                //
                $dir_image_internal = $data_dir_image[$id];
                //
                $type = explode(".", $dir_image_internal);
                $ext = strtolower($type[sizeof($type) - 1]);
                $extension = (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) ? "jpeg" : $ext;
                //
                $image_info[$id] = array(
                    'extension' => $extension,
                    'image_url' => $fix_image_url,
                    'path_storage' => UPLOAD_PATH . $dir_image_internal,
                    'dir_image' => $dir_image_internal,
                    'size' => $data_size[$id],
                    'image' => $image_url,
                    'max_width' => $max_width,
                    'max_height' => $max_height
                );
                //
                $curly[$id] = curl_init();
                //
                //$fix_image_url = str_replace(STATIC_URL, ROOT_PATH . '/static', $fix_image_url);

                curl_setopt($curly[$id], CURLOPT_URL, $fix_image_url);
                curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curly[$id], CURLOPT_BINARYTRANSFER, 1);
                // curl_setopt($curly[$id], CURLOPT_TIMEOUT, 20);
                curl_setopt($curly[$id], CURLOPT_HEADER, 0);
                curl_multi_add_handle($mh, $curly[$id]);
            }

            // execute the handles
            $running = null;
            do {
                curl_multi_exec($mh, $running);
            } while ($running > 0);

            // get content and remove handles
            foreach ($curly as $id => $c) {
                $data = curl_multi_getcontent($c);
                if (empty($data)) {
                    $result[$id] = '';
                } else {
                    //
                    //file_put_contents($image_info[$id]['path_storage'], $data);
                    $new_image = imagecreatefromstring($data);
                    //
                    $max_width = $image_info[$id]['max_width'];
                    $max_height = $image_info[$id]['max_height'];
                    //
                    list($i_width, $i_height, $type_image) = getimagesize($image_info[$id]['image_url']);
                    //
                    $image_scale = min($max_width / $i_width, $max_height / $i_height);
                    $new_width = ceil($image_scale * $i_width);
                    $new_height = ceil($image_scale * $i_height);
                    //
                    $new_canves = imagecreatetruecolor($new_width, $new_height);
                    //
                    switch (strtolower(image_type_to_mime_type($type_image))) {
                        case 'image/png':
                            $white = imagecolorallocate($new_canves, 255, 255, 255);
                            imagefill($new_canves, 0, 0, $white);
                            $function_copy = 'imagejpeg';
                            break;
                        case 'image/gif':
                            $function_copy = 'imagegif';
                            break;
                        default:
                            $function_copy = 'imagejpeg';
                    }
                    // Resize Image
                    imagecopyresampled($new_canves, $new_image, 0, 0, 0, 0, $new_width, $new_height, $i_width, $i_height);
                    // Enable interlancing
                    imageinterlace($new_canves, true);
                    //
                    $function_copy($new_canves, $image_info[$id]['path_storage'], $quality);
                    //
                    imagedestroy($new_canves);
                    //
                    imagedestroy($new_image);
                    /*
                    $fp = fopen($image_info[$id]['path_storage'], 'w');
                    fwrite($fp, $data);
                    fclose($fp);
                    */
                    //
                    $result[$id] = $image_info[$id];
                }
                //
                curl_multi_remove_handle($mh, $c);
                //
                curl_close($c);
            }
            // all done
            curl_multi_close($mh);
        } else {
            //
            $data_slice_link_image = array_chunk($data_link_image, $num, true);
            $data_slice_dir_image = array_chunk($data_dir_image, $num, true);
            $data_slice_size = array_chunk($data_size, $num, true);
            //
            foreach ($data_slice_link_image as $index_link_image => $item) {
                // array of curl handles
                $curly = array();
                //
                $image_info = array();
                // multi handle
                $mh = curl_multi_init();
                // loop through $data and create curl handles
                // then add them to the multi-handle
                foreach ($item as $id => $image_url) {
                    //
                    $max_size = explode('x', $data_slice_size[$index_link_image][$id]);
                    $max_width = $max_size[0];
                    $max_height = $max_size[1];
                    //
                    $fix_image_url = self::encodeUrlImage($image_url);
                    //
                    $dir_image_internal = $data_slice_dir_image[$index_link_image][$id];
                    //
                    $type = explode(".", $dir_image_internal);
                    $ext = strtolower($type[sizeof($type) - 1]);
                    $extension = (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) ? "jpeg" : $ext;
                    //
                    $image_info[$id] = array(
                        'extension' => $extension,
                        'image_url' => $fix_image_url,
                        'path_storage' => UPLOAD_PATH . $dir_image_internal,
                        'dir_image' => $dir_image_internal,
                        'size' => $data_size[$id],
                        'image' => $image_url,
                        'max_width' => $max_width,
                        'max_height' => $max_height
                    );
                    //
                    $curly[$id] = curl_init();
                    //
                    curl_setopt($curly[$id], CURLOPT_URL, $fix_image_url);
                    curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curly[$id], CURLOPT_BINARYTRANSFER, 1);
                    // curl_setopt($curly[$id], CURLOPT_TIMEOUT, 20);
                    curl_setopt($curly[$id], CURLOPT_HEADER, 0);

                    curl_multi_add_handle($mh, $curly[$id]);
                }

                // execute the handles
                $running = null;
                do {
                    curl_multi_exec($mh, $running);
                } while ($running > 0);


                // get content and remove handles
                foreach ($curly as $id => $c) {
                    $data = curl_multi_getcontent($c);

                    if (empty($data)) {
                        $result[$id] = '';
                    } else {
                        //
                        //file_put_contents($image_info[$id]['path_storage'], $data);
                        $new_image = imagecreatefromstring($data);
                        //
                        $max_width = $image_info[$id]['max_width'];
                        $max_height = $image_info[$id]['max_height'];
                        //
                        list($i_width, $i_height, $type_image) = getimagesize($image_info[$id]['image_url']);
                        //
                        $image_scale = min($max_width / $i_width, $max_height / $i_height);
                        $new_width = ceil($image_scale * $i_width);
                        $new_height = ceil($image_scale * $i_height);
                        //
                        $new_canves = imagecreatetruecolor($new_width, $new_height);
                        //
                        switch (strtolower(image_type_to_mime_type($type_image))) {
                            case 'image/png':
                                $white = imagecolorallocate($new_canves, 255, 255, 255);
                                imagefill($new_canves, 0, 0, $white);
                                $function_copy = 'imagejpeg';
                                break;
                            case 'image/gif':
                                $function_copy = 'imagegif';
                                break;
                            default:
                                $function_copy = 'imagejpeg';
                        }
                        // Resize Image
                        imagecopyresampled($new_canves, $new_image, 0, 0, 0, 0, $new_width, $new_height, $i_width, $i_height);
                        // Enable interlancing
                        imageinterlace($new_canves, true);
                        //
                        $function_copy($new_canves, $image_info[$id]['path_storage'], $quality);
                        //
                        imagedestroy($new_canves);
                        //
                        imagedestroy($new_image);
                        /*
                        $fp = fopen($image_info[$id]['path_storage'], 'w');
                        fwrite($fp, $data);
                        fclose($fp);
                        */
                        //
                        $result[$id] = $image_info[$id];
                    }
                    //
                    curl_multi_remove_handle($mh, $c);
                    //
                    curl_close($c);
                }
                // all done
                curl_multi_close($mh);
            }
        }
        //
        return $result;
    }

    public static function downloadResizeImage($image_url)
    {
        try {
            //$type = explode(".", $image_url);
            //$extension = strtolower($type[sizeof($type)-1]);
            //$extension = (!in_array($ext, array("jpeg","png","gif"))) ? "jpeg" : $ext;
            $extension = 'jpeg';
            //
            $date = date('Y/m/d');
            //
            $dir_image = '/images/' . $date;
            //
            $upload_dir = UPLOAD_PATH . $dir_image;
            //
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
                chown($upload_dir, 'ad-user');
            }
            //
            $upload_path = realpath($upload_dir);
            //
            $hashed_image_name = md5($image_url . uniqid()) . '.' . $extension;
            //
            $dir_image .= '/' . $hashed_image_name;
            //
            $path_storage = $upload_path . '/' . $hashed_image_name;
            //
            $ch = curl_init($image_url);
            //
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
            //
            $data = curl_exec($ch);
            //
            if (empty($data)) {
                return '';
            }
            //
            curl_close($ch);
            //
            $image = imagecreatefromstring($data);
            //
            $width = imagesx($image);
            $height = imagesy($image);
            $thumb_width = Adx_Model_DynamicRemarketing::WIDTH_FIX;
            $thumb_height = Adx_Model_DynamicRemarketing::HEIGHT_FIX;
            //
            $original_aspect = $width / $height;
            $thumb_aspect = $thumb_width / $thumb_height;
            //
            if ($original_aspect >= $thumb_aspect) {
                // If image is wider than thumbnail (in aspect ratio sense)
                $new_height = $thumb_height;
                $new_width = $width / ($height / $thumb_height);
            } else {
                // If the thumbnail is wider than the image
                $new_width = $thumb_width;
                $new_height = $height / ($width / $thumb_width);
            }
            //
            $image_new = imagecreatetruecolor($thumb_width, $thumb_height);
            // Resize and crop
            imagecopyresampled($image_new,
                $image,
                0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                0, 0,
                $new_width, $new_height,
                $width, $height);

            //free resources
            ImageDestroy($image);
            //
            $func = "image" . $extension;
            $func($image_new, $path_storage);
            //
            ImageDestroy($image_new);
            //
            return $dir_image;
        } catch (Exception $e) {
            return '';
        }
    }

    public static function resizeImage($params)
    {
        $max_width = $params['max_width'];
        $max_height = $params['max_height'];
        $is_crop = false;

        if (strpos($params['imageSource'], UPLOAD_URL) !== false) {
            $params['imageSource'] = str_replace(UPLOAD_URL, UPLOAD_PATH, $params['imageSource']);
        }
        $source_file = $params['imageSource'];
        $tokens = pathinfo($params['imageSource']);
        $filename = $tokens['filename'];

        if (Adx_Utils::str_len($filename) !== 32) {
            $filename = md5($filename . uniqid());
        }

        $basename = $filename . '.png';

        $date = date('Y/m/d');

        $uploadDir = UPLOAD_PATH . '/images/' . $date;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
            chown($uploadDir, 'ad-user');
        }

        $image_url = 'images/' . $date . '/' . $basename;
        $image_src = $uploadDir . '/' . $basename;
        $dest_file = $uploadDir . '/' . $basename;
        $src_x = $src_y = 0;
        list($width, $height, $type, $attr) = @getimagesize($source_file);
        if (($width * $height) == 0) return false;
        if (($max_width * $max_height) == 0) return false;
        $new_width = $width;
        $new_height = $height;
        if (($width > $max_width) && ($width / $max_width >= $height / $max_height)) {
            $new_width = $max_width;
            $new_height = (int)($height * $max_width / $width);
        } else if (($height > $max_height) && ($width / $max_width <= $height / $max_height)) {
            $new_height = $max_height;
            $new_width = (int)($width * $max_height / $height);
        }
        switch ($type) {
            case 1; //Gif
                $im_source = @imagecreatefromgif($source_file);
                break;
            case 2; //Jpg
                $im_source = @imagecreatefromjpeg($source_file);
                break;
            case 3; //Png
                $im_source = @imagecreatefrompng($source_file);
                break;
            default;
                $im_source = @imagecreatefromjpeg($source_file);
                break;
        }
        if ($is_crop && $width > $height) {
            $new_width = $max_width;
            $new_height = $max_height;

            $src_x = round(($width - $new_width) / 2);

            $src_y = round(($height - $new_height) / 2);

            if ($src_x < 0)
                $src_x = 0;
            if ($src_y < 0)
                $src_y = 0;
            $width = $max_width;
            $height = $max_height;

        }

        $im_dest = @imagecreatetruecolor($new_width, $new_height);

        if ($type == 3) {
            @imagealphablending($im_dest, false);
            @imagesavealpha($im_dest, true);
            @imagealphablending($im_source, true);
        } else {
            $background = @imagecolorallocate($im_dest, 0, 0, 0);
            @imagefill($im_dest, 0, 0, $background);
        }

        @imagecopyresampled($im_dest, $im_source, 0, 0, $src_x, $src_y, $new_width, $new_height, $width, $height);

        if ($im_dest)
            $v_ok = true;
        else
            $v_ok = false;

        @unlink($dest_file);
        if ($type == 3) {
            @imagepng($im_dest, $dest_file);
        } else {
            @imagejpeg($im_dest, $dest_file, 90);
        }

        @imagedestroy($im_dest);
        @imagedestroy($im_source);
        return array(
            'image_src' => $uploadDir . '/' . $basename,
            'image_url' => $image_url,
            'extension' => $tokens['extension'],
        );
        // return $v_ok;
    }

    public static function timeFrameConvert($params = array())
    {
        $day = isset($params['day']) ? $params['day'] : date("Y-m-d");
        $hour = isset($params['hour']) ? $params['hour'] : date("H");
        $day_of_week = date('w', strtotime($day)) + 1;
        $current_time_frame = $hour + ($day_of_week - 1) * 24;
        $max_time_frame = $current_time_frame + CONST_TIME_ZONE;
        $min_time_frame = $current_time_frame - CONST_TIME_ZONE < 0 ? $current_time_frame - CONST_TIME_ZONE + 168 + 1 : $current_time_frame - CONST_TIME_ZONE;
        $i = $min_time_frame;
        $arr = array();
        do {
            $i++;
            if ($i == 169) {
                $i = 0;
            }
            $arr[] = $i;
        } while ($i != $max_time_frame);
        return $arr;
    }

    public static function cutStr($p_str, $p_start, $p_end)
    {
        if ($p_start != "") {
            $v_start_post = strpos($p_str, $p_start);
            if ($v_start_post === false) return "";
            $p_str = substr($p_str, $v_start_post + strlen($p_start));
            if ($p_end == "") return $p_str;
            $v_end_post = strpos($p_str, $p_end);
            if ($v_end_post === false) return "";
            $p_str = substr($p_str, 0, $v_end_post);
            return $p_str;
        } else {
            if ($p_end != "") {
                $v_end_post = strpos($p_str, $p_end);
                if ($v_end_post === false) return "";
                $p_str = substr($p_str, 0, $v_end_post);
                return $p_str;
            } else {
                return "";
            }
        }
    }

    public static function parseURLInfo($url)
    {
        $info = array(
            'title' => '',
            'sku' => '',
            'images' => array(),
            'price' => '',
            'discount_price' => '',
        );
        $product_title = '';
        $product_id = '';
        $product_sku = '';
        $arr_images = array();
        $product_price = 0;
        $product_discount_price = 0;
        $product_categories = '';

        if ($url && strpos($url, 'lazada.vn')) {
            $buffer = self::fileGetContents(urldecode($url));

            $discount_price_partern = "/\<span id=\"special_price_box\"\>.*\<\/span\>/";
            preg_match($discount_price_partern, $buffer, $discount_price);

            $price_partern = "/\<span id=\"price_box\"\>.*\<\/span\>/";
            preg_match($price_partern, $buffer, $price);

            $image_partern = "/data-swap-image=\".+.jpg\"/";
            preg_match_all($image_partern, $buffer, $images);

            $title_partern = "/data-title=\".+\"/";
            preg_match($title_partern, $buffer, $title);

            $sku_partern = "/\"sku\"\:\".+\",\"name/";
            preg_match($sku_partern, $buffer, $sku);

            $id_partern = "/\"product\"\:\{\"id\"\:\".+\",\"sku/";
            preg_match($id_partern, $buffer, $id);

            if (isset($id[0])) {
                preg_match("/\d+/", $id[0], $product_id);
                $product_id = $product_id[0];
            }
            if (isset($title[0])) {
                $product_title = str_replace(array('data-title="', '"'), '', $title[0]);
            }
            if (isset($sku[0])) {
                $product_sku = trim(str_replace(array('"sku":"', '"', 'name', ','), '', $sku[0]));
            }
            if (isset($images)) {
                foreach ($images as $image) {
                    $arr_images[] = str_replace(array('data-swap-image="', '"'), '', $image);
                }
            }
            if (isset($price[0])) {
                $product_price = str_replace(array(' ', ','), '', strip_tags($price[0]));
            }
            if (isset($discount_price[0])) {
                $product_discount_price = strip_tags($discount_price[0]);
            }
            // get category
            $category_content = Adx_Utils::cutStr($buffer, '<div class="header__breadcrumb__wrapper">', '</div>');
            $category_partern = "/title.+\"/";
            preg_match_all($category_partern, $category_content, $categories);
            if (isset($categories[0])) {
                $arr_categories = array();
                foreach ($categories[0] as $idx => $category) {
                    if ($idx > 2) {
                        continue;
                    }
                    $arr_categories[] = str_replace(array('title="', '"'), '', $category);
                }
                if ($arr_categories) {
                    $product_categories = implode("\t", $arr_categories);
                }
            }
            $info = array(
                'id' => $product_id,
                'title' => $product_title,
                'sku' => $product_sku,
                'categories' => $product_categories,
                'images' => $arr_images,
                'price' => $product_price,
                'discount_price' => $product_discount_price,
            );
        }

        if ($url && strpos($url, 'zoca.vn')) {
            $buffer = self::fileGetContents(urldecode($url));

            $id_partern = "/\<div class=\"zar-discount zar-masp.+/";
            preg_match($id_partern, $buffer, $id);

            $title_partern = "/product_name.+/";
            preg_match($title_partern, $buffer, $title);

            $discount_price_partern = "/\<div class=\"zar-price.+/";
            preg_match($discount_price_partern, $buffer, $discount_price);

            $price_partern = "/\<div class=\"zar-oldprice.+/";
            preg_match($price_partern, $buffer, $price);

            $image_partern = "/zoom-tiny-image.+.jpg/";
            preg_match_all($image_partern, $buffer, $images);

            $product_title = '';
            $product_id = '';
            $product_sku = '';
            $arr_images = array();
            $product_price = 0;
            $product_discount_price = 0;

            if (isset($id[0])) {
                $product_id = str_replace(array('Mã sản phẩm :', ' ', ','), '', strip_tags($id[0]));
            }
            if (isset($title[0])) {
                $product_title = str_replace(array('product_name" type = "hidden" value = "', '">'), '', $title[0]);
            }
            if (isset($images)) {
                foreach ($images as $image) {
                    $arr_images[] = str_replace(array('zoom-tiny-image" src="'), '', $image);
                }
            }
            if (isset($price[0])) {
                $product_price = str_replace(array(' ', ','), '', strip_tags($price[0]));
            }
            if (isset($discount_price[0])) {
                $product_discount_price = strip_tags($discount_price[0]);
            }
            $info = array(
                'id' => $product_id,
                'title' => $product_title,
                'sku' => '',
                'categories' => '',
                'images' => $arr_images,
                'price' => $product_price,
                'discount_price' => $product_discount_price,
            );
        }

        $buffer = Adx_Utils::fileGetContents(urldecode($url));
        $buffer = str_ireplace(array('<img ', '</img>', "\n", "\r", "\t"), array('<e_img ', "</e_img>", '', '', ''), $buffer);
        $buffer = preg_replace(array('/ {2,}/', '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s', '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '#<script(.*?)>(.*?)</script>#is', '#<noscript(.*?)>(.*?)</noscript>#is'), array(' ', '', '>', '<', '\\1', '', ''), $buffer);
        $buffer = strip_tags($buffer, '<e_img>,<title>,<meta>');
        $buffer = str_replace('<e_img', "\n\r<e_img", $buffer);

        $title_partern = "/<title>(.*)<\/title>/";
        preg_match($title_partern, $buffer, $title);

        if (isset($title[0])) {
            $product_title = trim(strip_tags($title[0]));
        }

        $image_partern = "/src=\"(http|https)\:\/\/.+\.(jpg|png|jpeg)/";
        preg_match_all($image_partern, $buffer, $images);
        if (isset($images) && !$arr_images) {
            foreach ($images as $idx => $image) {
                $arr_images[] = str_replace(array('src="', '"'), '', $image);
            }
        }
        $info = array(
            'id' => $product_id,
            'title' => $product_title,
            'sku' => $product_sku,
            'categories' => $product_categories,
            'images' => $arr_images,
            'price' => $product_price,
            'discount_price' => $product_discount_price,
        );
        return $info;
    }

    /**
     * @param $string
     * @param $length
     * @return string
     */

    public static function splitString($string, $length)
    {
        if (strlen($string) > $length) {
            return mb_substr($string, 0, $length, 'UTF-8') . "...";
        } else {
            return $string;
        }
    }

    public static function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public static function multiRequest($data, $options = array())
    {
        // array of curl handles
        $curly = array();
        // data to be returned
        $result = array();

        // multi handle
        $mh = curl_multi_init();

        // loop through $data and create curl handles
        // then add them to the multi-handle
        foreach ($data as $id => $d) {

            $curly[$id] = curl_init();

            $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;

            curl_setopt($curly[$id], CURLOPT_URL, $url);
            curl_setopt($curly[$id], CURLOPT_HEADER, false);
            curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curly[$id], CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
            curl_setopt($curly[$id], CURLOPT_ENCODING, "");
            curl_setopt($curly[$id], CURLOPT_AUTOREFERER, true);
            curl_setopt($curly[$id], CURLOPT_SSL_VERIFYPEER, false);

            // post?
            if (is_array($d)) {
                if (!empty($d['post'])) {
                    curl_setopt($curly[$id], CURLOPT_POST, 1);
                    curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
                }
            }

            // extra options?
            if (!empty($options)) {
                curl_setopt_array($curly[$id], $options);
            }

            curl_multi_add_handle($mh, $curly[$id]);
        }

        // execute the handles
        $running = null;
        do {
            // execute curl requests
            curl_multi_exec($mh, $running);
            // block to avoid needless cycling until change in status
            curl_multi_select($mh);
            // check flag to see if we're done
        } while ($running > 0);

        // get content and remove handles
        foreach ($curly as $id => $c) {
            // handle error
            if (curl_errno($c)) {
                $result[$id] = null;
            } else {
                $result[$id] = curl_multi_getcontent($c);
            }
            // close individual handle
            curl_multi_remove_handle($mh, $c);
        }

        // all done
        curl_multi_close($mh);

        return $result;
    }

    public static function multiOriginalURL($data, $options = array())
    {
        // array of curl handles
        $curly = array();
        // data to be returned
        $result = array();

        // multi handle
        $mh = curl_multi_init();

        // loop through $data and create curl handles
        // then add them to the multi-handle
        foreach ($data as $id => $d) {
            //
            $curly[$id] = curl_init();
            //
            $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
            //
            curl_setopt($curly[$id], CURLOPT_URL, $url);
            curl_setopt($curly[$id], CURLOPT_HEADER, true);
            curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curly[$id], CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
            curl_setopt($curly[$id], CURLOPT_ENCODING, "");
            curl_setopt($curly[$id], CURLOPT_AUTOREFERER, true);
            curl_setopt($curly[$id], CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curly[$id], CURLOPT_FOLLOWLOCATION, true);

            // post?
            if (is_array($d)) {
                if (!empty($d['post'])) {
                    curl_setopt($curly[$id], CURLOPT_POST, 1);
                    curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
                }
            }

            // extra options?
            if (!empty($options)) {
                curl_setopt_array($curly[$id], $options);
            }

            curl_multi_add_handle($mh, $curly[$id]);
        }

        // execute the handles
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);

        // get content and remove handles
        foreach ($curly as $id => $c) {
            $httpStatus = curl_getinfo($c, CURLINFO_HTTP_CODE);
            //
            if ($httpStatus < 300 || $httpStatus >= 400) {
                $result[$id] = $data[$id];
            } else {
                //
                $content = curl_multi_getcontent($c);
                // look for a location: header to find the target URL
                if (preg_match('/location: (.*)/i', $content, $r)) {
                    $location = trim($r[1]);
                    // if the location is a relative URL, attempt to make it absolute
                    if (preg_match('/^\/(.*)/', $location)) {
                        $baseURL = '';
                        $urlParts = parse_url($data[$id]);
                        //
                        if ($urlParts['scheme']) {
                            $baseURL = $urlParts['scheme'] . '://';
                        }
                        //
                        if ($urlParts['host']) {
                            $baseURL .= $urlParts['host'];
                        }
                        //
                        if ($urlParts['port']) {
                            $baseURL .= ':' . $urlParts['port'];
                        }
                        //
                        $result[$id] = $baseURL . $location;
                    } else {
                        $result[$id] = $location;
                    }
                }
            }
            curl_multi_remove_handle($mh, $c);
        }

        // all done
        curl_multi_close($mh);

        return $result;
    }

    public static function checkUrlFormat($string)
    {
        if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $string)) {
            return 1;
        }
        return 0;
    }

    public static function multiRedownloadUpdateImage($dataSource, $dataTarget, $uploadTarget)
    {
        // array of curl handles
        $curly = array();
        // data to be returned
        $result = array();
        //
        $image_info = array();
        // multi handle
        $mh = curl_multi_init();

        // loop through $data and create curl handles
        // then add them to the multi-handle
        foreach ($dataSource as $id => $image_url) {
            //
            $type = explode(".", $image_url);
            $ext = strtolower($type[sizeof($type) - 1]);
            $extension = (!in_array($ext, array("jpg", "jpeg", "png", "gif"))) ? "jpeg" : $ext;
            //
            $upload_dir = UPLOAD_PATH . $uploadTarget[$id];

            //
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
                //chown($upload_dir, 'ad-user');
            }

            //
            $upload_path = realpath($upload_dir);
            //
            $image_info[$id] = array(
                'extension' => $extension,
                'path_storage' => $upload_path . '/' . $dataTarget[$id],
                'dir_image' => '/' . $uploadTarget[$id] . $dataTarget[$id]
            );
            //
            $curly[$id] = curl_init();
            //
            curl_setopt($curly[$id], CURLOPT_URL, $image_url);
            curl_setopt($curly[$id], CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curly[$id], CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($curly[$id], CURLOPT_TIMEOUT, 20);
            curl_setopt($curly[$id], CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');

            curl_multi_add_handle($mh, $curly[$id]);
        }

        // execute the handles
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);

        // get content and remove handles
        foreach ($curly as $id => $c) {
            $dataSource = curl_multi_getcontent($c);
            if (empty($dataSource)) {
                $result[$id] = '';
            } else {
                //
                $image = imagecreatefromstring($dataSource);

                $width = imagesx($image);
                $height = imagesy($image);

                //
                $image_new = imagecreatetruecolor($width, $height);
                // Resize and crop
                imagecopyresampled($image_new,
                    $image,
                    0,
                    0,
                    0,
                    0,
                    $width,
                    $height,
                    $width,
                    $height
                );

                //free resources
                ImageDestroy($image);
                //
                $func = "image" . $image_info[$id]['extension'];
                $func($image_new, $image_info[$id]['path_storage']);
                //
                ImageDestroy($image_new);
                //
                $result[$id] = $image_info[$id]['dir_image'];
            }

            curl_multi_remove_handle($mh, $c);
        }

        // all done
        curl_multi_close($mh);
    }

    public static function customFormatDate($date, $current_format = "Y-m-d", $new_format = "d/m/Y")
    {
        if (empty($date)) {
            return '';
        }

        return DateTime::createFromFormat($current_format, trim($date))->format($new_format);
    }

    public static function sortBydMy($arr_object)
    {
        $cmp = function ($a, $b) {
            return strtotime($a->date_name) == strtotime($b->date_name) ? 0 : (strtotime($a->date_name) > strtotime($b->date_name)) ? 1 : -1;
        };
        usort($arr_object, $cmp);
        unset($cmp);

        return $arr_object;
    }

    public static function getFirstLastDate($arr_object)
    {
        $minDate = strtotime($arr_object[0]->date_name);
        $maxDate = strtotime($arr_object[0]->date_name);

        $result = array('firstDate' => $arr_object[0], 'lastDate' => $arr_object[0]);

        foreach ($arr_object as $index => $object) {
            $value = strtotime($object->date_name);
            if ($value < $minDate) {
                $minDate = $value;
                $result['firstDate'] = $arr_object[$index];
                continue;
            }
            if ($value > $maxDate) {
                $maxDate = $value;
                $result['lastDate'] = $arr_object[$index];
            }
        }

        return $result;
    }

    public static function priceSellPriceDiscount($price, $unit_price, $discount, $discount_type = '')
    {
//        1.	Unit_price = null + discount = null => price_discount = price
//        2.	Unit_price # null + discount = null => price = price_discount = unit_price
//        3.	Unit_price # null + discount # null => price = unit_price, price_discount = unit_price - (unit_price * discount / 100)
//        4.	Unit_price = null + discount # null => price_discount = price - (price * discount / 100)

        $price = !empty($price) ? (int)$price : 0;
        $unit_price = !empty($unit_price) ? (int)$unit_price : 0;
        $discount = !empty($discount) ? (int)$discount : 0;

        $result = array(
            'price_sell' => $price,
            'price_discount' => $price
        );

        if ($unit_price != 0 && $discount == 0) {
            $result = array(
                'price_sell' => $unit_price,
                'price_discount' => $unit_price
            );
        } else if ($unit_price != 0 && $discount != 0) {
            $result = array(
                'price_sell' => $unit_price,
                'price_discount' => $unit_price - ($unit_price * $discount / 100)
            );
        } else if ($unit_price == 0 && $discount != 0) {
            $result = array(
                'price_sell' => $price,
                'price_discount' => $price - ($price * $discount / 100)
            );
        }

        return $result;
    }

    public static function setDataCache($params, $class, $function, $data)
    {
        if (!isset($params['caching']) || empty($params['caching'])) {
            return;
        }
        //
        $time_expire = isset($params['time_expire']) && !empty($params['time_expire']) ? $params['time_expire'] : 0;
        //
        $keyCache = self::buildKeyCache($params, $class, $function);
        //
        try {
            //
            $redis = Adx_Nosql_Redis::getInstance('caching');
            //
            $redis->SET($keyCache, serialize($data));
            //
            if ($time_expire != 0) {
                $redis->EXPIRE($keyCache, $time_expire);
            }
        } catch (Exception $ex) {
            return;
        }
    }

    public static function getDataCache($params, $class, $function)
    {
        if (!isset($params['caching']) || empty($params['caching'])) {
            return '';
        }
        //
        $keyCache = self::buildKeyCache($params, $class, $function);
        //
        try {
            //
            $redis = Adx_Nosql_Redis::getInstance('caching');
            //
            $data = $redis->GET($keyCache);
            //
            if ($data) {
                return unserialize($data);
            } else {
                return '';
            }
        } catch (Exception $ex) {
            return '';
        }
    }

    public static function buildObjectNameCaching($object_name)
    {
        $object_id = '';
        switch ($object_name) {
            case 'creative':
                $object_id = 'creative_id';
                break;
            case 'zone':
                $object_id = 'zone_id';
                break;
            case 'website':
                $object_id = 'website_id';
                break;
            case 'placement':
                $object_id = 'placement_id';
                break;
            case 'channel':
                $object_id = 'channel_id';
                break;
            case 'user':
                $object_id = 'user_id';
                break;
            case 'campaign':
                $object_id = 'campaign_id';
                break;
            case 'section':
                $object_id = 'section_id';
                break;
            case 'topic':
                $object_id = 'topic_id';
                break;
            case 'interest':
                $object_id = 'interest_id';
                break;
            case 'product':
                $object_id = 'product_id';
                break;
            case 'cate':
                $object_id = 'cate_id';
                break;
            case 'merchant_cate':
                $object_id = 'mer_cate_id';
                break;
            case 'location':
                $object_id = 'location_id';
                break;
            case 'resource':
                $object_id = 'resource_id';
                break;
            case 'browser':
                $object_id = 'browser_id';
                break;
            case 'device':
                $object_id = 'device_id';
                break;
            case 'os':
                $object_id = 'os_id';
                break;
            case 'inmarket':
                $object_id = 'inmarket_id';
                break;
            case 'remarketing':
                $object_id = 'remarketing_id';
                break;
            case 'contract':
                $object_id = 'contract_id';
                break;
            case 'country':
                $object_id = 'location_id';
                break;
            case 'age':
                $object_id = 'age_range_id';
                break;
        }

        return $object_id;
    }

    public static function getCachingStatistic($object_name, $data = array())
    {
        try {
            //
            $data_cache = array();
            $data_cache_id = array();
            $data_id = array();
            //
            $redis = Adx_Nosql_Redis::getInstance('caching');

            //
            $redis->MULTI();
            //
            $object_info = Adx_Model_Caching::getInfoObject($object_name);

            //
            foreach ($data as $item) {
                //
                if ($object_name == 'user' && isset($item['advertiser_id'])) {
                    $item['user_id'] = $item['advertiser_id'];
                } else if ($object_name == 'user' && isset($item['publisher_id'])) {
                    $item['user_id'] = $item['publisher_id'];
                }
                //
                if (isset($item[$object_info['private_key']])) {
                    $data_id[] = $item[$object_info['private_key']];

                    //
                    $keyCache = 'cache_' . $object_name . ':' . $item[$object_info['private_key']];
                    //
                    $redis->GET($keyCache);
                }
            }
            //
            $result = $redis->EXEC();

            //
            foreach ($result as $index => $item) {
                if (empty($item)) {
                    continue;
                }
                //
                $unserialize_item = unserialize($item);
                //
                $data_cache[$unserialize_item->{$object_info['private_key']}] = $unserialize_item;
                //
                $data_cache_id[] = $unserialize_item->{$object_info['private_key']};
            }
            //
            $arr_object_id = array_diff($data_id, $data_cache_id);
            //
            if (!empty($arr_object_id)) {
                //
                $data_statistic = self::setCachingStatistic($object_name, $arr_object_id);
                if (!empty($data_statistic)) {
                    foreach ($data_statistic as $item) {
                        $data_cache[$item->{$object_info['private_key']}] = $item;
                    }
                }
            }

            return $data_cache;

        } catch (Exception $ex) {
            return '';
        }
    }

    public static function getCachingObject($object_name, $data)
    {
        try {
            //
            $data_cache = array();
            $data_cache_id = array();
            //
            $redis = Adx_Nosql_Redis::getInstance('caching');

            //
            $redis->MULTI();

            //
            if (!empty($data)) {
                foreach ($data as $object_id) {
                    $keyCache = 'cache_' . $object_name . ':' . $object_id;
                    //
                    $redis->GET($keyCache);
                }

                //Result
                $result = $redis->EXEC();


                foreach ($result as $index => $item) {
                    if (empty($item)) {
                        continue;
                    }

                    //
                    $unserialize_item = unserialize($item);

                    //
                    $data_cache[$unserialize_item->{$object_name . "_id"}] = $unserialize_item;
                    //
                    $data_cache_id[] = $unserialize_item->{$object_name . "_id"};
                }

                //
                $arr_object_id = array_diff($data, $data_cache_id);
                //
                if (!empty($arr_object_id)) {
                    //
                    $data_statistic = self::setCachingStatistic($object_name, $arr_object_id);
                    if (!empty($data_statistic)) {
                        foreach ($data_statistic as $item) {
                            $data_cache[$item->{$object_name . "_id"}] = $item;
                        }
                    }
                }
            }

            return $data_cache;

        } catch (Exception $ex) {
            return '';
        }
    }

    public static function setCachingStatistic($object_name, $arr_object_id)
    {
        //
        try {
            //
            $user = Zend_Registry::get("user");
            $object_info = Adx_Model_Caching::getInfoObject($object_name);
            //
            $result = Adx_Utils::autoReconnectionProcess(
                'Adx_DAO_Caching',
                $object_info['function_name'],
                array(
                    'network_id' => $user->network_id,
                    $object_info['private_key'] => is_array($arr_object_id) ? $arr_object_id : array($arr_object_id)
                )
            );

            $data = isset($result['rows']) && !empty($result['rows']) ? $result['rows'] : array();

            //
            $redis = Adx_Nosql_Redis::getInstance('caching');
            //
            $redis->MULTI();
            //
            foreach ($data as $item) {
                //
                $keyCache = 'cache_' . $object_name . ':' . $item->{$object_info['private_key']};
                //
                $redis->SET($keyCache, serialize($item));
            }
            //
            $redis->EXEC();

            return $data;

        } catch (Exception $ex) {
            return;
        }
    }

    public static function arrayValuesRecursive($array)
    {
        $arrayValues = array();

        foreach ($array as $value) {
            if (is_scalar($value) OR is_resource($value)) {
                $arrayValues[] = $value;
            } elseif (is_array($value)) {
                $arrayValues = array_merge($arrayValues, self::arrayValuesRecursive($value));
            }
        }

        return $arrayValues;
    }

    public static function buildKeyCache($params, $class, $function)
    {
        $key = sha1(json_encode(array($params['user_id'], $params['network_id'], $class, $function)));

        return $key;
    }

    public static function formatNumberByShortcut($number)
    {
        if ($number >= 1000000000) {
            $n_format = round(($number / 1000000000)) . 'B';
        } else if ($number >= 1000000) {
            // Anything less than a billion
            $n_format = round(($number / 1000000)) . 'M';
        } else if ($number >= 1000) {
            // Anything less than a billion
            $n_format = round(($number / 1000)) . 'K';
        } else {
            // Anything less than a billion
            $n_format = round(($number / 1));
        }
        return $n_format;

    }

    public static function remove_accent($fragment)
    {
        //
        if (php_sapi_name() == 'cli') {
            $fragment = iconv('UTF-8', 'US-ASCII//TRANSLIT', $fragment);
        }

        //
        $translate_symbols = array(
            '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#',
            '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
            '#(ç)#',
            '#(ì|í|ị|ỉ|ĩ)#',
            '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
            '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#',
            '#(ỳ|ý|ỵ|ỷ|ỹ)#',
            '#(đ)#',
            '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#',
            '#(Ç)#',
            '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
            '#(Ì|Í|Ị|Ỉ|Ĩ)#',
            '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#',
            '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#',
            '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#',
            '#(Đ)#',
            '#(_|-|/|\*|\?|\`|\~|\!|\@|\#|\$|\%|\^|\&|\(|\)|\+|\{|\=|\;|\:|\'|\"|\,|\<|\>|\}|\[|\]|\||\\\)#'
            //'/[^a-zA-Z0-9\-\_]/',
        );

        //
        $replace = array(
            'a',
            'e',
            'c',
            'i',
            'o',
            'u',
            'y',
            'd',
            'A',
            'C',
            'E',
            'I',
            'O',
            'U',
            'Y',
            'D',
            ' '
        );

        //
        $fragment = preg_replace($translate_symbols, $replace, $fragment);

        $fragment = preg_replace('/(-)+/', ' ', $fragment);

        //
        return strtolower($fragment);
    }

    public static function convertCapture($data)
    {
        $image = imagecreatefrompng($data);
        $background = imagecreatetruecolor(imagesx($image), imagesy($image));
        imagefill($background, 0, 0, imagecolorallocate($background, 255, 255, 255));
        imagealphablending($background, TRUE);
        imagecopy($background, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        imagedestroy($image);
        imagejpeg($background);
        return $background;
    }

    public static function getMultiOriginalURL($user_id, $network_id, $arr_product_link)
    {
        $config = Adx_Config::get('application')['redis']['cacheadx'];
        $redis = new Redis();
        $redis->connect($config['host'], $config['port']);
        //
        $arr_product_link_remake = array();
        $arr_product_original_link_cache = array();
        foreach ($arr_product_link as $key => $item_product_link) {
            //
            $product_link_original = $redis->hget('user:' . $user_id . ':network:' . $network_id . ':product_link', crc32($item_product_link));
            //
            if (empty($product_link_original)) {
                $arr_product_link_remake[$key] = $item_product_link;
            } else {
                $arr_product_original_link_cache[$key] = $product_link_original;
            }
        }
        //
        if (!empty($arr_product_link_remake)) {
            $arr_product_original_link = self::multiOriginalURL($arr_product_link_remake);
            //
            if (!empty($arr_product_original_link)) {
                foreach ($arr_product_original_link as $key => $item_product_original_link) {
                    $redis->hset('user:' . $user_id . ':network:' . $network_id . ':product_link', crc32($arr_product_link_remake[$key]), $item_product_original_link);
                }
                //
                if (empty($arr_product_original_link_cache)) {
                    $arr_product_original_link_cache = $arr_product_original_link;
                } else {
                    $arr_product_original_link_cache = array_merge_recursive($arr_product_original_link_cache, $arr_product_original_link);
                }
            }
        }

        $redis->close();

        if (count($arr_product_link) != count($arr_product_original_link_cache)) {
            return array();
        }

        return $arr_product_original_link_cache;
    }

    public static function changeKeyArray($array, $array_old_key, $new_key)
    {
        if (empty($array_old_key))
            return $array;
        $flag = false;
        foreach ($array_old_key as $old_key) {
            if (!array_key_exists($old_key, $array))
                continue;
            $flag = true;
            $keys = array_keys($array);
            $keys[array_search($old_key, $keys)] = $new_key;
            return array_combine($keys, $array);
        }

        if ($flag == false)
            return $array;
    }

    public static function T($message = 'Unknown')
    {
        $language = Zend_Registry::get('Zend_Translate');
        return (string)$language->translate($message);
    }

    public static function getTimeFrame($date)
    {
        //$date: Y-m-d H:00:00
        $int_date = strtotime($date);
        $day_of_week = date('N', $int_date) + 1;
        if ($day_of_week == 8) {
            $day_of_week = 1;
        }
        $hour_of_day = date('H', $int_date);
        $time_frame = 24 * ($day_of_week - 1) + $hour_of_day;
        return $time_frame;
    }

    public static function getNextDateTime($date, $format = 'Y-m-d H:00:00')
    {
        //$date: Y-m-d H:00:00
        $int_date = strtotime($date);
        $next_date_time = date($format, strtotime("+1 hour", $int_date));
        return $next_date_time;
    }

    public static function getPreviousDateTime($date, $format = 'Y-m-d H:00:00')
    {
        //$date: Y-m-d H:00:00
        $int_date = strtotime($date);
        $next_date_time = date($format, strtotime("-1 hour", $int_date));
        return $next_date_time;
    }

    public static function checkSSLDomain($domain)
    {
        $checked = -1;
        //
        if(empty($domain)){
            return $checked;
        }
        //
        $url = 'https://' . $domain;
        //
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //
        $output = curl_exec($ch);
        //
        if ($output === FALSE) {
            $checked = 0; //http
        } else {
            $checked = 1; //https
        }
        //free up the curl handle
        curl_close($ch);
        //
        return $checked;
    }
}