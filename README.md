# consumption-bundle

## Installation

### Composer

Installation with composer:

...

### Enable the bundle

Next, enable this bundle in your `config/bundles.php` file:

```
#!php

return [
    // ...
    Nahoy\ApiPlatform\ConsumptionBundle\ConsumptionBundle::class => ['all' => true],
];
```

### Setting up your entities

This bundle has entity that must be implemented by you in an application bundle:

```php
<?php
// src/Entity/Consumption.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

use Nahoy\ApiPlatform\ConsumptionBundle\Entity\BaseConsumption;

/**
 * Class Consumption
 *
 * @ORM\Entity()
 * @ORM\Table(name="consumption")
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     attributes={
 *         "normalization_context"={"groups"={"consumption"}},
 *         "denormalization_context"={"groups"={"consumption"}}
 *     }
 * )
 */
class Consumption extends BaseConsumption
{
    //
}
```

And, update your database schema:

```bash
bin/console doctrine:schema:update
```

## Full Configuration Options

```yaml
# app/config/config.yml

consumption:
    api_pattern: ~/api/.+~
    class:
        consumption: App\Entity\Consumption
        user: App\Entity\User
    getter:
        user_id: getId
        user_username: getUsername
```
