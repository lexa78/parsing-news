# Разворачивание приложения

1. `git clone https://github.com/lexa78/parsing-news.git`
2. `cd parsing-news`
3. `mv .env.example .env`
4. 
```
  docker run --rm \
         -u "$(id -u):$(id -g)" \
         -v $(pwd):/var/www/html \
         -w /var/www/html \
         laravelsail/php81-composer:latest \
         composer install --ignore-platform-reqs
```
Затем билдим проект `docker-compose up -d`\
Выполняем миграции `docker exec -it parsing-news_laravel.test1 php artisan migrate`\
Делаем ссылку на картинки для показа на странице `docker exec -it parsing-news_laravel.test1 php artisan storage:link`
 
##Приложение развернуто
**Для запуска парсера нужно выполнить команду**\
`docker exec -it parsing-news_laravel.test1 php artisan news:parse`\
**Или**\
`docker exec -it parsing-news_laravel.test1 php artisan news:parse rbk`
\
***Посмотреть результат парсинга можно на [localhost](http://localhost/)***

##Расширение функционала
**Для парсинга другого сайта, нужно сделать следующее:**
* в БД в таблицу **parse_settings** внести информацию о новом сайте.
1. **code** - аргумент, который будет передаваться при запуске команы парсинга. (пример *rbk*)
2. **url** - *url* нового сайта
3. **selectors** - селекторы для выбора нужных частей (ссылки, заголовок, дата публикации и т.д.)
4. **options** - если для доступа к сайту нужны дополнительные настройки, например *заголовки*, то заполняется это поле.
* Создать класс парсера для нового сайта, реализующий интерфейс **SiteParserInterface**. Класс должен находиться по пути 
**App\Service** и его название должно начинаться с кода, который внесли в таблицу **parse_settings** в **code** и 
заканчиваться словом **Parser**
\
##Пример
Если нужно спарсить сайт **google.com**, вносим нужные записи в БД, код, например **google** и создаем класс 
**App\Service\GoogleParser**, реализуем методы из интерфейса и запускаем команду
```docker exec -it parsing-news_laravel.test1 php artisan news:parse google```
