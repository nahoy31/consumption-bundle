## Usage

### API statistics

1\ I want to know the total consumption per month of my API for a specific user.

Request your API at the following URL:

    GET /api/consumptions?username=john&metricName=consumptionTotalByMonth

In the example above, the total consumption of the user "john" for all months will be returned.

Example:

```json
{
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
    ]
}
```

### API limit

in writing...


---

[Return to the index.](../../../README.md)
