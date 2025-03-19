#!/bin/bash

# Démarrer le worker de file d'attente
php artisan queue:work --tries=3 --timeout=60 