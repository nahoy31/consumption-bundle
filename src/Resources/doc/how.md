## How it works?

### API statistics

#### Pusher

Everytime your API is requested, the following counter is incremented in your cache system:

    app~consumption~{USER_ID}~{USER_NAME}~{YYYYMMDD}~{METHOD}~{URI}

Examples:

    app~consumption~1~admin~20180923~GET~/api/metrics
    app~consumption~1~admin~20180923~GET~/api/metrics/{id}

See `src/EventSubscriber/ConsumptionPusherSubscriber.php`.

#### Puller

Cron job executed on your system that will get the counters from your cache system, empty them and fill the  the MySQL table (see next chapter).

See `src/Command/PullCommand.php`.

#### Entities

##### Consumption

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

### API limit

in writing...

---

[Return to the index.](../../../README.md)
