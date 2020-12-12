<p align="center"><img src="./public/img/logo.png" width="400"></p>


# Grupo APC | Point Of Sale 

## Cambios en producción:

*.env*
```
APP_DEBUG=false

... 

AIRLOCK_STATEFUL_DOMAINS=grupoapc.controller.net.ar // <Dominio sin http o https>
```

*/config/airlock.php*
```php
'stateful' => explode(',', env('AIRLOCK_STATEFUL_DOMAINS', 'localhost')),
```