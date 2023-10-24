#!/bin/bash

export AWS_DEFAULT_REGION=us-east-1

PROJECT_NAME="COMPARA_LEILAO"

declare -a environments=(
    'APP_KEY'
    'APP_ENV'
    'APP_NAME'
    'DB_CONNECTION'
    'DB_HOST'
    'DB_DATABASE'
    'DB_PASSWORD'
    'DB_USERNAME'
    'REDIS_HOST'
    'REDIS_PASSWORD'
    'REDIS_PORT'
    'QUEUE_DRIVER'
    'CACHE_DRIVER'
    'LOG_CHANNEL'
    'PAPERTRAIL_URL'
    'PAPERTRAIL_PORT'
    'PAPERTRAIL_TAG'
    'LOG_LEVEL'
)

if [ ! -x "$(command -v aws)" ]; then
  echo 'The aws command line tools (awscli) is required in order to run this script'
  exit 1
fi

# $1: Env var on application
# $2: Var on SSM
function export_var {
  env_var=$(aws ssm get-parameters --names $2 --with-decryption --output text | cut -f7)
  export "$1=$env_var"
  echo "$1=$env_var"
}

# $1: Env
#function set_env {
#    for env in "${environments[@]}"
#    do
#        export_var "${env}" "${PROJECT_NAME}_${1}_${env}"
#    done
#}
#
#echo 'Fetching dev environments variables'
#set_env "DEV"

echo 'Set zone'
cp  -f /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime
ln -sf /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime
dpkg-reconfigure --frontend noninteractive tzdata

echo 'Running migrates'
php artisan migrate --force

echo 'Clearing and configuring cache'
php artisan optimize

/usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
/usr/bin/supervisorctl -n -c /etc/supervisor/supervisord.conf
