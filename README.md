# Database backup CLI app

PS: This work was inspired from many people around me, especially [backmeup](https://github.com/Ardakilic/backmeup) which was created by [Arda Kılıçdağı](https://github.com/Ardakilic).
  
## The idea
  
  I wanted to have a small cli app that I can use to backup my databases whenever I need, and with simple modifications,
  so I tried bach/shell and I didn't like it.
  
  So to make the story short this is what I have done so far, this is just a small backup cli app that you can use on 
  your server and run it via cron jobs to create a backup for all your databases or for one of them if you pass it as a parameter.

## Installation

Right now the best way to install it is to clone the repository

```git
git clone https://github.com/linuxjuggler/database-backup.git
```

Once I got it covered with tests I'll submit it to [packagist](https://packagist.org).

## Usage

1. You should create the configuration file using the command:

```bash
./bin/backup init
```

You will be asked for few questions which the app is going to use them later.

2. You can start the backup process using the command:

```bash
./bin/backup run --database my_database_name
```

if you didn't pass the database parameter it will backup all the databases.

## Create new Drivers

Right now the app will backup your database locally, 
but you can create a new adapter or driver simply by creating a new class inside `/src/Backup/Classes/Drivers`, 
but you will need to name it carefully, as the backup class will automatically load the classes based on the driver name.

So lets say you have created a driver for Amazon S3, you will call your class `S3Backup.php`, and the system will pickup 
the name automatically and convert it to `s3` so in the `config.yml` file all you have to do is to change:

```yaml
adapter:
    default: local
```

to

```yaml
adapter:
    default: s3
```
or you can simply run the initiate command again to regenerate the config file, and you will notice that your new driver will automatically be listed.
