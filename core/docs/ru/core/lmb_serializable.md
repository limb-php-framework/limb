# Класс lmbSerializable
**lmbSerializable** используется для сериализации и десериализации объектов. Преимуществом использования именно этого класса заключается в том, что он **сам заботится о включении всех файлов классов, которые используются в сериализованных объекте, еще до десериализации этих объектов**.

lmbSerializable применяется следующим образом:

1. объект, который требует сериализации, необходимо обернуть в объект класса lmbSerializable
2. при десериализации нужно вызвать метод getSubject у объекта класса lmbSerializable

lmbSerializable используется активно в [пакете SESSION](../../../../session/docs/ru/session.md) в классе lmbSession.

Пример:

    class lmbSession 
    {
      [...]
      function set($name, $value)
      {
        if(is_object($value))
          $_SESSION[$name] = new lmbSerializable($value);
        else
          $_SESSION[$name] = $value;
 
        $this->touched_names[$name] = true;
      }
 
      function get($name, $empty_value = null)
      {
        if(!isset($_SESSION[$name]))
          return $empty_value;
 
        if(is_object($_SESSION[$name]) && $_SESSION[$name] instanceof lmbSerializable)
          return $_SESSION[$name]->getSubject();
        else
          return $_SESSION[$name];
      }
      [...]
    }
