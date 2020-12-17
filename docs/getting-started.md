Getting started
===

This section will guide you through all necessary and optional installation and configuration steps. By the end of the 
section, you should be able to receive incoming requests from Lokalise. 

> 💡 Found an issue or is this section missing anything? Feel free to open a 
> [PR](https://github.com/craftzing/laravel-lokalise-webhooks/compare) or 
> [issue](https://github.com/craftzing/laravel-lokalise-webhooks/issues/new).

## ⚒️ Requirements

This package requires:
- [PHP](https://www.php.net/supported-versions.php) 7.4 or 8
- [Laravel](https://laravel.com) 7 or 8

Some features may have additional requirements. These will be listed in the according section of the documentation.

## 🧙 Installation

You can install this package using [Composer](https://getcomposer.org) by running the following command:
```bash
composer require craftzing/laravel-lokalise-webhooks
```

We're using [Laravel's package discovery](https://laravel.com/docs/8.x/packages#package-discovery) to automatically
register the service provider, so you don't have to register it yourself.

You can publish the package config file by running the command below, but it's not mandatory:
```bash
php artisan vendor:publish --provider="Craftzing\Laravel\LokaliseWebhooks\LokaliseWebhooksServiceProvider" --tag="config"
```

This package is built on top of Spatie's [Laravel webhook client](https://github.com/spatie/laravel-webhook-client).
If you're already using that package for any other webhook integrations, you can skip this next step. If you're not, 
you must run the following commands to publish and run its migrations:
```bash
php artisan vendor:publish --provider="Spatie\WebhookClient\WebhookClientServiceProvider" --tag="migrations"
php artisan migrate
```

## ⚙️ Configuration

Lokalise includes a `X-Secret` header with every webhook requests. That secret is specific to the webhook integration 
and enables us to verify that incoming requests originate from the webhook integration you've set up in the settings of
your Lokalise project (see https://app.lokalise.com/settings/{yourProjectId}/#integrations/rest-webhook).

Add the `X-Secret` header to your `.env`:
```dotenv
LOKALIZE_X_SECRET=<your-secret>
```

As Lokalise does not sign requests, the only way to ensure requests are coming from Lokalise is to set up IP 
restrictions. Only requests coming from a known Lokalise IP should have access to the webhook handling route. This 
behaviour is enabled by default, but you can turn it off for development purposes (for example when you're using a 
tunneling service to expose your local environment).

To turn off IP restrictions, add the following to your `.env`:
```dotenv
LOKALIZE_ENABLE_IP_RESTRICTIONS=false
```

> ⚠️ We highly discourage disabling IP restrictions on production environments. If your application sits behind a load 
> balancer or any intermediary (reverse) proxy, make sure to list it as a trusted proxy so we can access the real 
> end-client IP (see https://laravel.com/docs/8.x/requests#configuring-trusted-proxies).

Next, you must specify the route that will handle incoming Lokalise webhook calls. Head over to your app's routes file
and add the lines below. Under the hood, this will register a `POST` route with a request handler provided by this
package.
```php
use Illuminate\Support\Facades\Route;

Route::lokaliseWebhooks('your-route'); 
```

As Lokalise has no way to set a CSRF token, you must exclude this newly registered route from your app's CSRF
verification. Head over to the `VerifyCsrfToken` middleware and add the route to the `except` array:
```php
protected $except = [
    'your-route',
]; 
```

Now that the route's in place, we can configure it in the Webhook integration settings of your project on Lokalise. Head
over to the webhook integration settings (https://app.lokalise.com/settings/{yourProjectId}/#integrations/rest-webhook)
and fill out the `Webhook URL` field with the route you just added in your app.

Once you hit the `Enable integration` button, a ping request will be made to the provided webhook URL to ensure that it
works properly. Lokalise does not allow you to configure a webhook URL that does not return a successful response.

> ⚠️ If you're configuring the webhook URL with a route pointing to your localhost, Lokalise will not accept the route
> as your localhost is not accessible to the outside world. To learn more about working with webhooks on your local
> environment, have a look at our [documentation](usage.md#-working-with-webhooks-on-your-local-environment).

---

[Usage ⏩](usage.md)
