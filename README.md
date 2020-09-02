# eZPlatform Push Connector

## Requirement 

eZ Platform 3.x +

## Installation 

Now manually as we don't have an automatic installation process

### The bundle

add the bundle next to your installation

### Route

add following file route configuration in `htdocs/config/routes/ezplatform_push_connector.yaml`

```
ezplatform_push_connector:
    resource: "@EthinkingEzPlatformPushConnectorBundle/Resources/config/routing.yaml"
    prefix:   /
```

### Bundle declaration

This could be added in `config/bundles.php`

```
    Ethinking\PushConnectorBundle\EthinkingEzPlatformPushConnectorBundle::class => ['all' => true],
```

### Composer

you can have the bundle everywhere in the installation but you should consider to add two paths in `composer.json`

```
    "Ethinking\\PushConnectorBundle\\": "../ethinking/ezplatform-push-connector/src/bundle",
    "Ethinking\\PushConnector\\": "../ethinking/ezplatform-push-connector/src/lib"
```
The above entries should be added in the `autoload` section.


### Cahe and autoload

regenerate autoload with

```
composer dump-autoload
```

Clear cache

```
php bin/console c:c
```


