## Installation

### Requirements

This bundle requires Symfony 3 or Symfony 4.

* API Platform (https://api-platform.com/)

### Composer

Installation with composer:

```bash
composer require nahoy31/consumption-bundle:dev-master
```

### Enable the bundle

For Symfony 4 - Enable this bundle in your `config/bundles.php` file:

```php
<?php
// config/bundles.php

return [
    // ...
    Nahoy\ApiPlatform\ConsumptionBundle\ConsumptionBundle::class => ['all' => true],
];
```

For Symfony 3 - in your `app/AppKernel.php` file:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = [
        new Nahoy\ApiPlatform\ConsumptionBundle\ConsumptionBundle(),
    ];
    ...
}
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

Adapt the class if necessary.

And, update your database schema:

```bash
bin/console doctrine:schema:update
```

### Running the cron jobs

Add the following cron job on your system:

```bash
* * * * * YOUR_APP/bin/console --env=prod nahoy:consumption:pull > /dev/null
```

If `enabled_limit` is set to `true` then add also this cron job:

```bash
* * * * * YOUR_APP/bin/console --env=prod nahoy:consumption:create-limits > /dev/null
```

You can adapt the frequency.

---

[Return to the index.](../../../README.md)
