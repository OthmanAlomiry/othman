FROM php:8.2-apache

# 1. تحديد مسار العمل داخل السيرفر
WORKDIR /var/www/html

# 2. نسخ كل ملفاتك من GitHub إلى داخل السيرفر
COPY . /var/www/html/

# 3. إعطاء صلاحيات كاملة للسيرفر ليقرأ الملفات
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 4. التأكد من تشغيل Apache في الواجهة
CMD ["apache2-foreground"]
