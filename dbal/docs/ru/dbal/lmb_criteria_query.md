# lmbCriteriaQuery
**lmbCriteriaQuery** — это абстрактный класс, который позволяет добавлять сложные условия в SQL-запросы. Наследуется от класса [lmb_template_query](./lmb_template_query.md). lmbCriteriaQuery добавляет поддержку метки вида %where% в SQL-шаблон. Условия, добавляемые в запрос, реализуются в виде классов подсистемы [Criteria](./criteria.md).

Для добавления новых условий в запрос, используется метод lmbCriteriaQuery :: **addCriteria($criteria)**, например:

    $query->addCriteria(new lmbSQLRawCriteria('recipe_item.cocktail_id = cocktail.id'));
    $criteria = new lmbSQLFieldCriteria('ingredient.name', '%'.$name.'%', lmbSQLFieldCriteria :: LIKE);
    $query->addCriteria($criteria);

Для addCriteria() есть алиас **where($criteria)**.

Если в качестве $criteria пришла обычная строка, то она будет автоматически обвернута в объект класса lmbSQLCriteria():

    $query->where('recipe_item.cocktail_id = cocktail.id');

[lmb_select_query](./lmb_select_query.md), [lmb_update_query](./lmb_update_query.md) и [lmb_delete_query](./lmb_delete_query.md) наследуются от lmbCriteriaQuery.
