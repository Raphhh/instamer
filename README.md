# Instamer - Symfony app to automatise Instagram growing


## Add an account

```bash
$ bin/console account:create {username} {password} 
```

## Synchronize followings with local database

```bash
$ bin/console following:synchronize {username}
```

## Prune the followings

Set as "inactive", the accounts:
 - active
 - not frozen
 - not reciprocal
 - added to the local database before 10 days ago

```bash
$ bin/console following:prune {username} [--before="10 days ago"] [--dry-run] 
```

## Add new followings

```bash
$ bin/console following:add {username}
```
