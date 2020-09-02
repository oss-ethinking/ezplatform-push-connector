# Backend Navigation in eZ Platform
eZ Platorm is using `KnpMenuBundle` to generate the menu in several part of the application(Top, Right and Left). To be able extend the menu we need to implement an EventListener. Most of our Enterprise Bundle are using a Listener e.g:
```
vendor/ezsystems/ezplatform-form-builder/src/bundle/Menu/ConfigureMainMenuListener.php

```
configuration:
```
vendor/ezsystems/ezplatform-form-builder/src/bundle/Resources/config/services/misc.yaml
```

In this bundle I'm going to use an EventSubscriber instead. It allows us to have less configuration and to be able to have the Event name directly in the Subscriber.

```
ezplatform-push-connector/src/lib/UI/Menu/EventSubscriber/PushConnectorMenuSubscriber.php
``` 
configuration:
```
ezplatform-push-connector/src/bundle/Resources/config/ezplatform/services/menu.yaml
```

The Event we want to extend is: `ConfigureMenuEvent::MAIN_MENU`

## Translation
The translation of the menu item is using `translation_domain=> menu` and the `TranslationContainerInterface` of the `JMS\TranslationBundle`. Of course, the translation file should be `menu.en.yaml` and located in the standard translation file:
```
ezplatform-push-connector/src/bundle/Resources/translations/menu.en.yaml
```

Note: We are using here `yaml` instead of xliff(standard in ezplatfom) to keep the translation process simple.

## Permissions

Now we are using the PermissionResolver to check if the user is allowed to see the menu items. to do so you can always use:

```
if ($this->permissionResolver->hasAccess('push', 'main_menu')) {
            $root->addChild(....
}
```

Add your module and function names in `policies.yaml` and add translation in `forms.en.yaml`

Check also permission in the Controller methods to prevent accessing the module from the URL.

## Symfony standard
Starting from this step we are decoupled from eZ Platform. 

### Routing

To add a navigation route it is enough to add a new parameters to the `addChild()` method in the above EventSubscriber

```
[
    'route' => 'ezplatform.push.main_settings.view',
]

```

The route is availbale in `ezplatform-push-connector/src/bundle/Resources/config/routing.yaml` and imported in

```
config/routes/ezplatform_push_connector.yaml
``` 

### Controller

The route will need a Controller and an action method, where we will add our business logic later e.g Forms.


# Right Content Menu

Please check `ContentSidebarRightSubscriber.php`, there are the same steps like above description. This Subscriber is using the `ConfigureMenuEvent::CONTENT_SIDEBAR_RIGHT` . We are using the `$options['location']->id` to access later the content from the controller.  