# [Agora-API](https://agora-api-hetic.herokuapp.com/)

#### Disclaimer
Ce site a été réalisé à des fins pédagogiques dans le cadre du cursus Bachelor de l’école HETIC. Les contenus présentés
n'ont pas fait l'objet d'une demande de droit d'utilisation. Ce site ne sera en aucun cas exploité à des fins commerciales.

---

## Architecture du projet

### Fondation
Notre application côté back, se présente comme une `API`. Nous avons décidé de séparer la partie `client` et `serveur`,
pour permettre une plus grande malléabilité, si jamais nous devons changer la technologie front-end ou back-end.

Pour gérer les connexions, nous avons utilisé les `JWT Tokens` pour permettre aux utilisateurs de se connecter et d'avoir accès
aux routes dédiées à leur groupe respectif. Pour des soucis de sécurité, le `token` expire toutes les 15 minutes et nécessite
un rafraichissement (`refresh_token`). Ce rafraichissement renverra un nouveau token `JWT` ainsi qu'un tout nouveau `refresh token`.

### Base de données
Nous avons créé la base de données à partir de l'outil `CLI` de `Symfony` étant donné que sur un projet précédent, nous avions
mappé notre base de données sur notre projet, ce qui nous avait généré de multiples problèmes à l'époque, sur les Entités notamment.

