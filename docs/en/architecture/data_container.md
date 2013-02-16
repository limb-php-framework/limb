# Data containers
All data containers used in Limb implement two interfaces: DataSource and Iterator.

## UML charts
### Non-list data containter implementing DataSource
### List data containter implementing DataSourceIterator
## Examples
### Iteration
Let's define a small array of data, create an ArrayDataSet using this array and iterate over it using Iterator interface.

    $dataset = array (
      array ('username'=>'jdoe','email'=>'jdoe@hotmail.com'),
      array ('username'=>'rsmith','email'=>'rsmith@cure.org'),
      array ('username'=>'nclark','email'=>'nclark@yahoo.com'),
    );
 
    $rs = new ArrayDataSet($dataset);
 
    $str = '';
    for($rs->rewind(); $rs->valid(); $rs->next())
    {
      $record = $rs->current();
      $str .= 'User: '. $record->get('username') . ' ';
      $str .= 'Email: '. $record->get('email') . "\n";
    }

Since Iterator interface is builtin in php5 you can simplify «foreach» usage a bit:

    $dataset = array (
      array ('username'=>'jdoe','email'=>'jdoe@hotmail.com'),
      array ('username'=>'rsmith','email'=>'rsmith@cure.org'),
      array ('username'=>'nclark','email'=>'nclark@yahoo.com'),
    );
 
    $rs = new ArrayDataSet($dataset);
 
    $str = '';
    foreach($rs as $record)
    {
      $str .= 'User: '. $record->get('username') . ' ';
      $str .= 'Email: '. $record->get('email') . "\n";
    }

### Pagination
The following example will display only two first records.

    $dataset = array (
      array ('username'=>'jdoe','email'=>'jdoe@hotmail.com'),
      array ('username'=>'rsmith','email'=>'rsmith@cure.org'),
      array ('username'=>'nclark','email'=>'nclark@yahoo.com'),
    );
 
    $pager = new lmbPagerComponent();
    $pager->setItemsPerPage(2);
 
    $rs = new PagedArrayDataSet($dataset);
    $rs->paginate($pager);
 
    $str = '';
    foreach($rs as $record)
    {
      $str .= 'User: '. $record->get('username') . ' ';
      $str .= 'Email: '. $record->get('email') . "\n";
    }
