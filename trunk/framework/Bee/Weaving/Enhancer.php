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
 * Time: 16:17
 */

use Bee\Framework;

require_once 'Source/Block.php';

class Bee_Weaving_Enhancer {

    const NAME_T_OPEN_TAG = 'T_OPEN_TAG';

    const NAME_T_CLASS = 'T_CLASS';
    const NAME_T_FUNCTION = 'T_FUNCTION';

    const NAME_T_STATIC = 'T_STATIC';
    const NAME_T_ABSTRACT = 'T_ABSTRACT';

    const NAME_T_IMPLEMENTS = 'T_IMPLEMENTS';

    const NEWLINE = "\n";

    const ENHANCED_SUFFIX = '__enhancedByBeeFramework';

    private static $possibleModifiers = array(
        self::NAME_T_ABSTRACT => 'abstract',
        'T_PRIVATE' => 'private',
        'T_PUBLIC' => 'public',
        'T_PROTECTED' => 'protected',
        self::NAME_T_STATIC => 'static'
    );

    private static $relevantBlocks = array(
        self::NAME_T_CLASS => 'class',
        self::NAME_T_FUNCTION => 'function'
    );

    private $reflectionMethodAccessible = false;

    private $className;

    private $blockStack = array();

    /**
     * @var Bee_Weaving_Source_Block
     */
    private $tos;

    private $nestingDepth = 0;

    private $nextBlockModifiers = array();

    private $nextBlockType = false;

    private $nextBlockPreHeader = array();

    private $nextBlockName = false;

    private $nextBlockPostHeader = array();

	private $nextBlockImplements = false;

	private $startingPhpTagStripped = false;

    public function __construct($className) {
        $this->className = $className;
        $this->reflectionMethodAccessible = method_exists('ReflectionMethod', 'setAccessible');
    }

    public function createEnhancedClass() {

        $classNameToCreate = $this->className;
		$templateClassName = $classNameToCreate;

//        if(class_exists($classNameToCreate, false) /* && !Types::implementsInterface($classNameToCreate, '')  || interface_exists($classNameToCreate, false)*/) {
//            // must create subclass
//			$classNameToCreate .= self::ENHANCED_SUFFIX;
//        }

		if(class_exists($classNameToCreate) && method_exists($classNameToCreate, 'setMethodInterceptor')) {
			return $classNameToCreate;
		}

		if(!Framework::getEnhancedClassesStore()->hasStoredClass($classNameToCreate)) {
			$incPaths = explode(PATH_SEPARATOR, get_include_path());

			foreach(Framework::getClassFileLocations($templateClassName) as $loc) {
				foreach($incPaths as $incPath) {
					$classFile = $incPath . DIRECTORY_SEPARATOR . $loc;
					if(file_exists($classFile)) {
						$this->enhanceClass(file_get_contents($classFile));
						Framework::getEnhancedClassesStore()->storeClass($classNameToCreate, $this->toSourceCode());
//						file_put_contents($enhancedClassLocation, $this->toSourceCode());
						break 2;
					}
				}
			}
		}

		if (Framework::getEnhancedClassesStore()->loadClass($classNameToCreate)) {
			return $classNameToCreate;
		}
        return false;
    }

    public function getClassName() {
        return $this->className;
    }

    protected function enhanceClass($classSource) {
        $this->parseClass($classSource);
        $this->walkTree($this->tos->bodyTokens);
    }

    private function walkTree(array &$currentBodyTokens) {

        $proxyTokens = array();

        foreach($currentBodyTokens as $token) {

            if($token instanceof Bee_Weaving_Source_Block) {

                if($token->type === self::NAME_T_CLASS) {
                    $this->augmentClass($token);
                } else if($token->type === self::NAME_T_FUNCTION) {
                    $this->augmentFunction($token, $proxyTokens);
                }

                $this->walkTree($token->bodyTokens);
            }
        }

        $this->appendTokens($currentBodyTokens, $proxyTokens);
    }

