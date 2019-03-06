# consumption-bundle

## Introduction

This module makes it easy to know the number of requests per request / user.

It works with:

* a pusher
* a puller

## Compatibility with Symfony

* Symfony `3`
* Symfony `4`

## Installation

### Requirements

This bundle requires Symfony 3 or Symfony 4.

* API Platform (https://api-platform.com/)
* Redis server
* snc/SncRedisBundle (https://github.com/snc/SncRedisBundle)

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

## Configuration

For Symfony 4 - Configure the bundle in your `config/packages/consumption.yaml` file:

```yaml
# config/packages/consumption.yaml

consumption:
    api_pattern: ~/api/.+~ # the pattern of the URLs of your API. URLs that do not match this pattern will not have statistics.
    class:
        consumption: App\Entity\Consumption # your consumption entity
        user: App\Entity\User               # your user entity 
    getter:
        user_id: getId             # your user ID getter method
        user_username: getUsername # your username getter method
```

For Symfony 3 - Add the lines above in your `app/config/config.yml` file.

### Running the cron jobs

Add the following cron job on your system:

```bash
* * * * * YOUR_APP/bin/console --env=prod nahoy:consumption:pull > /dev/null
```

You can adapt the frequency.

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

The possible values of **metric_name** are:

| Value                           | Description                                 |
| ------------------------------- | ------------------------------------------- | 
| consumptionCountByMethodByDay   | Nombre de requêtes par méthode et par jour  |
| consumptionTotalByDay           | Nombre de requêtes total par jour           |
| consumptionCountByMethodByMonth | Nombre de requêtes par méthodes et par mois |
| consumptionTotalByMonth         | Nombre de requêtes total par mois           |

## Usage

1\ I want to know the total consumption per month of my API for a specific user.

Request your API at the following URL:

    GET /api/consumptions?username=john&metricName=consumptionTotalByMonth

In the example above, the total consumption of the user "john" for all months will be returned.

Example:

```json
{
    ...
    "hydra:member": [
        {
            "@id": "/api/consumptions/4",
            "@type": "Consumption",
            "id": 4,
            "username": "john",
            "method": null,
            "uri": null,
            "metricName": "consumptionTotalByMonth",
            "lastValue": 1530,
            "date": "2019-01-01T00:00:00+01:00",
            "user": "/api/users/1"
        },
        {
            "@id": "/api/consumptions/10",
            "@type": "Consumption",
            "id": 10,
            "username": "john",
            "method": null,
            "uri": null,
            "metricName": "consumptionTotalByMonth",
            "lastValue": 2025,
            "date": "2019-02-01T00:00:00+01:00",
            "user": "/api/users/1"
        },
        {
            "@id": "/api/consumptions/16",
            "@type": "Consumption",
            "id": 16,
            "username": "john",
            "method": null,
            "uri": null,
            "metricName": "consumptionTotalByMonth",
            "lastValue": 500,
            "date": "2019-03-01T00:00:00+01:00",
            "user": "/api/users/1"
        }
        ...
    ]
}
```

2\ I want to know the detailed consumption of my API for a specific month and user.

Request your API at the following URL:

    GET /api/consumptions?username=john&metricName=consumptionCountByMethodByMonth&date%5Bafter%5D=2019-03-01&date%5Bbefore%5D=2019-03-31

In the example above, the consumption detailed by method of the user "john" and for the month of March 2019 will be returned.

Example:

```json
{
    ...
    "hydra:member": [
        {
            "@id": "/api/consumptions/9",
            "@type": "Consumption",
            "id": 9,
            "username": "john",
            "method": "GET",
            "uri": "/api/metrics",
            "metricName": "consumptionCountByMethodByMonth",
            "lastValue": 450,
            "date": "2019-03-01T00:00:00+01:00",
            "user": "/api/users/1"
        },
        {
            "@id": "/api/consumptions/24",
            "@type": "Consumption",
            "id": 24,
            "username": "john",
            "method": "GET",
            "uri": "/api/metrics/{id}",
            "metricName": "consumptionCountByMethodByMonth",
            "lastValue": 50,
            "date": "2019-03-01T00:00:00+01:00",
            "user": "/api/users/1"
        } 
        ...
    ]
}
```
