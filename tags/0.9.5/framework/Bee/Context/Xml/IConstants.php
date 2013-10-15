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
 * Date: 04.07.11
 * Time: 00:46
 */
 
interface Bee_Context_Xml_IConstants {

    const BEAN_NAME_DELIMITERS = ',; ';

    const DESCRIPTION_ELEMENT = 'description';

    const REF_ATTRIBUTE = 'ref';

    const VALUE_ATTRIBUTE = 'value';

    const SCOPE_ATTRIBUTE = 'scope';

    const PARENT_ATTRIBUTE = 'parent';

    const NAME_ATTRIBUTE = 'name';

    const DEPENDS_ON_ATTRIBUTE = 'depends-on';

    const VALUE_TYPE_ATTRIBUTE = 'value-type';

    const ASSOC_ITEM_ELEMENT = 'assoc-item';

    const KEY_ATTRIBUTE = 'key';

}
