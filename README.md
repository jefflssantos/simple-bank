# Simple Bank

![continuous integration](https://github.com/jefflssantos/simple-bank/actions/workflows/continuous_integration.yml/badge.svg)
[![codecov](https://codecov.io/gh/jefflssantos/simple-bank/branch/main/graph/badge.svg?token=TBGUEQJWK2)](https://codecov.io/gh/jefflssantos/simple-bank)

### Clone the project
```bash
git clone git@github.com:jefflssantos/simple-bank.git
```

### Copy the ```.env.example```  to  ```.env```
```bash
cd simple-bank
cp .env.example .env
```

### Start docker (docker compose must be intalled) and install composer dependencies
```bash
docker compose up -d
docker compose exec app composer install
```

### Create the app key
```bash
docker compose exec app php artisan key:generate
```

### Now you are able to run the tests and static analysis
```bash
docker compose exec app composer run test
```

### Testing the API
```bash
docker compose exec app php artisan migrate:fresh --seed
```
```http request
POST /transfer
Content-Type: application/json
Accept: application/json

{
  "value": 100.0,
  "payer": 1,
  "payee": 2
}
```
