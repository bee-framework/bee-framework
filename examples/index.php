<?php
use Persistence\Pdo\SimpleHierarchyDao;
use Persistence\Pdo\OrderedColorsDao;
/*
 * Copyright 2008-2010 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * User: mp
 * Date: 07.05.13
 * Time: 12:25
 */

require_once '../framework/Bee/Framework.php';

// Verzeichnis mit Applikations-Klassen zum Classpath hinzufügen
Bee_Framework::addApplicationIncludePath('classes');

Bee_Framework::addApplicationIncludePath('../libs/apache-log4php-2.3.0');
Bee_Framework::addApplicationIncludePath('../libs/apache-log4php-2.3.0/helpers');
Bee_Framework::addApplicationIncludePath('../libs/apache-log4php-2.3.0/pattern');
Bee_Framework::addApplicationIncludePath('../libs/apache-log4php-2.3.0/layouts');
Bee_Framework::addApplicationIncludePath('../libs/apache-log4php-2.3.0/appenders');
Bee_Framework::addApplicationIncludePath('../libs/apache-log4php-2.3.0/configurators');
Bee_Framework::addApplicationIncludePath('../libs/apache-log4php-2.3.0/renderers');

Logger::configure('conf/log4php.xml');

$ctx = new Bee_Context_Xml('conf/context.xml');

$ctx->getBean('pdoConnection')->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
/*
 * == EXAMPLE : Ordered Dao =============================================
 */
$dao = $ctx->getBean('orderedColorsDao');

$dao->createTable();

$redId = $dao->addColor('Red', '#ff0000');
$greenId = $dao->addColor('Green', '#00ff00');
$dao->addColor('Blue', '#0000ff');
$dao->addColor('Yellow', '#ffff00');
$purpleId = $dao->addColor('Purple', '#ff00ff');
$cyanId = $dao->addColor('Cyan', '#00ffff');

//$dao->doInTransaction(function(OrderedColorsDao $dao, \Logger $log) use ($greenId, $purpleId) {
//	$dao->getOrderedStrategy()->moveAfter($greenId, $purpleId);
//});
//
//$dao->doInTransaction(function(OrderedColorsDao $dao, \Logger $log) use ($redId, $cyanId) {
//	$dao->getOrderedStrategy()->moveAfter($redId, $cyanId);
//});
//
//$dao->doInTransaction(function(OrderedColorsDao $dao, \Logger $log) use ($purpleId, $redId) {
//	$dao->getOrderedStrategy()->moveBefore($purpleId, $redId);
//});

/*
 * == EXAMPLE : NestedSet Dao =============================================
 */
$dao = $ctx->getBean('simpleHierarchyDao');

$dao->createTable();

$id1 = $dao->addEntry('Entry 1');
$id2 = $dao->addEntry('Entry 2');
$id3 = $dao->addEntry('Entry 3');
$id4 = $dao->addEntry('Entry 4');
$id5 = $dao->addEntry('Entry 5');
$id6 = $dao->addEntry('Entry 6');
$id7 = $dao->addEntry('Entry 7');
$id8 = $dao->addEntry('Entry 8');
$id9 = $dao->addEntry('Entry 9');


$dao->doInTransaction(function(SimpleHierarchyDao $dao, \Logger $log) use ($id9, $id1) {
	$dao->getNestedSetStrategy()->moveAsPrevSibling($id9, $id1);
});

$dao->doInTransaction(function(SimpleHierarchyDao $dao, \Logger $log) use ($id5, $id4) {
	$dao->getNestedSetStrategy()->moveAsFirstChild($id5, $id4);
});

$dao->doInTransaction(function(SimpleHierarchyDao $dao, \Logger $log) use ($id6, $id4) {
	$dao->getNestedSetStrategy()->moveAsFirstChild($id6, $id4);
});

$dao->doInTransaction(function(SimpleHierarchyDao $dao, \Logger $log) use ($id8, $id6) {
	$dao->getNestedSetStrategy()->moveAsLastChild($id8, $id6);
});

$dao->doInTransaction(function(SimpleHierarchyDao $dao, \Logger $log) use ($id1, $id8) {
	$dao->getNestedSetStrategy()->moveAsNextSibling($id1, $id8);
});

/*
 * == END OF EXAMPLES =============================================
 */

echo 'DONE<hr/>';
