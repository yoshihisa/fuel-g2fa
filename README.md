# fuel-g2fa

This package is 2-factor authentication with Google Authenticator for FuelPHP.

# License

MIT

# Configuration

config/g2fa.php

```php
return array(
    'title' => 'Your service name'
);
```

# Usage

## G2FA::createSecret()

Create the secret key at Base32 string.

## G2FA::getQRCodeUrl(string $name, string $secret)

Generate QR code for Google Authenticator.
$name will be used in Google Authenticator for identify.
Issuer is set in config file.(See ./config/g2fa.php)

## G2FA::getCode(string $secret [, int $timeSlice])

Generate One-time password.

## G2FA::verifyCode($secret, $code, [, $discrepancy, $timeSlice])

Check the one-time password.
