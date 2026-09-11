#!/bin/sh

export COMPOSE_BAKE=true

docker compose up --build --force-recreate -d
