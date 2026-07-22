<?php
// OAuth 2.0 helpers for Google and Facebook social login.
// Pure PHP + cURL — no external libraries.

class OAuth
{
    // ---------- GOOGLE ----------

    public static function googleEnabled()
    {
        if (GOOGLE_CLIENT_ID !== '' && GOOGLE_CLIENT_SECRET !== '') {
            return true;
        }
        return false;
    }

    public static function googleAuthUrl()
    {
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state_google'] = $state;

        $params = array(
            'client_id'     => GOOGLE_CLIENT_ID,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
            'access_type'   => 'online',
            'prompt'        => 'select_account',
        );
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    // Returns array('id', 'email', 'name', 'avatar') or null on failure.
    public static function googleHandleCallback($code, $state)
    {
        if (empty($_SESSION['oauth_state_google']) || !hash_equals($_SESSION['oauth_state_google'], $state)) {
            return null;
        }
        unset($_SESSION['oauth_state_google']);

        $token = self::httpPost('https://oauth2.googleapis.com/token', array(
            'code'          => $code,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'grant_type'    => 'authorization_code',
        ));
        if (!$token || empty($token['access_token'])) {
            return null;
        }

        $profile = self::httpGet(
            'https://www.googleapis.com/oauth2/v3/userinfo',
            array('Authorization: Bearer ' . $token['access_token'])
        );
        if (!$profile || empty($profile['sub']) || empty($profile['email'])) {
            return null;
        }

        $name = $profile['email'];
        if (!empty($profile['name'])) {
            $name = $profile['name'];
        }
        $avatar = null;
        if (!empty($profile['picture'])) {
            $avatar = $profile['picture'];
        }

        return array(
            'id'     => (string)$profile['sub'],
            'email'  => $profile['email'],
            'name'   => $name,
            'avatar' => $avatar,
        );
    }

    // ---------- FACEBOOK ----------

    public static function facebookEnabled()
    {
        if (FACEBOOK_APP_ID !== '' && FACEBOOK_APP_SECRET !== '') {
            return true;
        }
        return false;
    }

    public static function facebookAuthUrl()
    {
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state_facebook'] = $state;

        $params = array(
            'client_id'    => FACEBOOK_APP_ID,
            'redirect_uri' => FACEBOOK_REDIRECT_URI,
            'state'        => $state,
            'scope'        => 'email,public_profile',
            'response_type'=> 'code',
        );
        return 'https://www.facebook.com/v18.0/dialog/oauth?' . http_build_query($params);
    }

    public static function facebookHandleCallback($code, $state)
    {
        if (empty($_SESSION['oauth_state_facebook']) || !hash_equals($_SESSION['oauth_state_facebook'], $state)) {
            return null;
        }
        unset($_SESSION['oauth_state_facebook']);

        $tokenUrl = 'https://graph.facebook.com/v18.0/oauth/access_token?' . http_build_query(array(
            'client_id'     => FACEBOOK_APP_ID,
            'client_secret' => FACEBOOK_APP_SECRET,
            'redirect_uri'  => FACEBOOK_REDIRECT_URI,
            'code'          => $code,
        ));
        $token = self::httpGet($tokenUrl);
        if (!$token || empty($token['access_token'])) {
            return null;
        }

        $profileUrl = 'https://graph.facebook.com/me?' . http_build_query(array(
            'fields'       => 'id,name,email,picture.type(large)',
            'access_token' => $token['access_token'],
        ));
        $profile = self::httpGet($profileUrl);
        if (!$profile || empty($profile['id'])) {
            return null;
        }

        // Facebook may not return an email if the user hides it — synthesize one.
        $email = 'fb_' . $profile['id'] . '@facebook.local';
        if (!empty($profile['email'])) {
            $email = $profile['email'];
        }

        $name = $email;
        if (!empty($profile['name'])) {
            $name = $profile['name'];
        }

        $avatar = null;
        if (isset($profile['picture']['data']['url'])) {
            $avatar = $profile['picture']['data']['url'];
        }

        return array(
            'id'     => (string)$profile['id'],
            'email'  => $email,
            'name'   => $name,
            'avatar' => $avatar,
        );
    }

    // ---------- HTTP helpers ----------

    private static function httpPost($url, $fields)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($fields),
            CURLOPT_HTTPHEADER     => array('Accept: application/json'),
            CURLOPT_TIMEOUT        => 15,
        ));
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code < 200 || $code >= 300 || $body === false) {
            return null;
        }
        $data = json_decode($body, true);
        if (is_array($data)) {
            return $data;
        }
        return null;
    }

    private static function httpGet($url, $headers = array())
    {
        $ch = curl_init($url);
        $allHeaders = array('Accept: application/json');
        foreach ($headers as $h) {
            $allHeaders[] = $h;
        }
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $allHeaders,
            CURLOPT_TIMEOUT        => 15,
        ));
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code < 200 || $code >= 300 || $body === false) {
            return null;
        }
        $data = json_decode($body, true);
        if (is_array($data)) {
            return $data;
        }
        return null;
    }
}
