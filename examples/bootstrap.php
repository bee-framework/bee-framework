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
 * Time: 14:15
 */

unlink('db/examples.sqlite');

require_once '../framework/Bee/Framework.php';
require_once 'vendor/autoload.php';

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

$conn = $ctx->getBean('pdoConnection');

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// obtaining the entity manager
$entityManager = $ctx->getBean('entityManager');
