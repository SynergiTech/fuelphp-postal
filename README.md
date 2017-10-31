# FuelPHP Postal

FuelPHP-Postal is designed as a fully featured wrapper to the [Postal](https://github.com/atech/postal) email sending platform.

Currently it handles sending emails, with or without attachments, stores the message ID returned from Postal in a database, and also exposes a webhook receiver to handle notifications from Postal that messages have been delivered, opened etc.

## Installation

The recommended way to install `fuelphp-postal` is using [Composer](https://getcomposer.org/).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version of `fuelphp-postal`.

```bash
php composer.phar require synergitech/postal
```

You will then need database tables for storing the outbound email details and webhook notifications. We have provided FuelPHP migrations to make this easy.

```bash
php oil r migrate --packages=postal
```

## Configuration

You need to configure your app to point to your Postal instance and the address from which to send. A basic config looks like this:

```php
return [
    'url' => 'https://yourpostal.io',
    'api_key' => 'ABCDEFGHI123456790',
    'send-name' => 'Your App Name',
    'send-address' => 'noreply@yourapp.io',
    'reply-to' => 'reply@yourapp.io',
    'template_html' => 'email/generic/html',
    'template_text' => 'email/generic/text'
];
```

The `template_text` and `template_html` will be used by default as the view files into which your `$data` array will be merged using FuelPHP's built in View `render()` function.

You will need to add `postal` to the `config` and `package` autoloaders to ensure they are available to your classes.

You can also define an environment variable in your `.htaccess` file to send all emails to one address which will help you develop your app.

```
SetEnv EMAIL you@company.io
```

## Quick Sending email

You can call the `send()` function to send an email. Arguments after `$to` are optional.
```php
\Synergitech\Postal::send($subject, $body, $to, $to_name, $from, $data, $bcc);
```

## Webhooks

You can configure a webhook receiving URL within Postal. You need to create a controller within your FuelPHP project that calls:

```php
\Synergitech\Postal\Webhook::ProcessWebhook();
```

Example Controller file (be sure to allow unauthenticated requests to pass to this function):

```php
namespace Controller;

class Webhook extends \Controller_Rest
{
    public function action_postal()
    {
        \Synergitech\Postal\Webhook::ProcessWebhook();
    }
}
```
