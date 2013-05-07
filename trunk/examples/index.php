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

$dao = $ctx->getBean('orderedColorsDao');

$dao->createTable();

$dao->addColor('Red', '#ff0000');
$greenId = $dao->addColor('Green', '#00ff00');
$dao->addColor('Blue', '#0000ff');
$dao->addColor('Yellow', '#ffff00');
$purpleId = $dao->addColor('Purple', '#ff00ff');
$dao->addColor('Cyan', '#00ffff');

$dao->getOrderedStrategy()->moveAfter($greenId, $purpleId);

//BeeFramework::dispatchRequestUsingSerializedContext('conf/context.serialized');
