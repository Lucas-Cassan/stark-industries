FROM debian:buster-slim

RUN apt-get update
RUN apt-get install -y
RUN apt-get install -y apache2
RUN apt-get -y install lsb-release apt-transport-https ca-certificates
RUN apt-get install -y wget
RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list
RUN apt-get update
RUN apt-get install -y php7.4
RUN apt-get install -y libzip-dev
RUN apt-get install -y libpng-dev
RUN apt-get install -y zip
RUN apt-get install -y php7.4-bcmath php7.4-bz2 php7.4-intl php7.4-gd php7.4-mbstring php7.4-mysql php7.4-zip

RUN curl -fsSL https://deb.nodesource.com/setup_current.x | bash -
RUN apt-get install -y nodejs npm

COPY ./apache-conf.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite
RUN a2ensite 000-default.conf

WORKDIR /composer

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"

WORKDIR /var/www/html

CMD /composer/composer.phar install
CMD ["npm","install"]
CMD ["npm", "run", "build"]
CMD ["php","bin/console","d:s:u" ,"-f"]
CMD ["php", "bin/console", "c:c"]
CMD ["apachectl", "-D", "FOREGROUND"]

EXPOSE 80