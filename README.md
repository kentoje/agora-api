# agora-api
Disclaimer
Ce site a été réalisé à des fins pédagogiques dans le cadre du cursus Bachelor de l’école HETIC. Les contenus présentés n'ont pas fait l'objet d'une demande de droit d'utilisation. Ce site ne sera en aucun cas exploité à des fins commerciales.

## Install project

After cloning the project, go to its root and run:

```shell script
composer install
```

---

Then create a `.env.local` file:

```shell script
touch .env.local
```

---

In `.env.local` add these lines and make changes according to your configuration:

```shell script
APP_ENV=dev
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7
CORS_ALLOW_ORIGIN=^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$
```
