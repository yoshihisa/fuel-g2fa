<?php

/**
 * Created by IntelliJ IDEA.
 * User: taked
 * Date: 2016/05/05
 * Time: 2:51
 */
use \Mockery as m;
class Test_Twostepauthverify extends \PHPUnit_Framework_TestCase
{
    public function testCreateSecret()
    {
        $this->assertEquals(16, strlen(\G2FA\G2FA::createSecret()));
    }

    public function testGetQRCodeUrl()
    {
        $mock = m::Mock('alias:Fuel\Core\Config');
        $mock->shouldReceive('load')->andReturn(null);
        $secret = \G2FA\G2FA::createSecret();
        $otpauth = urlencode('otpauth://totp/foo?secret=' . $secret);
        $expect = "https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=" . $otpauth;
        $actual = \G2FA\G2FA::getQRCodeUrl('foo', $secret);
        $this->assertEquals($expect, $actual);
    }
    /**
     * @runInSeparateProcess
     */
    public function testGetQRCodeUrlWithTitle()
    {
        $mock = m::Mock('alias:Fuel\Core\Config');
        $mock->shouldReceive('load')->andReturn(array('title' => 'TheMock'));
        $secret = \G2FA\G2FA::createSecret();
        $otpauth = urlencode('otpauth://totp/bar?secret=' . $secret . '&issuer=' . urlencode('TheMock'));
        $expect = "https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=" . $otpauth;
        $actual = \G2FA\G2FA::getQRCodeUrl('bar', $secret);
        $this->assertEquals($expect, $actual);
    }

    public function testGetCode()
    {
        $secret = 'OSWET3R3NTLTRJDB';
        $timeSlice = 48756793;
        $actual = \G2FA\G2FA::getCode($secret, $timeSlice);
        $this->assertEquals(6, strlen($actual));
        $this->assertEquals(388927, $actual);
    }

    public function testVerifyCode()
    {
        $secret = 'OSWET3R3NTLTRJDB';
        $timeSlice = 48756793;
        $actual = \G2FA\G2FA::verifyCode($secret, 388927, 1, $timeSlice);
        $this->assertEquals(true, $actual);
    }
}
