# Database backup CLI app

PS: This work was inspired by many people around me, especially [backmeup](https://github.com/Ardakilic/backmeup) which was created by [Arda Kılıçdağı](https://github.com/Ardakilic).
  
## The idea
  
  I wanted to have a small cli app that I can use to backup my databases whenever I need and with simple modifications,
  so I tried bach/shell and I didn't like it.
  
  So to make the story short this is what I have done so far, this is just a small backup cli app that you can use on 
  your server and run it via cron jobs to create a backup for all your databases or for one of them if you pass it as a parameter.

## Currently implemented backup drivers:

1. Local backup.
2. Dropbox.
3. Amazon.

## Installation

### Requirement

Remember that you need to have composer installed locally, if its not installed locally you can get it from [getcomposer.org](https://getcomposer.org/)

### Via git clone

Right now the best way to install it is to clone the repository

```git
git clone https://github.com/linuxjuggler/database-backup.git
```

then execute the following commands

```bash
cd database-backup
composer install
```

### Via composer

You can install it using composer by executing the following command:

```bash
composer create-project damascene/database-backup
```

### Via composer (globally)

You can install it using composer by executing the following command:

```bash
composer global require damascene/database-backup
```

_PS: if you run it globally you can just use `backup` instead of `./bin/backup`._

## Usage

- You should create the configuration file using the command:

```bash
./bin/backup init
```

You will be asked for few questions which the app is going to use them later.

- You can start the backup process using the command:

```bash
./bin/backup db:run --database my_database_name
```

if you didn't pass the database parameter it will backup all the databases.

- you can list all the backup files that you have using the command:

```bash
./bin/backup db:list
```

## Upgrade

Based on how you have install it you can run one of the following commands:

- If installed via git:

from within your code directory run:

```git
git pull origin master
```

- If installed via composer:

from within your code directory run:

```bash
composer update
```

- If installed via composer globally:

```bash
composer global update
```

- Finally check the file `config.yml.example` for the new updates if you don't want to use the `init` command.
