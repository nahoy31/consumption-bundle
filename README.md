# consumption-bundle

## Introduction

This module makes it easy to know the number of requests per request / user.

It works with:

* a pusher
* a puller

## Installation

### Requirements

* Symfony 3 or Symfony 4
* API Platform (https://api-platform.com/)
* Redis server
* snc/SncRedisBundle (https://github.com/snc/SncRedisBundle)

### Composer

Installation with composer:

```bash
composer require nahoy31/consumption-bundle
```

### Enable the bundle

Next, enable this bundle in your `config/bundles.php` file:

```php
<?php

return [
    // ...
    Nahoy\ApiPlatform\ConsumptionBundle\ConsumptionBundle::class => ['all' => true],
];
```

Or in your `app/AppKernel.php` file:

```php
<?php

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

You can adapt the frequency.

## Full Configuration Options

```yaml
# config/packages/consumption.yaml
# or app/config/config.yml

consumption:
    api_pattern: ~/api/.+~
    class:
        consumption: App\Entity\Consumption
        user: App\Entity\User
    getter:
        user_id: getId
        user_username: getUsername
```

## Pusher

Everytime your API is requested, the following counter is incremented in your Redis server:

    app~consumption~{USER_ID}~{USER_NAME}~{YYYYMMDD}~{METHOD}~{URI}

Examples:

    app~consumption~1~admin~20180923~GET~/api/metrics
    app~consumption~1~admin~20180923~GET~/api/metrics/{id}

See `src/EventSubscriber/ConsumptionPusherSubscriber.php`.

## Puller

Cron job executed on your system that will get the Redis counters, empty them and fill the  the MySQL table (see next chapter).

See `src/Command/PullCommand.php`.

## Entities

### Consumption

| Field       | Format       | Required | Example                 |
| ----------- | ------------ | -------- | ----------------------- |
| id          | integer      | yes      | 1                       |
| user_id     | integer      | yes      | 1                       |
| username    | string       | yes      | admin                   |
| method      | string       | no       | GET                     |
| uri         | string       | no       | /api/metrics/{id}       |
| metric_name | string       | yes      | consumptionTotalByMonth |
| last_value  | integer      | yes      | 15250                   |
| date        | date (Y-m-d) | yes      | 2018-09-20              |