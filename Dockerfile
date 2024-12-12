FROM laravelsail/php82-composer as base

# Instalar wkhtmltopdf
RUN apt-get update \
    && apt-get install -y \
        wkhtmltopdf \
        xvfb \
    && apt-get clean

# Configuración final
WORKDIR /var/www/html
