# EzPlatform Push Connector

The Push Delivery Connector from ethinking GmbH lets you enhance your CMS experience with providing access to new ways of reaching your customers using your created content. It establishes the possibility to send push notifi cations to various platforms from the inside of your CMS.
The connector will provide its own tabs next to the admin section to let your CMS admin easily set up all necessary connections. Moreover, it is designed to create and alter distribution channels or push templates.

## Installation

The easiest and recommended way to install this utility is as a composer package:

```php
composer require ethinking/push-api dev-master
composer require ethinking/ezplatform-push-connector dev-master
```

## Usage
Create ezplatform_push_connector.yaml in the config/routes/ with the next code:
```yaml
ezplatform_push_connector:
  resource: "@EzPlatformPushConnectorBundle/Resources/config/routing.yaml"
  prefix:   /
```

Update assets
```php
yarn encore dev
```

Clear cache
```php
php bin/console c:c
```

if you're getting an error about missing dependencies in the entrypoints.json,
try to delete manually "public/assets/ezplatform" directory and update
assets again

## Dependencies

```json
{
    "symfony/dependency-injection": "^5.0",
    "symfony/http-kernel": "^5.0",
    "symfony/http-foundation": "^4.4|^5.0",
    "symfony/http-client": "^4.3|^5.0",
    "symfony/http-client-contracts": "^1.1.8|^2",
    "symfony/validator": "^3.4.30|^4.3.3|^5.0",
    "symfony/mime": "^4.3|^5.0",
    "ethinking/push-api": "dev-master"
}
```
