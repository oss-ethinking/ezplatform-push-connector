# Mapping definition

See the node builder in `DependencyInjection/Configuration/Parser/HubSpotConfigParser.php`. You can find more information about Node tree in the symfony Documentation.

## Usage:
create a yaml file in `config/packages/push_mapping.yaml` :

```
ezplatform:
    system:
        admin_group:
            push_config:
                content_types_map:
                    article:
                        webpush:
                            body: 'title'
                            image: 'image'
                            enabled: true
                        whatsapp:
                            body: 'title'
                            image: 'image'
                            enabled: false
```

import this file in `config/packages/ezplatform.yaml`

```
imports:
    - push_mapping.yaml
```

You may now check if the configuration is loaded using:

```
php bin/console debug:config ezplatform
```