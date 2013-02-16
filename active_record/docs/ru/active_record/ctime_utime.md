# Автоматическое сохранение даты создания и обновления объекта ActiveRecord

lmbActiveRecord позволяет автоматически сохранять дату создания и обновления обновления объекта. Для этого необходимо лишь создать в соответствующей таблице базы данных поля **ctime** и **utime** типа integer.

Для получения этих данных используются методы **getCreateTime()** и **getUpdateTime()**, например:

    $user = new User();
    $user->setLogin('vasa');
    $user->setPassword('secret');
    $user->save();
 
    echo $user->getCreateTime();
    echo $user->getUpdateTime();
