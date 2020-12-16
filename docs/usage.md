Usage
===

This section will guide you through the basic usage of this package. By the end of this section, you should be able to
setup your own listeners for any Lokalise event.

> 💡 Found an issue or is this section missing anything? Feel free to open a
> [PR](https://github.com/craftzing/laravel-lokalise-webhooks/compare) or
> [issue](https://github.com/craftzing/laravel-lokalise-webhooks/issues/new).

## 📡 Handling Lokalise events

Whenever Lokalise calls the webhook handling route you defined, the request will be validated using the configured
Lokalise `X-Secret`. When the request is valid, this package will fire a `lokalise-webhooks::<name-of-the-event>` event.
The payload of the event will be a `Spatie\WebhookClient\Models\WebhookCall` instance that was created for the incoming 
request.

To hook into to the event, you can register a listener for it in your application's `EventServiceProvider`:
```php
use Craftzing\Laravel\LokaliseWebhooks\LokaliseEvent;

protected $listen = [
    // The constant below is an alias to 'lokalise-webhooks::project.languages.added'.
    // You can use our predefined constants or write the full event name yourself.
    LokaliseEvent::PROJECT_LANGUAGES_ADDED => [
        App\Listeners\HandleAddedLanguage::class,
    ],
];
```

Your listener would look something like this:
```php 
namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookClient\Models\WebhookCall;

final class HandleAddedLanguage implements ShouldQueue
{
    public function handle(WebhookCall $webhookCall): void
    {
        // You can access the payload of the webhook call with `$webhookCall->payload`...
        // Do whatever you need to do when the event occurs...
    }
}
```

This is just one of the ways you can hook into events in Laravel. To learn more about event handling in Laravel, head 
over to their [docs](https://laravel.com/docs/8.x/events#registering-events-and-listeners).

> 💡 Though it's not mandatory, we highly recommend queueing your event listeners to minimise the response time of 
> the request. 

## 🌐 Working with webhooks on your local environment

By default, your localhost is not accessible to the outside world. If you were to setup the webhook URL in the Lokalise
integration settings with a link to your localhost, Lokalise will not allow it as it will try to reach the URL with a 
ping event.

You can, however, expose your localhost using a tunneling service. We highly recommend using either 
[Ngrok](https://ngrok.com) or [Expose](https://beyondco.de/docs/expose/introduction). Once you've got the tunnel set up, 
you should be able to use the publicly accessible as the webhook URL (f.e. `https://92832de0.ngrok.io/your-route`).

---

[⏪ Getting started](getting-started.md) | [Common event listeners ⏩](common-event-listeners.md)
