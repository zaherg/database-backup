# Database backup CLI app

PS: This work was inspired by many people around me, especially [backmeup](https://github.com/Ardakilic/backmeup) which was created by [Arda Kılıçdağı](https://github.com/Ardakilic).
  
## The idea
  
  I wanted to have a small cli app that I can use to backup my databases whenever I need and with simple modifications,
  so I tried bach/shell and I didn't like it.
  
  So to make the story short this is what I have done so far, this is just a small backup cli app that you can use on 
  your server and run it via cron jobs to create a backup for all your databases or for one of them if you pass it as a parameter.

## Installation

### Via git clone

Right now the best way to install it is to clone the repository

```git
git clone https://github.com/linuxjuggler/database-backup.git
```

### Via composer

You can install it using composer by executing the following command:

```bash
composer create-project damascene/database-backup
```

## Usage

1. You should create the configuration file using the command:

```bash
backup init
```

You will be asked for few questions which the app is going to use them later.

2. You can start the backup process using the command:

```bash
backup db:run --database my_database_name
```

if you didn't pass the database parameter it will backup all the databases.

3. you can list all the backup files that you have using the command:

```bash
backup db:list
```