    private function augmentFunction(Bee_Weaving_Source_Block $functionBlock, &$return) {
        if($functionBlock->parentBlock !== null && $functionBlock->parentBlock->type === self::NAME_T_CLASS &&
           !array_key_exists(self::NAME_T_STATIC, $functionBlock->modifiers) && !array_key_exists(self::NAME_T_ABSTRACT, $functionBlock->modifiers)) {

            if(!$this->reflectionMethodAccessible) {
                $functionBlock->preHeaderTokens = array_diff($functionBlock->preHeaderTokens, array('private', 'protected'));
            }
            $methodDef = implode(
                array(
                    self::NEWLINE,
                    '   '.$functionBlock->headerToSourceCode().' {'.self::NEWLINE,
                    '       $args = func_get_args(); '.self::NEWLINE,
                    '       return $this->getMethodInterceptor()->intercept($this, \'' . $functionBlock->name . '\', $this->getMethodForName(\'' . $functionBlock->name . '\'), $args);'.self::NEWLINE,
                    '   }'.self::NEWLINE,
                    self::NEWLINE
                )
            );

            $functionBlock->name .= self::ENHANCED_SUFFIX;
            $return[] = $methodDef;
        }
    }

    private function augmentClass(Bee_Weaving_Source_Block $classBlock) {
		if($classBlock->implementsIndex === false) {
			// class implements no interfaces, need to
		}
        $this->appendTokens($classBlock->bodyTokens, $this->createMethodInterceptorPropertyCode());
    }

    private function appendTokens(&$bodyTokens, $additionalTokens) {
        $lastToken = array_pop($bodyTokens);
        foreach($additionalTokens as $token) {
            array_push($bodyTokens, $token);
        }
        array_push($bodyTokens, $lastToken);
    }

    private function createMethodInterceptorPropertyCode() {
        return array(
			self::NEWLINE,
            '   // GENERATED BY BEE FRAMEWORK WEAVING' . self::NEWLINE,
            self::NEWLINE,
            '   private $methodInterceptor;' .self::NEWLINE,
            self::NEWLINE,
            '   private $methodsByName = array();' .self::NEWLINE,
            self::NEWLINE,
            '   public function setMethodInterceptor(Bee_Weaving_Callback_IMethodInterceptor $methodInterceptor) { $this->methodInterceptor = $methodInterceptor; }' . self::NEWLINE,
            self::NEWLINE,
            '   private function getMethodInterceptor() {'                                      . self::NEWLINE,
            '       if($this->methodInterceptor == null) {'                                     . self::NEWLINE,
            '           $this->methodInterceptor = new Bee_Weaving_Callback_PassthroughInterceptor();'   . self::NEWLINE,
            '       }'                                                                          . self::NEWLINE,
            '       return $this->methodInterceptor;'                                           . self::NEWLINE,
            '   }'                                                                              . self::NEWLINE,
            self::NEWLINE,
            '   private function getMethodForName($methodName) {'                               . self::NEWLINE,
            '       if(!array_key_exists($methodName, $this->methodsByName)) {'                 . self::NEWLINE,
            '           $this->methodsByName[$methodName] = new Bee_Weaving_Callback_Method_Reflection($this, __CLASS__, $methodName.\''.self::ENHANCED_SUFFIX.'\');'. self::NEWLINE,
            '       }'                                                                          . self::NEWLINE,
            '       return $this->methodsByName[$methodName];'                                  . self::NEWLINE,
            '   }'
        );
    }

    private function parseClass($src) {

        $tokens = token_get_all($src);

        $this->tos = new Bee_Weaving_Source_Block();

        foreach($tokens as $token) {
            if(is_array($token)) {
                $tokenType = token_name($token[0]);
                $tokenValue = $token[1];

				if($tokenType === self::NAME_T_OPEN_TAG && !$this->startingPhpTagStripped) {
					$tokenValue = false;
					$this->startingPhpTagStripped = true;
				} else if(array_key_exists($tokenType, self::$possibleModifiers)) {
                    $this->nextBlockModifiers[$tokenType] = self::$possibleModifiers[$tokenType];
                } else if(array_key_exists($tokenType, self::$relevantBlocks)) {
                    $this->nextBlockType = $tokenType;
                } else if($tokenType === 'T_STRING' && $this->nextBlockType && !$this->nextBlockName) {
                    $this->nextBlockName = $tokenValue;
                    $tokenValue = false;
//				} else if($tokenType === self::NAME_T_IMPLEMENTS && $this->nextBlockType && $this->nextBlockName) {
//					$this->nextBlockImplements = true;
                }

                $this->writeToken($tokenValue, $tokenType);

            } else {
                $tokenValue = $token;

                if($tokenValue === ';') {
                    if($this->checkBlockBegins()) {
                    }
                    $this->closeBlock();
                    $this->writeToken($tokenValue);
                } else if($tokenValue === '{') {
                    $this->checkBlockBegins();
                    $this->writeToken($tokenValue);
                } else if($tokenValue === '}') {
                    $this->writeToken($tokenValue);
                    $this->closeBlock();
                } else {
                    $this->writeToken($tokenValue);
                }
            }
        }
    }

