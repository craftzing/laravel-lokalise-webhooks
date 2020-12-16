Common event listeners
===

Beside allowing you to add your own event listeners, we also provide a limited number of (optional) common event 
listeners for you to re-use in any project.

> 💡 Have an idea for a common event listener? Feel free to open a
> [PR](https://github.com/craftzing/laravel-lokalise-webhooks/compare)!

## 🪣 Copying an exported project to storage 

Lokalise does not provide a content delivery network for your language files. Instead, they require you to copy the 
language files locally and serve your visitors with static files/your database content. To make it all nice and easy, 
they offer Amazon S3/Google CouldStorage integrations to automatically upload your language files to a bucket of your 
choice. This feature, however, is part of a paid plan which comes with a bunch of extra features you may not need.

Therefore, we added this `CopyExportedProjectToStorage` listener.  It basically replicates the paid integration and is 
developed according to their own guidelines (see 
[Developer docs](https://docs.lokalise.com/en/articles/1400500-downloading-files-and-using-webhooks)).

### Usage

To use the listener, you can register it in your app's `EventServiceProvider`:
```php
protected $subscribe = [
    \Craftzing\Laravel\LokaliseWebhooks\Subscribers\CopyExportedProjectToStorage::class,
];
```

> 💡 Note that you should register it as a subscriber. That way, it can register itself for the webhook events it 
> actually supports.

The listener will copy the exported project to the root of the default storage disk of your app. If you want it to use
any other disk, you should extend the binding in the IoC container through a service provider:
```php
use Craftzing\Laravel\LokaliseWebhooks\Subscribers\CopyExportedProjectToStorage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->extend(CopyExportedProjectToStorage::class, function () {
            return new CopyExportedProjectToStorage(Storage::disk('your-custom-disk'));
        });
    }
}
```

---

[⏪ Usage](usage.md)
