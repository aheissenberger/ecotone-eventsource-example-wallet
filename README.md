# ecotone-eventsource-example-wallet

* add database persistance
* add Server-Sent Events (SSE) API

## debugging

### php

export XDEBUG_MODE=debug XDEBUG_SESSION=1

### sql

**ein:**

```sql
SET global general_log = 1;
SET global log_output = 'table';
```
**aus:**

```sql
SET global general_log = 0;
```

https://tableplus.com/blog/2018/10/how-to-show-queries-log-in-mysql.html

## vs code dev container

https://github.com/microsoft/vscode-dev-containers/blob/main/containers/php-mariadb/README.md

### start apache

`apache2ctl start`

**only once - connect `App/` directory to `www`:**
`cd /workspace/App && sudo chmod a+x "$(pwd)" && sudo rm -rf /var/www/html && sudo ln -s "$(pwd)" /var/www/html`


## Fixes

use patches to fix problems in dependencies:
composer require --dev symplify/vendor-patches
`composer require --dev symplify/vendor-patches`

to create a patch:
1. copy origin file to xxxx.php.old
2. change the origin file xxxx.php
3. run `vendor/bin/vendor-patches generate` which will create the patch in directory `patches` and adds it to `composer.json`

Error `Fatal error: Uncaught Prooph\EventStore\Exception\StreamExistsAlready: A stream with name Ecotone\App\Wallet exists already in /workspace/App/vendor/prooph/event-store/src/Exception/StreamExistsAlready.php:22`:
