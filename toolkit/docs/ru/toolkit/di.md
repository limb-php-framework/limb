# Зачем нужен пакет Toolkit
Некоторые объекты являются особо популярными, и зачастую требуется иметь глобальный доступ к таким объектам в рамках всей системы. К таким объектам можно отнести Запрос, Ответ, Пользователь, различные фабрики и т.д.

Некоторые разработчики реализуют такие классы в виде одиночек (**Singleton**). Но паттерн одиночки в последнее время воспринимается двояко, некоторые признают в нем anti-pattern, то есть как неверное решение, использование которого может повредить проекту.

Другая альтернатива — это воспользоваться методиками для инверсии зависимостей и применить принципы **Dependency Injection** или **Service Locator**.

## Что такое Dependency Injection?
Принципы Dependency Injection и Dependency Pullup хорошо описаны в статье [Инверсия зависимостей при проектировании Объектно-Ориентированных систем](http://wiki.agiledev.ru/doku.php?id=ooad:dependency_injection).

Подробнее о инверсии зависимостей можно узнать из статьи Мартина Фаулера [Inversion of Control Containers and the Dependency Injection pattern](http://martinfowler.com/articles/injection.html)

Также можно почитать о паттерне [ServiceLocator в статье Java J2EE](http://www.oracle.com/technetwork/java/index.html)

## Service Locator
Service Locator — это активная форма получения зависимых объектов, относится к Dependency Pullup. Нам этот способ показался более удобным и наглядным, чем классический Dependency Injection. Пакет TOOLKIT — это реализация расширенной версии этого паттерна Dynamic Service Locator.
