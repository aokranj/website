### Base to start with
#
FROM ubuntu:20.04



### Prevent APT from asking any questions
#
ARG DEBIAN_FRONTEND=noninteractive



### Install UTF-8 locale
#
RUN apt-get update && \
    apt-get install -y locales && \
    rm -rf /var/lib/apt/lists/*

RUN localedef -i en_US -c -f UTF-8 -A /usr/share/locale/locale.alias en_US.UTF-8
ENV LANG en_US.utf8



### Install Apache + PHP (+ modules for both)
#
RUN apt-get update \
    && \
    apt-get install -y \
        libxml2-dev \
        zlib1g-dev \
        apache2 \
        libapache2-mod-auth-mellon \
        libapache2-mod-php7.4 \
        php-curl \
        php-gd \
        php-imagick \
        php-intl \
        php-mbstring \
        php-mysql \
        php-sqlite3 \
        php-xml \
        php-zip \
        ssmtp \
        mariadb-client \
        less \
    && \
    rm -rf /var/lib/apt/lists/*



### Enable modules
#
RUN a2enmod info
RUN a2enmod rewrite



### Place configuration files into the container
#
ADD  conf/apache2/sites-enabled /etc/apache2/sites-enabled
COPY conf/php/prepend.php       /var/lib/php/prepend.php
COPY conf/php/conf.d            /etc/php/7.4/apache2/conf.d
COPY conf/ssmtp/ssmtp.conf      /etc/ssmtp/ssmtp.conf



### Expose TCP port 80
#
EXPOSE 80



### Command to start in the container
#
# ENVs are here to replace apachectl with apache2, since apachectl does not handle SIGINT.
# (Not handling SIGINT causes stalls with container shutdowns.)
#
ENV APACHE_RUN_DIR=/var/run/apache2
ENV APACHE_LOCK_DIR=/var/lock/apache2
ENV APACHE_PID_FILE=/var/run/apache2/apache2.pid
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data
ENV APACHE_LOG_DIR=/var/log/apache2

CMD ["/usr/sbin/apache2", "-D", "FOREGROUND"]
