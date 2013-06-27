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
 * Date: 21.06.13
 * Time: 14:17
 */

use Treetest\Node;

require_once('bootstrap.php');

function addChild(&$children, Node $node) {
	global $entityManager;
	$entityManager->persist($node);
	array_push($children, $node);
}

$root = new Node('Root Node');
$entityManager->persist($root);

addChild($root->getChildren(), new Node('Child 1-1'));
addChild($root->getChildren(), new Node('Child 1-2'));

$child13 = new Node('Child 1-3');
addChild($root->getChildren(), $child13);

addChild($child13->getChildren(), new Node('Child 1-3-1'));
addChild($child13->getChildren(), new Node('Child 1-3-2'));

$treeDao = $ctx->getBean('treeDao', 'Treetest\TreeDao');

$treeDao->getNestedSetStrategy()->saveStructure($root);

$entityManager->flush();
$entityManager->close();

// new EM instance
$entityManager = $ctx->getBean('entityManager');

$c12 = $entityManager->getRepository('Treetest\Node')->findOneBy(array('name' => 'Child 1-2'));

addChild($c12->getChildren(), new Node('Child 1-2-1'));
addChild($c12->getChildren(), new Node('Child 1-2-2'));

$treeDao->getNestedSetStrategy()->saveStructure($c12);
$entityManager->flush();
$entityManager->close();

// new EM instance
$entityManager = $ctx->getBean('entityManager');
$c12 = $entityManager->getRepository('Treetest\Node')->findOneBy(array('name' => 'Child 1-2'));

//array_pop($c12->getChildren());

$treeDao->getNestedSetStrategy()->saveStructure($c12);
$entityManager->flush();
$entityManager->close();


