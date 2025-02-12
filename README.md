# Book Search

## Build Setup

```bash
# install docker and lando

# start lando
$ lando start

# run in pgsql cli to enable uuid generation
$ CREATE EXTENSION pgcrypto;

# Db Import run sql command
$ database/authors.sql
$ database/books.sql

# Run cron
$ php command/cron.php
```
