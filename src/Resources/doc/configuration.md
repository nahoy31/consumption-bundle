## Configuration

For Symfony 4 - Configure the bundle in your `config/packages/consumption.yaml` file:

```yaml
# config/packages/consumption.yaml

consumption:
    # enable the exception if the limit exceeded
    enabled_limit: true
    
    # the pattern of the URLs of your API. URLs that do not match this pattern will not have statistics.
    api_pattern: ~/api/.+~
    
    # The service that is used to persist the metadatas used by this bundle.
    # The service has to implement the Psr\Cache\CacheItemPoolInterface interface.
    # If no service id provided then the default cache is Filesystem (location: %kernel.cache_dir%/nahoy_consumption).
    cache: ~
    
    class:
        consumption: App\Entity\Consumption # your consumption entity
        user: App\Entity\User               # your user entity
    
    # Exception thrown when the limit exceeded
    exception:
        status_code: 429
        message: 'API requests limit exceeded for %s.' # %s will be replace with client IP address
        custom_exception: ~ # The exception has to extend Nahoy\ApiPlatform\ConsumptionBundle\Exception\RateLimitExceededException
    
    # Specify how the bundle will find the following information:
    # The component `symfony/property-access` and his property notation are used:
    # https://symfony.com/doc/current/components/property_access.html#usage
    getter:
        # the user ID index
        # the property will be read in your user entity
        user_id: id
        # the username index
        # the property will be read in your user entity
        user_username: username
        # the limit of requests to your API of the user index
        # the property will be read in your user entity
        user_limit: plan.limit
```

For Symfony 3 - Add the lines above in your `app/config/config.yml` file.

---

[Return to the index.](../../../README.md)
