# EzPlatform Push Connector

Service which makes it possible to send Push Notifications via various channels to many subscribers and in high speed

## Installation

The easiest and recommended way to install this utility is as a composer package:

```php
composer require ethinking/ezplatform-push-connector
```

## Usage
Create ezplatform_push_connector.yaml in the config/routes/ with the next code:
```yaml
ezplatform_push_connector:
  resource: "@EzPlatformPushConnectorBundle/Resources/config/routing.yaml"
  prefix:   /
```

Clear cache
```php
php bin/console c:c
```

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
    "ethinking/push-api": "*"
}
```
