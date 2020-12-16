[![Laravel Lokalise webhooks](art/banner.jpg)](https://craftzing.com)

![Quality assurance](https://github.com/craftzing/laravel-lokalise-webhooks/workflows/Quality%20assurance/badge.svg)
![Code style](https://github.com/craftzing/laravel-lokalise-webhooks/workflows/Code%20style/badge.svg)
[![Test Coverage](https://api.codeclimate.com/v1/badges/881eb71372c1b12c18d5/test_coverage)](https://codeclimate.com/github/craftzing/laravel-lokalise-webhooks/test_coverage)
[![Maintainability](https://api.codeclimate.com/v1/badges/881eb71372c1b12c18d5/maintainability)](https://codeclimate.com/github/craftzing/laravel-lokalise-webhooks/maintainability)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat&color=4D6CB8)](https://github.com/craftzing/laravel-lokalise-webhooks/blob/master/LICENSE)

Lokalise offers a bunch of [webhooks](https://docs.lokalise.com/en/articles/3184756-webhooks) you can listen to in order
to get instant notifications about events happening in one of your Lokalise projects. This package aims at helping you
manage these Lokalise events within a Laravel app.

## 🔥 Features

- Automatic secret verification of incoming Lokalise event requests
- Integration of incoming Lokalise events within [Laravel's events architecture](https://laravel.com/docs/8.x/events)
- Out-of-the-box common event listeners

## 📚 Docs

- [Getting started](/docs/getting-started.md)
- [Usage](/docs/usage.md)
- [Common event listeners](/docs/common-event-listeners.md)

## 🙏 Credits

- [The entire Craftzing team](https://craftzing.com)
- [All current and future contributors](https://github.com/creaftzing/laravel-lokalise-webhooks/graphs/contributors)

Not only is this package built on top of Spatie's fantastic [Laravel webhook client](https://github.com/spatie/laravel-webhook-client),
its development (and documentation) is also heavily inspired by their [Laravel Stripe webhooks](https://github.com/spatie/laravel-stripe-webhooks) 
package. So we owe them a TON of gratitude!

## 🔑 License

The MIT License (MIT). Please see [License File](/LICENSE) for more information.
