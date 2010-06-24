<?php
lmb_require('limb/constructor/src/lmbARRelationAggrementsResolver.class.php');

class lmbARRelationAggrementsResolverTest extends UnitTestCase
{
  /**
   * @var lmbARRelationAggrementsResolver
   */
  protected $resolver;
  
  function setUp()
  {
    $this->resolver = new lmbARRelationAggrementsResolver();
  }
  
  function testIsRelationColumn()
  {
    $this->assertTrue($this->resolver->isRelationColumn('member_id'));
  }
  
  function testMakeTableNameFromRelationColumn()
  {
    $this->assertEqual(
      $this->resolver->makeTableNameFromRelationColumn('member_id'),
      'member'
    );
  }  
}