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
 * Date: Feb 19, 2010
 * Time: 11:51:34 PM
 */

class Bee_AOP_Support_Utils {

    /**
     * Determine the sublist of the <code>candidateAdvisors</code> list that is applicable to the given class.
     * @param candidateAdvisors the Advisors to evaluate
     * @param clazz the target class
     * @return sublist of Advisors that can apply to an object of the given class
     * (may be the incoming List as-is)
     */
    public static function findAdvisorsThatCanApply(array $candidateAdvisors, $classOrClassName) {
        if (count($candidateAdvisors) == 0) {
            return $candidateAdvisors;
        }
        $eligibleAdvisors = array();
        foreach ($candidateAdvisors as $candidate) {
            if (self::canApply($candidate, $classOrClassName)) {
                $eligibleAdvisors[] = $candidate;
            }

        }
        return $eligibleAdvisors;
    }

    /**
     * Can the given advisor apply at all on the given class?
     * <p>This is an important test as it can be used to optimize out a advisor for a class.
     * This version also takes into account introductions (for IntroductionAwareMethodMatchers).
     * @param Bee_AOP_IAdvisor $advisor the advisor to check
     * @param string $targetClassOrClassName class we're testing
     * @return whether the pointcut can apply on any method
     */
    public static function canApply(Bee_AOP_IAdvisor $advisor, $targetClassName) {
        if ($advisor instanceof Bee_AOP_IPointcutAdvisor) {
            return self::canPointcutApply($advisor->getPointcut(), $targetClassName);
        }
        else {
            // It doesn't have a pointcut so we assume it applies.
            return true;
        }
    }

    /**
     * Can the given pointcut apply at all on the given class?
     * <p>This is an important test as it can be used to optimize
     * out a pointcut for a class.
     * @param Bee_AOP_IPointcut $pc the static or dynamic pointcut to check
     * @param string $targetClass the class to test
     * @return whether the pointcut can apply on any method
     */
    public static function canPointcutApply(Bee_AOP_IPointcut $pc, $targetClassName) {
        if (!$pc->getClassFilter()->matches($targetClassName)) {
            return false;
        }

        $methodMatcher = $pc->getMethodMatcher();

        $targetClass = new ReflectionClass($targetClassName);
        $classes = $targetClass->getInterfaces();
        $classes[$targetClassName] = $targetClass;
        foreach ($classes as $class) {
            foreach($class->getMethods() as $method) {
                if ($methodMatcher->matches($method, $targetClass)) {
                    return true;
                }
            }
        }

        return false;
    }
}
