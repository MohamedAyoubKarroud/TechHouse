<?php
// IP-based geolocation via free ip-api.com endpoint. Logs each pageview to analytics_visits.

class Geolocation
{
    public static function clientIp()
    {
        $keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($keys as $k) {
            if (!empty($_SERVER[$k])) {
                $parts = explode(',', $_SERVER[$k]);
                return trim($parts[0]);
            }
        }
        return '0.0.0.0';
    }

    public static function lookup($ip)
    {
        // Skip local / private IPs (XAMPP localhost).
        if ($ip === '127.0.0.1' || $ip === '::1'
            || substr($ip, 0, 8) === '192.168.'
            || substr($ip, 0, 3) === '10.') {
            return array('country' => 'Local', 'city' => 'Localhost');
        }

        $opts = array('http' => array('timeout' => 2));
        $ctx  = stream_context_create($opts);
        $url  = 'http://ip-api.com/json/' . urlencode($ip) . '?fields=status,country,city';
        $raw  = @file_get_contents($url, false, $ctx);
        if ($raw === false) {
            return array('country' => null, 'city' => null);
        }

        $data = json_decode($raw, true);
        if (!is_array($data) || !isset($data['status']) || $data['status'] !== 'success') {
            return array('country' => null, 'city' => null);
        }
        $country = isset($data['country']) ? $data['country'] : null;
        $city    = isset($data['city'])    ? $data['city']    : null;
        return array('country' => $country, 'city' => $city);
    }

    public static function logVisit()
    {
        $page = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        // Don't log asset requests.
        if (preg_match('#\.(css|js|png|jpg|jpeg|gif|svg|ico|woff2?)$#i', $page)) {
            return;
        }

        $ip  = self::clientIp();
        $geo = self::lookup($ip);
        $ua  = '';
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $ua = substr($_SERVER['HTTP_USER_AGENT'], 0, 255);
        }
        $uid = null;
        if (isset($_SESSION['user']['id'])) {
            $uid = $_SESSION['user']['id'];
        }

        $db = Database::connect();
        $stmt = $db->prepare(
            'INSERT INTO analytics_visits (user_id, ip, country, city, page, user_agent)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute(array($uid, $ip, $geo['country'], $geo['city'], substr($page, 0, 255), $ua));
    }
}