### CRON
Nous avons dû mettre en place des routines (`CRON`), qui permettent de vérifier à des instants précis plusieurs états
de nos datas, via les commandes custom `Symfony`. Voir [CRON Symfony commands](https://github.com/kentoje/agora-api#cron-launches-3-custom-symfony-command) !
La commande `app:simulateMesure` est une commande qui ne devrait pas exister dans un vrai projet, elle est ici pour simuler les données
que devrait renvoyer le dispositif `(Agora)` installé dans les foyers.

### Test et hook
Concernant la partie de développement, nous avons intégré des `tests` permettant de vérifier que les routes fonctionnent bien,
couplé à un `Git hook` de `pre-commit`, qui run les tests avant chaque commit et bloque potentiellement le commit si les tests ne sont
pas concluants. Cela permet de savoir si le code modifié impacte ou non les routes existantes.

### Fixtures
Au niveau de la génération de fausses données, Faker a été l'outil de fixtures que nous avons utilisé. Il nous a permis
de générer avec aise, une grande quantité de données sans faire trop d'effort.

### Documentation
Cette fois-ci, nous voulions générer notre documentation quasiment automatiquement, c'est pourquoi nous avons installé et utilisé
`Swagger (Open API)` sur notre projet. À l'aide `d'annotations`, il nous est donc possible de détailler les spécifications de nos routes et entités,
tout cela grâce à un fichier `JSON` que nous générons à partir d'une commande.

### Hébergement
En terme d'hébergement, nous avons décidé d'utiliser `Heroku`. Heroku nous permet à l'aide de `JawsDB` de mettre facilement en place
la base de données MySQL et de push rapidement notre projet en production. En plus de ça, nous avons un nom de domaine
assigné par Heroku qui permet au client de notre application d'avoir une adresse de l'API qui ne change pas, peu importe
le redémarrage du serveur.

### Outils annexes
Pour faciliter l'exécution de commandes répétitives, nous avons mis en place une `Makefile` permettant de raccourcir les
commandes les fastidieuses à écrire à la main.

---

## Swagger documentation

Visit the documentation at route: `/swagger/index.html`.

---

## Installing the project

After cloning the project, go to the root of the project and create a `.env.local` file:

```shell script
touch .env.local
```

---

In `.env.local` add these lines and make changes according to your configuration:

```shell script
APP_ENV=dev
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/agoraDb?serverVersion=5.7
CORS_ALLOW_ORIGIN=^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$
```

---

### Basic commands

Then, install dependencies:

#### Normal

```shell script
composer install
```

#### Makefile

```shell script
make install
```

---

Create the Database:

#### Normal

```shell script
./bin/console doctrine:database:create
```

#### Makefile

```shell script
make db-create
```

---

Execute the last migration:

#### Normal

```shell script
./bin/console doctrine:migration:migrate
```

#### Makefile

```shell script
make db-migration
```

---

Populate database with fixtures:

#### Normal

```shell script
./bin/console doctrine:fixtures:load
```

#### Makefile

```shell script
make db-fixture
```

---

Launch the server:

#### Normal

```shell script
symfony server:start
```

#### Makefile way

```shell script
make start
```

Stop the server:

```shell script
make stop
```

---

### Setup JWT Token

First create the JWT Folder in config folder:
```shell script
mkdir -p config/jwt
```

---

Then, generate your `.pem` keys according to your `JWT_PASSPHRASE` located in `.env`, ie, when you will have to enter the `passphrase` enter the same value as the `JWT_PASSPHRASE`:

```shell script
openssl genrsa -out config/jwt/private.pem -aes256 4096
``` 

Same for this command:

```shell script
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

---

Now, if you try to login as an existing user like so:

#### httpie

```shell script
http POST localhost:8000/api/login username=aymeric.mayeux@hetic.net password=azerty
```

#### CURL

```shell script
curl -X POST -H "Content-Type: application/json" localhost:8000/api/login -d '{"username": "aymeric.mayeux@hetic.net", "password": "azerty"}'
```

You will retrieve the `JWT` Token and the `refresh` Token:

```json
{
    "user": {
        "id": 1,
        "...":  "..."
    },
    "tokens": {
        "token": "eyJ0eXAi...",
        "refresh_token": "337bb346 ..."
    }
}
```

With this JWT Token, you can now make request on routes `/api` of the application. You need to put the token in your `Headers` every time you want to make a request.

#### httpie-jwt

```shell script
http --auth-type=jwt --auth="eyJ0eXAi..." localhost:8000/api/users
```

#### CURL

```shell script
curl -X GET localhost:8000/api/users -H "Authorization: BEARER eyJ0eXAi..."
```

---

### Refresh JWT Token after expiration

Your JWT Token is set to expire at a certain time that is set in `config/packages/lexik_jwt_authentication.yaml`:

```yaml
# Unit is in second. So 900 seconds.
token_ttl: 900
```

If your JWT Token expire, you will need to re-generate a new one with the second token provided when you login, called `refresh_token`, the `refresh_token` can only be used once, but everytime you request a new JWT Token you will get a new `refresh_token` as well:

```json
{
    "refresh_token": "b1ca3f...",
    "token": "eyJ0eXAi..."
}
```

Retrieve a new valid JWT Token:

#### httpie

```shell script
http POST localhost:8000/api/token/refresh refresh_token=b1ca3f...
```

#### CURL

```shell script
curl -X POST -H "Content-Type: application/json" localhost:8000/api/token/refresh -d '{"refresh_token": "b1ca3f..."}'
```

With this new token, you will be able to fetch data from the API again.

---

### Setup crontab

Run:

```shell script
crontab -e
```

add the following line and change `<PATH_TO_PROJECT>` to your project `PATH`:

```shell script
*/15 * * * * cd ~/<PATH_TO_PROJECT>/agora-api && ./bash_scripts/launch-schedule.sh >> /dev/null 2>&1
```

[You do not know anything about CRON? Click here!](https://crontab.guru/#*/15_*_*_*_*)

#### Mac OS Catalina cron Permission Troubleshooting

Open folder containing `cron` binary file:

```shell script
open /usr/sbin
```

* Go to your System Settings > Security & Privacy > Disk access

* Click on the lock at the bottom left corner and enter your password

* Drag and drop the `cron` binary file into your System Settings Window.

* Cross the checkbox next to it.

Then it should fix the permission problem.

#### Cron launches 3 custom symfony command

NAME | DESCRIPTION | File | Time
--- | --- | --- | ---
app:newMonth | Creates a new month and new tasks for each user at the beginning of each month | `src/Command/NewMonthCommand.php`| [`0 0 1 * *`](https://crontab.guru/#0_0_1_*_*)
app:simulateMesure | Simulates every 30 minutes the measurements for each user and check if their task is still valid | `src/Command/SimulateAgoraMesureCommand.php` | [`*/30 * * * *`](https://crontab.guru/#*/30_*_*_*_*)
app:resetLevel | Every January 1st of each year resets the user level to 0 | `src/Command/ReseyAllUserLevelCommand.php`| [`0 0 1 1 *`](https://crontab.guru/#0_0_1_1_*)

---

### Setup Git hook
Current hook will run all tests at every commit.

```shell script
./bash_scripts/install-hooks.sh
```

---

### Setup Test env file
Create a `.env.test` file and change `db_user` and `db_password`:

```shell script
# define your env variables for the test env here
KERNEL_CLASS='App\Kernel'
APP_SECRET='$ecretf0rt3st'
SYMFONY_DEPRECATIONS_HELPER=999999
PANTHER_APP_ENV=panther
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/agoraDbTest?serverVersion=5.7

JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private-test.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public-test.pem
JWT_PASSPHRASE=agoratest
```

---

## Commands

### Makefile

Prefix the following names with `make`:

NAME | DESCRIPTION
--- | ---
start | Start the server
stop | Stop the server
cc | Clear the cache
router | List all routes
log | Display server logs
test | Run all tests
test-file | Run a specific test file that user enter. Example: `UserTest`
install | Install dependencies
db-create | Create the database
db-migration | Launch migrations
db-fixture | Load fixtures
db-update | Run `db-migration` and `db-fixture`
swagger | Update swagger documentation

---

## SQL

### Notre MCD 
![alt text](https://github.com/kentoje/agora-api/blob/master/mcd_mld/MCD_FIGMA.jpg "MCD_FIGMA")

### MCD JMerise
![alt text](https://github.com/kentoje/agora-api/blob/master/mcd_mld/MCD.png "MCD")

### Notre MLD
![alt text](https://github.com/kentoje/agora-api/blob/master/mcd_mld/MLD_FIGMA.jpg "MLD_FIGMA")

### MLD JMerise
![alt text](https://github.com/kentoje/agora-api/blob/master/mcd_mld/MLD.png "MLD")
