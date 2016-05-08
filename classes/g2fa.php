<?php
/**
 * Created by IntelliJ IDEA.
 * User: taked
 * Date: 2016/05/04
 * Time: 19:05
 */

namespace G2FA;


class G2FA
{
    const SECRET_LENGTH = 16;
    const TIME_STEP = 30;
    const CODE_LENGTH = 6;

    public static function createSecret()
    {
        $base32_str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < self::SECRET_LENGTH; $i++) {
            $secret .= $base32_str[mt_rand(0, 31)];
        }
        return $secret;
    }

    public static function getQRCodeUrl($name, $secret)
    {
        $config = \Fuel\Core\Config::load('tsauthverify', true);
        $urlencoded_str = urlencode('otpauth://totp/' . $name . '?secret=' . $secret);
        if(isset($config['title']))
        {
            $urlencoded_str .= urlencode('&issuer=' . urlencode($config['title']));
        }
        return 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . $urlencoded_str;
    }

    public static function getCode($secret, $timeSlice = null)
    {
        if($timeSlice === null) $timeSlice = floor(time() / self::TIME_STEP );
        $secretKey = \Base32\Base32::decode($secret);
        $binaryTimestamp = pack('N*', 0) . pack('N*', $timeSlice);
        $hmac = hash_hmac('SHA1', $binaryTimestamp, $secretKey, true);
        $offset = ord(substr($hmac, -1)) & 0xF;
        $part = substr($hmac, $offset, 4);
        $unpack_value_array = unpack('N', $part);
        $unpack_value = $unpack_value_array[1];
        $seed = $unpack_value & 0x7FFFFFFF;
        $pow = pow(10, self::CODE_LENGTH);
        return str_pad($seed % $pow, self::CODE_LENGTH,  '0', STR_PAD_LEFT);
    }

    public static function verifyCode($secret, $code, $discrepancy = 1, $currentTimeSlice = null)
    {
        if($currentTimeSlice === null) $currentTimeSlice = floor(time() / self::TIME_STEP);
        for ($i =- $discrepancy; $i <= $discrepancy; $i++) {
            $calcCode = self::getCode($secret, $currentTimeSlice + $i);
            if ($calcCode == $code) return true;
        }
        return false;
    }

}