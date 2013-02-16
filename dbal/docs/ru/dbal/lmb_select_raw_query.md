# lmbSelectRawQuery
**lmbSelectRawQuery** — при помощи этого класса возможно создавать объектные SQL шаблоны с метками-расширениями. Потомок от [lmb_criteria_query](./lmb_criteria_query.md).

Список возможных меток расширения:

* **%fields%** — для вставки дополнительных полей.
* **%tables%** — для вставки дополнительных таблиц.
* **%left_join%** — для вставки объединений.
* **%where%** — для вставки условий.
* **%group%** — для вставки группировок.
* **%having%** — для вставки условий, связанных с группировкой.
* **%order%** — для вставки сортировок.

Если метка была не заполнена, она в любом случае удаляется из конечного запроса.

## Пример lmbSelectRawQuery

    class CocktailDetailInfoQuery extends lmbSelectRawQuery 
    {
      function  __construct()
      {
        $sql = 'SELECT '.
                'cocktail.id as id, '.
                'cocktail.name as name, '.
                'cocktail.ru_name as ru_name, '.
                'cocktail.description as description, '.
                'cocktail.dish_id as dish_id, '.
                'cocktail.method_id as method_id, '.
                'cocktail.type_id as type_id, '.
                'dish.name as dish_name, '.
                'preparation_method.name as preparation_method_name, '.
                'cocktail_type.name as cocktail_type_name '.
                '%fields% '.
              'FROM '.
                'cocktail '.
                ' LEFT JOIN dish ON dish.id = cocktail.dish_id '.
                ' LEFT JOIN preparation_method ON preparation_method.id = cocktail.method_id '.
                ' LEFT JOIN cocktail_type ON cocktail_type.id = cocktail.type_id '.
                '%tables% ' .
                '%where% %group% %having% %order%';
 
        parent :: __construct($sql);
      }
    } 

## Методы для расширения lmbSelectRawQuery
Для каждой из меток (расширения) есть свой собственный метод для расширения.

**addField($field, $alias = null)** или **field($field, $alias = null)**

    $query->addField('dish.long_name');
    $query->field('dish.long_name', 'dish_full_name');

**addTable($table, $alias = null)** или **from($table, $alias = null)**

    $query->addTable('special_method as sm');
    $query->table('special_method', 'sm');

**addOrder($field, $type='ASC')** или **order($field, $type='ASC')**

    $query->addOrder('dish.long_name');
    $query->order('dish.long_name', 'DESC');

**addGroupBy($group)** или **group($group)**

    $query->addGroupBy('cocktail.dish_id');
    $query->group('cocktail.dish_id');
    
**addCriteria($criteria)** или **where($criteria)**. Для расширения %where% применяется именно эти методы. $criteria — объект из подсистемы [Criteria](./criteria.md). Если в качестве criteria задана обычная строка — она будет автоматически преобразована в объект класса [lmbSQLCriteria](./lmbsql_criteria.md).

    $query->addCriteria(new lmbSQLFieldCriteria('ingredient.id', $ingredient_ids, lmbSQLFieldCriteria :: IN));
    $query->where(lmbSQLCriteria :: in('ingredient.id', $ingredient_ids));

**addHaving($criteria)** или **having($criteria)**. Для расширения %having%% также используются объекты из подсистемы [Criteria](./criteria.md). Если в качестве criteria задана обычная строка — она будет автоматически преобразована в объект класса [lmbSQLCriteria](./lmbsql_criteria.md).

    $query->addHaving(new lmbSQLCriteria('count > 0'));
    $query->having('count > 0');

**addLeftJoin($table, $field, $connect_table, $connect_field)** или **join($table, $field, $connect_table, $connect_field)**

    $query->addLeftJoin('dish', 'id', 'cocktail', 'dish_id');
    $query->join('dish', 'id', 'cocktail', 'dish_id');

## Пример расширения lmbSelectRawQuery
Небольшой кусок кода из класса, который добавляет в query новые условия:

    [...]
    function _analizeIngredientCondition($query)
    {
      $ingredient_ids = $this->_getIngredientIdsArray();
      $ingredient_names = $this->_getIngredientNames();
      if(!(sizeof($ingredient_ids) || sizeof($ingredient_names)))
        return false;
 
      $criteria = null;
      if(sizeof($ingredient_ids))
        $criteria = new lmbSQLCriteria :: in('ingredient.id', $ingredient_ids);
 
      foreach($ingredient_names as $name)
      {
        if(!$criteria)
          $criteria = new lmbSQLCriteria :: like('ingredient.name', '%'.$name.'%');
        else
        {
          $new_criteria = new lmbSQLCriteria :: like('ingredient.name', '%'.$name.'%');
          $criteria->addOr($new_criteria);
        }
      }
 
      $query->addField('COUNT(cocktail.id) as ccount');
      $query->addTable('recipe_item');
      $query->addTable('ingredient');
      $query->addCriteria('recipe_item.cocktail_id = cocktail.id');
      $query->addCriteria('recipe_item.ingredient_id = ingredient.id');
      $query->addOrder('ccount', 'DESC');
      $query->addCriteria($criteria);
 
      $query->addGroupBy('recipe_item.cocktail_id');
      return true;
    }
    [...]