    protected function toSourceCode() {
        return $this->tos->toSourceCode();
    }

    /**
     * todo
     * @return mixed
     */
//    protected function getEnhancedSource() {
//        return Manager::retrieveCachable(new Bee_Weaving_Enhancer_Cacheable($this));
//    }

    private function checkBlockBegins() {
        $blockOpened = false;
        if($this->nextBlockName) {
            $blockOpened = true;

            $this->blockStack['_'.$this->nestingDepth] = $this->tos;

            $prevTos = $this->tos;

            $this->tos = new Bee_Weaving_Source_Block();

            $this->tos->parentBlock = $prevTos;
            $this->tos->modifiers = $this->nextBlockModifiers;
            $this->tos->type = $this->nextBlockType;
            $this->tos->preHeaderTokens = $this->nextBlockPreHeader;
            $this->tos->name = $this->nextBlockName;
            $this->tos->postHeaderTokens = $this->nextBlockPostHeader;
			$this->tos->implementsIndex = $this->nextBlockImplements;
        }

        $this->nestingDepth++;

        $this->nextBlockModifiers = array();
        $this->resetNextBlockData();

        return $blockOpened;
    }

    private function closeBlock() {
        $this->nestingDepth--;

        if(array_key_exists('_'.$this->nestingDepth, $this->blockStack)) {

            $prevTos = $this->tos;

            $this->tos = $this->blockStack['_'.$this->nestingDepth];
            unset($this->blockStack['_'.$this->nestingDepth]);

            array_push($this->tos->bodyTokens, $prevTos);
        }

        $this->resetNextBlockData();
    }

    private function resetNextBlockData() {
        foreach($this->nextBlockPreHeader as $token) {
            $this->writeToken($token);
        }
        $this->nextBlockModifiers = array();
        $this->nextBlockType = false;
        $this->nextBlockPreHeader = array();
        $this->nextBlockName = false;
        $this->nextBlockPostHeader = array();
		$this->nextBlockImplements = false;
    }


    private function writeToken($token, $tokenType = false) {
        if($token !== false) {
            if(!$this->nextBlockName && (count($this->nextBlockModifiers) > 0 || $this->nextBlockType)) {
                array_push($this->nextBlockPreHeader, $token);
            } else if($this->nextBlockName) {
				if($tokenType === self::NAME_T_IMPLEMENTS && $this->nextBlockType && $this->nextBlockName) {
					$this->nextBlockImplements = count($this->nextBlockPostHeader);
                }
                array_push($this->nextBlockPostHeader, $token);
            } else {
                array_push($this->tos->bodyTokens, $token);
            }
        }
    }
}

/**
 * todo: use this in a sensible way...
 */
//class Bee_Weaving_Enhancer_Cacheable implements ICachableResource {
//
//    /**
//     * @var Bee_Weaving_Enhancer
//     */
//    private $enhancer;
//
//    public function __construct(Bee_Weaving_Enhancer $enhancer) {
//        $this->enhancer = $enhancer;
//    }
//
//    public function getKey() {
//        return $this->enhancer->getClassName() . Bee_Weaving_Enhancer::FUNCTION_ENHANCED_SUFFIX;
//    }
//
//    public function getModificationTimestamp() {
//        return filemtime($this->enhancer->getClassName());
//    }
//
//    public function &createContent() {
//        $this->enhancer->enhanceClass();
//        return $this->enhancer->toSourceCode();
//    }
//
//}