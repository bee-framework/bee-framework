<?php
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

require_once('bootstrap.php');

use Persistence\Pdo\SimpleHierarchyDao;
use Persistence\Pdo\OrderedColorsDao as OrderedColorsDaoPdo;
use Persistence\Doctrine\OrderedColorsDao as OrderedColorsDaoDoctrine;

/*
 * == EXAMPLE : Ordered Dao (PDO) =============================================
 */
$dao = $ctx->getBean('orderedColorsDaoPdo');

$dao->createTable();
$dao->deleteAllColors();

$redId = $dao->addColor('Red', '#ff0000', 1);
$greenId = $dao->addColor('Green', '#00ff00', 1);
$blueId = $dao->addColor('Blue', '#0000ff', 1);
$dao->addColor('Yellow', '#ffff00', 1);
$purpleId = $dao->addColor('Purple', '#ff00ff', 1);
$cyanId = $dao->addColor('Cyan', '#00ffff', 1);

$redId2 = $dao->addColor('Red2', '#ff0000', 2);
$greenId2 = $dao->addColor('Green2', '#00ff00', 2);
$blueId2 = $dao->addColor('Blue2', '#0000ff', 2);
$yellowId2 = $dao->addColor('Yellow2', '#ffff00', 2);

$dao->doInTransaction(function(OrderedColorsDaoPdo $dao, \Logger $log) use ($greenId, $purpleId) {
	$dao->getOrderedStrategy()->moveAfter($greenId, $purpleId);
});

$dao->doInTransaction(function(OrderedColorsDaoPdo $dao, \Logger $log) use ($redId, $cyanId) {
	$dao->getOrderedStrategy()->moveAfter($redId, $cyanId);
});

$dao->doInTransaction(function(OrderedColorsDaoPdo $dao, \Logger $log) use ($purpleId, $redId) {
	$dao->getOrderedStrategy()->moveBefore($purpleId, $redId);
});

$dao->doInTransaction(function(OrderedColorsDaoPdo $dao, \Logger $log) use ($blueId) {
	$dao->getOrderedStrategy()->remove($blueId);
});

$dao->doInTransaction(function(OrderedColorsDaoPdo $dao, \Logger $log) use ($redId) {
	$dao->getOrderedStrategy()->remove($redId);
});

/*
 * == EXAMPLE : Ordered Dao (Doctrine) =============================================
 */
$dao = $ctx->getBean('orderedColorsDaoDoctrine');

$dao->createTable();
$dao->deleteAllColors();

$redId = $dao->addColor('Red', '#ff0000', 1);
$greenId = $dao->addColor('Green', '#00ff00', 1);
$blueId = $dao->addColor('Blue', '#0000ff', 1);
$dao->addColor('Yellow', '#ffff00', 1);
$purpleId = $dao->addColor('Purple', '#ff00ff', 1);
$cyanId = $dao->addColor('Cyan', '#00ffff', 1);

$redId2 = $dao->addColor('Red2', '#ff0000', 2);
$greenId2 = $dao->addColor('Green2', '#00ff00', 2);
$blueId2 = $dao->addColor('Blue2', '#0000ff', 2);
$yellowId2 = $dao->addColor('Yellow2', '#ffff00', 2);

$dao->doInTransaction(function(OrderedColorsDaoDoctrine $dao, \Logger $log) use ($greenId, $purpleId) {
	$dao->getOrderedStrategy()->moveAfter($greenId, $purpleId);
});

$dao->doInTransaction(function(OrderedColorsDaoDoctrine $dao, \Logger $log) use ($redId, $cyanId) {
	$dao->getOrderedStrategy()->moveAfter($redId, $cyanId);
});

$dao->doInTransaction(function(OrderedColorsDaoDoctrine $dao, \Logger $log) use ($purpleId, $redId) {
	$dao->getOrderedStrategy()->moveBefore($purpleId, $redId);
});

$dao->doInTransaction(function(OrderedColorsDaoDoctrine $dao, \Logger $log) use ($blueId) {
	$dao->getOrderedStrategy()->remove($blueId);
});

$dao->doInTransaction(function(OrderedColorsDaoDoctrine $dao, \Logger $log) use ($redId) {
	$dao->getOrderedStrategy()->remove($redId);
});

/*
 * == EXAMPLE : NestedSet Dao =============================================
 */
$dao = $ctx->getBean('simpleHierarchyDao');

$dao->createTable();
$dao->deleteAll();

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
	$dao->getNestedSetStrategy()->moveBefore($id9, $id1);
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
	$dao->getNestedSetStrategy()->moveAfter($id1, $id8);
});

$dao->doInTransaction(function(SimpleHierarchyDao $dao, \Logger $log) use ($id6) {
	$dao->getNestedSetStrategy()->remove($id6);
});

$dao->doInTransaction(function(SimpleHierarchyDao $dao, \Logger $log) use ($id6, $id9) {
	$dao->getNestedSetStrategy()->moveAsLastChild($id6, $id9);
});

/*
 * == END OF EXAMPLES =============================================
 */

echo 'DONE<hr/>';
