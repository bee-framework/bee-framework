<?php
namespace Bee\Security\Acls\Impl;
/*
 * Copyright 2008-2014 the original author or authors.
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
use Bee\Framework;
use Bee\Security\Acls\IAccessControlEntry;
use Bee\Security\Acls\IAuditableAccessControlEntry;
use Bee\Security\Acls\IAuditLogger;
use Bee\Utils\Assert;
use Bee\Utils\TLogged;
use Logger;

/**
 * User: mp
 * Date: Mar 18, 2010
 * Time: 12:51:38 AM
 */

class BasicAuditLogger implements IAuditLogger {
    use TLogged;

	/**
	 * @param $granted
	 * @param IAccessControlEntry $ace
	 */
    public function logIfNeeded($granted, IAccessControlEntry $ace) {
		Assert::notNull($ace, 'AccessControlEntry required');
        if ($ace instanceof IAuditableAccessControlEntry) {
            if ($granted && $ace->isAuditSuccess()) {
				$this->getLog()->info('GRANTED due to ACE: ' . $ace->getId());
            } else if (!$granted && $ace->isAuditFailure()) {
				$this->getLog()->info('DENIED due to ACE: ' . $ace->getId());
            }
        }
    }
}
