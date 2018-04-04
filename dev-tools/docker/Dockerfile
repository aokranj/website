FROM    ubuntu:14.04


RUN     apt-get update && \
        apt-get -y upgrade && \
        \
        DEBIAN_FRONTEND=noninteractive   apt-get install -y software-properties-common && \
        add-apt-repository -y ppa:ondrej/php && \
        apt-get update && \
        \
        DEBIAN_FRONTEND=noninteractive   apt-get install -y --force-yes       \
            apache2          \
            php5.6           \
            php5.6-bz2       \
            php5.6-cli       \
            php5.6-curl      \
            php5.6-gd        \
            php5.6-intl      \
            php5.6-json      \
            php5.6-mbstring  \
            php5.6-mcrypt    \
            php5.6-mbstring  \
            php5.6-mysql     \
            php5.6-opcache   \
            php5.6-xml       \
            php5.6-xsl       \
            php5.6-zip       \
        \
        && true

RUN     a2enmod auth_digest
RUN     a2enmod rewrite
RUN     a2enmod php5.6

# Remove default vhost, to make our vhost the default
RUN     rm /etc/apache2/sites-enabled/000-default.conf

# Disable apache daemon startup via upstart, docker does this via CMD
RUN     update-rc.d apache2 disable


EXPOSE 80


CMD /usr/sbin/apache2ctl -D FOREGROUND
