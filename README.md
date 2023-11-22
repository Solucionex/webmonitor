# Plataforma Fiware para la monitorización web

## Sobre el proyecto

Este es un proyecto de formación que trata de implementar los conocimientos adquiridos de la plataforma Fiware.

En concreto, se implementa una plataforma de monitorización de web haciendo uso de una serie de servicios que desempeñan las labores de recolección, persistencia y tratamiento de los datos que generan una serie de sensores virtuales HTTP.

## Arquitectura

Este proyecto consta de los siguientes servicios:

- Orion CB (fiware-orion)
- IoT Agent JSON (fiware-agent)
- MongoDB (fiware-mongodb)
- Cygnus (fiware-cygnus)
- MariaDB (fiware-mariadb)
- PhpMyAdmin (fiware-phpmyadmin)
- Grafana (fiware-grafana)
- Monitor (fiware-monitor)
- Front (fiware-front)

## Instrucciones

Para hacer uso del entorno de desarrollo:

- Generar el `.env` a partir del `dist.env`.
- Lanzar el comando `docker-compose up -d` para levantar el entorno.

## Mapa de puertos

## Acceso a interfaces

- Front (http://localhost)
- Grafana (http://localhost:3000)
- PhpMyAdmin (http://localhost:8000)

## Acceso a APIs

- Orion CB (http://localhost:1026/)
- IoT Agent (http://localhost:4041)
- Cygnus (http://localhost:5080)