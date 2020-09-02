# eZPlatform Forms

eZ Platform is using Symfony Forms to create forms in different part of the UI. you can find some examples in: `vendor/ezsystems/ezplatform-admin-ui/src/lib/Form`

To create a form we will host the different FomTypes as well as the main Factory class in below path

```
ezplatform-push-connector/src/lib/EzPlatform/Repository/Form
```

## FormFactory

This is a standalone class which using the Symfony `FormFactoryInterface` and a specific method used also in all eZ Forms: `createNamed()`

This Method requires the `Type` as well as the `Data` Class

The Controller will then build the form using the FormFactory:

MainSettingsController:
```
$this->formFactory->apiKeyFormType
```

## FormType Class

This is where we will build the different Forms field.

```
ezsystems/ezplatform-push-connector/src/lib/EzPlatform/Repository/Form/Type/MainSettingsFormType.php
```

## Data/Entity Class

This is a typical getter and setter standalone class for all fields properties used in the form.

> You should consider using the same property name in the type class

Field name:
```
$builder
    ->add(
        'domain',
```

Property name:
```
public $domain;
```

The Data class is stored in `ezsystems/ezplatform-push-connector/src/bundle/Entity/PushService/MainSettings.php` 

> Form field validation and Doctrine ORM will should be extended from this class

## Form Templates

Form templates will be stored in `ezplatform-push-connector/src/bundle/Resources/views/themes/admin`. For the settings area we can create a settings folder.

## configuration

```
ezplatform-push-connector/src/bundle/Resources/config/ezplatform/services/forms.yaml
```

## Controller

Generate the Form using the Data class
```
$form = $this->formFactory->apiKeyFormType(
            $this->mainSettings,
            $this->generateUrl('ezplatform.push.main_settings.view')
        );
```

In the `try` `catch` block is the logic to store the data in the database.
