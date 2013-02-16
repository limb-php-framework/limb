# Работа с репозиториями для разработчика
ВНИМАНИЕ! Если вы хотите просто получить исходный код Limb и не планируете вносить какие-то изменения, то [все проще](./how_to_download.md).

Все репозитории, относящиеся к Limb, располагаются на сервисе GitHub — http://github.com/limb-php-framework/

## Ветки и тэги репозиториев
Ветками обозначаются версии фреймворка:

* **2010.1** — версия 2010.1
* **master** — изменения, которые не приняты в версию, разрабатываемую в данный момент
* **merging** — ветка для слияния коммитов, которые конфликтуют друг с другом
* **2010.2_php53compatibility** — ветка под конкретную функциональность, которая постепенно сливается с веткой версии(в данном случае 2010.2)

Тэги обозначают статусы:

* 2010.2alpha — alpha-версия, «грубая» реализация всех изменений, запланированных в RoadMap, добавляются новые функции
* 2010.2beta — beta-версия, «чистка» кода, дописывание тестов и документации, внутреннее тестирование, принимаются только мелкие функции
* 2010.2RC — release-candidate, публичное тестирование, функции не добавляются, дописывается документация, исправляются ошибки
* 2010.2stable — полная заморозка кода

## Типичный workflow для разработчика
Допустим изменение, которое мы хотим провести, это удаление файлов с расширением .orig.

### 1. Fork репозитория
Переходим на страницу репозитория, и жмем кнопку «fork». С этого момента у нас есть своя собственная копия репозитория.

### 2. Получаем и настраиваем наш репозиторий
#### Клонирование

Вы можете загрузить код в локальный репозиторий, используя Git, [Mercurial](http://hg-git.github.com/) или [SVN](https://github.com/blog/644-subversion-write-support), по вашему выбору. Я буду показывать на примере консольного Git.

    $ git clone git@github.com:korchasa/limb.git my-limb
    Initialized empty Git repository in /www/my-limb/.git/
    remote: Counting objects: 38777, done.
    remote: Compressing objects: 100% (35696/35696), done.
    remote: Total 38777 (delta 1025), reused 38777 (delta 1025)
    Receiving objects: 100% (38777/38777), 25.83 MiB | 515 KiB/s, done.
    Resolving deltas: 100% (1025/1025), done.
    $ cd my-limb/
    $ git branch
    * master

#### Создание локальной ветки
Мы выбрали из репозитория код. Но пока только ветку мастер. А нам хочется посмотреть и поправить текущую версию 2010.1. Нам необходимо создать локальную ветку, и привязать ее к удаленной:

    $ git checkout --track -b remove_origs origin/2010.1 
    Branch remove_origs set up to track remote branch 2010.1 from origin.
    Switched to a new branch 'remove_origs'
    $ git branch 
    * remove_origs
      master

В данном случае мы связали локальную ветку 'remove_origs' с веткой '2010.1' из репозитория origin(место, откуда мы сделали clone).

### 3. Делаем необходимые нам правки
Теперь вносим необходимые изменения.

    $ find | grep "\.orig" | xargs git rm
    $ git commit 
    $ git push origin 2010.1
    Counting objects: 55, done.
    Delta compression using up to 2 threads.
    Compressing objects: 100% (27/27), done.
    Writing objects: 100% (28/28), 2.46 KiB, done.
    Total 28 (delta 22), reused 4 (delta 1)
    To git@github.com:korchasa/limb.git
       707e561..2420fdc  2010.1 -> 2010.1

### 4. Запрос на внесение изменений
Переходим на сайт GitHub, жмем «Pull Request» и оформляем запрос. После этого кто-нибудь из коммитеров сольет ваши изменения.

### 5. Получение изменений из основного репозитория проекта
Но ведь остальные разработчики не спят! И нам необходимо как-то получать их изменения.

Для этого добавим наш основной удаленный репозиторий(назовем его upstream):

    $ git remote add upstream git://github.com/limb-php-framework/limb.git
    $ git remote
    origin
    upstream

Теперь подтянем изменения из ветки master основного репозитория в нашу ветку remove_origs:

    $ git checkout remove_origs
    $ git pull origin upstream
    $ git push

### 6. Удаление локальной ветки
Ветка remove_origs нам более не нужна, поэтому удаляем ее:

    $ git branch -d remove_origs
    Deleted branch remove_origs (was 742bdfd).

Отличное описание workflow для работы с GitHub'ом — [GitHub Suggested Workflow](http://www.apreche.net/github-suggested-workflow/).

## Этикет

* все коммиты **обязательно надо** сопровождать вразумительными комментариями о том, что было сделано, на **английском языке** (по возможности грамотном)
* не стоит совмещать в одном коммите несвязанные друг с другом изменения
