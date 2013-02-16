# lmbSelectQuery
lmbSelectQuery allows to partially encapsulate SQL SELECT query. lmbSelectQuery accepts a text template of Select SQL that can be extended. This template SQL must contain several special placeholders where extensions can be inserted into, e.g.: %fields% or %where%. That's why it's more corrent to call lmbSelectQuery as TemplateSelectQuery(on the TODO list).

Here is the list of supported placeholders:

* **%fields%** — extra fields.
* **%tables%** — extra tables.
* **%left_join%** — extra joins.
* **%where%** — extra conditions.
* **%group%** — extra group by fields.
* **%having%** — extra grouping conditions.
* **%order%** — extra order fields.

## An example of lmbSelectQuery

    class CocktailDetailInfoQuery extends lmbSelectQuery
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

## How to extend lmbSelectQuery
**addField($field)**

    $query->addField('dish.long_name');

**addTable($table)**

    $query->addTable('special_method as sm');

**addOrder($field, $type='ASC')**

    $query->addOrder('dish.long_name DESC');

**addGroupBy($group)**

    $query->addGroupBy('cocktail.dish_id');

**addCriteria($criteria)**. Use this method to fill %where% placeholder. $criteria is a [Criteria](./criteria.md) object.

    $query->addCriteria(new lmbSQLTableFieldCriteria('ingredient.id', $ingredient_ids, lmbSQLFieldCriteria :: IN));

**addHaving($criteria)**. Use this method to fill %having% placeholder where $criteria is a [Criteria](./criteria.md) object.

    $query->addHaving(new lmbSQLRawCriteria('count > 0'));

**addLeftJoin($table, $connect_by)**

    $query->addLeftJoin('dish', 'dish.id = cocktail.dish_id');

## An example of lmbSelectQuery usage

    [...]
    function _analizeIngredientCondition($query)
    {
      $ingredient_ids = $this->_getIngredientIdsArray();
      $ingredient_names = $this->_getIngredientNames();
      if(!(sizeof($ingredient_ids) || sizeof($ingredient_names)))
        return false;
 
      $criteria = null;
      if(sizeof($ingredient_ids))
        $criteria = new lmbSQLTableFieldCriteria('ingredient.id', $ingredient_ids, lmbSQLFieldCriteria :: IN);
 
      foreach($ingredient_names as $name)
      {
        if(!$criteria)
          $criteria = new lmbSQLTableFieldCriteria('ingredient.name', '%'.$name.'%', lmbSQLFieldCriteria :: LIKE);
        else
        {
          $new_criteria = new lmbSQLTableFieldCriteria('ingredient.name', '%'.$name.'%', lmbSQLFieldCriteria :: LIKE);
          $criteria->addOr($new_criteria);
        }
      }
 
      $query->addField('COUNT(cocktail.id) as ccount');
      $query->addTable('recipe_item');
      $query->addTable('ingredient');
      $query->addCriteria(new lmbSQLRawCriteria('recipe_item.cocktail_id = cocktail.id'));
      $query->addCriteria(new lmbSQLRawCriteria('recipe_item.ingredient_id = ingredient.id'));
      $query->addOrder('ccount', 'DESC');
      $query->addCriteria($criteria);
 
      $query->addGroupBy('recipe_item.cocktail_id');
      return true;
    }
    [...]
