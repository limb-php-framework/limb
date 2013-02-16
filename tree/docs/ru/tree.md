# Пакет TREE — работа с иерархической информацией(деревья) в БД
## Основные возможности

1. Хранение иерархической информации в БД.
2. Манипуляции с иерархией.
3. Вывод иерархической информации.

## Классы, используемые для работы с деревьями
### lmbMaterializedPathTree

**lmbMaterializedPathTree** — класс, который инкапсулирует операции по хранению, изменению и получению информации об иерархических конструкциях, хранящихся в одной таблице базы данных. По умолчанию для хранения дерева используется таблица sys_tree со следующей структурой:

    CREATE TABLE `sys_tree` (                         
                `id` INT(11) NOT NULL AUTO_INCREMENT,           
                `root_id` INT(11) NOT NULL DEFAULT '0',         
                `parent_id` INT(11) NOT NULL DEFAULT '0',       
                `priority` INT(11) NOT NULL DEFAULT '0',        
                `level` INT(11) NOT NULL DEFAULT '0',           
                `identifier` VARCHAR(128) NOT NULL DEFAULT '',  
                `path` VARCHAR(255) NOT NULL DEFAULT '',        
                `children` INT(11) NOT NULL DEFAULT '0',        
                PRIMARY KEY  (`id`),                            
                KEY `root_id` (`root_id`),                      
                KEY `identifier` (`identifier`),                
                KEY `level` (`level`),                          
                KEY `rlr` (`root_id`),                          
                KEY `parent_id` (`parent_id`),                  
                KEY `id` (`id`,`parent_id`)                     
              ) TYPE=InnoDB

lmbMaterializedPathTree работает с базой данных через пакет Limb3 [DBAL](../../../dbal/docs/ru/dbal.md).
