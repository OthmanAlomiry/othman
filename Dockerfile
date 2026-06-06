FROM php:8.2-apache

# تثبيت إضافة الـ calendar المطلوبة لحسابات التاريخ الهجري
RUN docker-php-ext-install calendar

COPY . /var/www/html/

RUN chmod -R 755 /var/www/html/

EXPOSE 80
