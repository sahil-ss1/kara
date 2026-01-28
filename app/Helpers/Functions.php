<?php
    function genSKey($length = 10) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    function uniqueID(){
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $timestamp = base_convert( $timestamp, 10, 36 ); //equal with toString(36)
        $random = (float)rand()/(float)getrandmax(); //equal with Math.random()
        $random = base_convert( $random, 10, 36 );

        return $timestamp . substr($random,2,16);
    }

    function clearText($value){
        // Strip HTML Tags
        $clear = strip_tags($value);
        // Clean up things like &amp;
        $clear = html_entity_decode($clear);
        // Strip out any url-encoded stuff
        $clear = urldecode($clear);
        // Replace non-AlNum characters with space
        $clear = preg_replace('/[^A-Za-z0-9]/', ' ', $clear);
        // Replace Multiple spaces with single space
        $clear = preg_replace('/ +/', ' ', $clear);
        // Trim the string of leading/trailing space
        return trim($clear);
    }

    function decompress_zip($file, $target_folder){
        $zip = new ZipArchive;
        $zip->open($file);
        $zip->extractTo($target_folder);
        $zip->close();
    }

    // Function to get the client IP address
    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    function getGeoLocation($ip){
        $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
        $res ='';
        if ($details) {
            //if (property_exists($details, 'ip')) $res = $details->ip . PHP_EOL;
            //if (property_exists($details, 'hostname')) $res .= $details->hostname . PHP_EOL;
            if (property_exists($details, 'city')) $res .= $details->city . PHP_EOL;
            if (property_exists($details, 'country')) $res .= $details->country . PHP_EOL;
            if (isset($details->org)) $res .= $details->org . PHP_EOL;
        }
        return $res;
    }

    function saveToFile($content, $path) {
        //$path = dirname(__FILE__) . DIRECTORY_SEPARATOR . basename('data.txt');
        $message = sprintf('%s' . PHP_EOL, $content);
        return @file_put_contents($path, file_get_contents($path) . $message);
    }

    function URLtoPath($url) {
        $path = parse_url($url, PHP_URL_PATH);
        //To get the dir, use: dirname($path)
        return $_SERVER['DOCUMENT_ROOT'] . $path;
    }

    function notempty($var) {
        return ($var==="0"||$var);
    }

    function encrypt_url($string) {
        $key = "MAL_979805"; //key to encrypt and decrypts.
        $result = '';
        $test = [];
        for($i=0; $i<strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)+ord($keychar));

            $test[$char]= ord($char)+ord($keychar);
            $result.=$char;
        }

        return urlencode(base64_encode($result));
    }

    function decrypt_url($string) {
        $key = "MAL_979805"; //key to encrypt and decrypts.
        $result = '';
        $string = base64_decode(urldecode($string));
        for($i=0; $i<strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)-ord($keychar));
            $result.=$char;
        }
        return $result;
    }

    function gmp_base_convert(string $value, int $initialBase, int $newBase){
        return gmp_strval(gmp_init($value, $initialBase), $newBase);
    }

    function encodeUUID(string $uuid){
        return gmp_base_convert(str_replace('-', '', $uuid), 16, 62);
    }

    function decodeUUID(string $hashid) {
        return array_reduce(
            [20, 16, 12, 8],
            function ($uuid, $offset) {
                return substr_replace($uuid, '-', $offset, 0);
            },
            str_pad(gmp_base_convert($hashid, 62, 16), 32, '0', STR_PAD_LEFT)
        );
    }

    function str_lreplace($search, $replace, $subject) {
        $pos = strrpos($subject, $search);
       if($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }
        return $subject;
    }

    function escapeJsonString($value) {
        # list from www.json.org: (\b backspace, \f formfeed)
        $escapers =     array("\\",     "/",   "\"",  "\n",  "\r",  "\t", "\x08", "\x0c", "'");
        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t",  "\\f",  "\\b", "\'");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }

    function checkExpire($expireDate){
        if ($expireDate) {
            $now = new DateTime("now");
            $expireDate = new DateTime($expireDate);
            $diff = $now->diff($expireDate)->format("%r%a");
            return $diff;
        }else return null;
    }

    function getDomainFromEmail($email){
        // make sure we've got a valid email
        if( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            // split on @ and return last value of array (the domain)
            $arr = explode( '@', strtolower($email) );
            return array_pop( $arr );
        }
    }

    function currency_formatter($amount, $currency) {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        return $formatter->formatCurrency(
            $amount,
            $currency // e.g. EUR, USD, INR
        );
    }
