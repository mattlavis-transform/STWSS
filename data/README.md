# Notes from Chris Mills

heroku pg:backups:restore https://raw.githubusercontent.com/mattlavis-transform/STWSS/master/data/smart_signposting_data.dump DATABASE_URL --app stwss --confirm stwss

# Heroku URL with instructions

https://devcenter.heroku.com/articles/upgrading-heroku-postgres-databases#upgrade-with-pg-copy-default



### To create a new Hobby basic ($9 a month) database

This creates the new database (paid)

```term
heroku addons:create heroku-postgresql:hobby-basic
```

This waits and then tells you when it is ready

```term
heroku pg:wait
```

This then prevents data from being written during the copy process

```term
heroku maintenance:on
```





Last login: Fri Jul 24 04:38:13 on ttys001
You have mail.
matt.admin@ldntranml012452-Matt-Lavis stw_data % heroku addons:create heroku-postgresql:hobby-basic
Creating heroku-postgresql:hobby-basic on ⬢ stwss... $9/month
Database has been created and is available
 ! This database is empty. If upgrading, you can transfer
 ! data from another database with pg:copy
Created postgresql-rigid-80226 as HEROKU_POSTGRESQL_BRONZE_URL
Use heroku addons:docs heroku-postgresql to view documentation
matt.admin@ldntranml012452-Matt-Lavis stw_data %

```term
heroku pg:promote HEROKU_POSTGRESQL_BRONZE_URL
```