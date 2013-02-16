# lmbTimePeriod
Класс lmbTimePeriod — представляет период времени (даты без чисел). Наследник от [lmbDatePeriod](./lmb_date_period.md).

Помимо тех методов, что есть в lmbDatePeriod есть также фабричный метод lmbTimePeriod :: **getDatePeriod($date)**, который позволяет на основе временного периода создать объект класса lmbDatePeriod, где в качестве числа будут использованы данные из $date.
