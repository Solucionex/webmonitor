# Monitorización Web haciendo uso de Fiware

## Sobre el proyecto

Este es un proyecto de formación que trata de implementar los conocimientos adquiridos de la plataforma Fiware, implementando una aplicación de monitorización web haciendo uso de una serie de servicios que desempeñan las labores de recolección, persistencia y tratamiento de los datos que generan una serie de sensores.

## Arquitectura

Este proyecto consta de los siguientes servicios:

- Orion CB (orion)
- IoT Agent JSON (iot-agent)
- MongoDB (mongodb)
- Cygnus (cygnus)
- MariaDB (mariadb)
- PhpMyAdmin (phpmyadmin)
- Grafana (grafana)
- App (app)
- Proxy (proxy)

## Instrucciones

Para hacer uso del entorno de desarrollo `docker-compose.dev.yml` se ha desarrollado un pequeño archivo Makefile con atajos para el uso del entorno. A continuación se definen los comandos disponibles:

- `make ps` equivale a `docker-compose ps`
- `make up` equivale a `docker-compose up`
- `make down` equivale a `docker-compose down`
- `make restart` equivale a `docker-compose restart`
- `make ssh [servicio]` equivale a `docker-compose exec [servicio] bash`
- `make logs [servicio]` equivale a `docker-compose logs [servicio] bash`
- `make docker [comando]` equivale a `docker-compose [comando]`
- `make composer [comando]` equivale a `docker-compose exec app composer [comando]`
- `make require [paquete]` equivale a `docker-compose exec app composer require [paquete]`
- `make console [comando]` equivale a `docker-compose exec app php bin/console [comando]`
- `make create [comando]` equivale a `docker-compose exec app php bin/console make:[comando]`

Para levantar el entorno de desarrollo, una vez clonado el proyecto:

- Generar el `.env` a partir del `dist.env`.
- Lanzar el comando `make up` para levantar el entorno.
- Al acceder a `http://localhost` podrá ver la interfaz de la app.

## Mapa de puertos

En el archivo `dist.env` están definidos todos los puertos por defecto que utiliza cada servicio. En el caso de utilizar el proyecto `docker-compose.dev.yml`, todos los servicios son accesibles desde el exterior mediante la url `http://localhost:PORT`, mientras que si se utiliza el proyecto destinado a producción `docker-compose.prod.yml` solamente se podrá acceder al puerto 80 mediante el proxy.