<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\CodeQuality\Rector\FuncCall\ArrayMergeOfNonArraysToSimpleArrayRector;
use Rector\CodingStyle\Rector\ClassConst\VarConstantCommentRector;
use Rector\CodingStyle\Rector\ClassMethod\NewlineBeforeNewAssignSetRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchMethodCallReturnTypeRector;
use Rector\Symfony\Set\SensiolabsSetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src'
    ]);

    if(str_contains(strtolower(php_uname('s')), 'window')) {
        $rectorConfig->disableParallel();
    }

    // register a single rule
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

    // define sets of rules
    //    $rectorConfig->sets([
    //        LevelSetList::UP_TO_PHP_81
    //    ]);

    $rectorConfig->sets([
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
        SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
        //NetteSetList::ANNOTATIONS_TO_ATTRIBUTES,
        SensiolabsSetList::FRAMEWORK_EXTRA_61,
        SymfonySetList::SYMFONY_60,
        SymfonySetList::SYMFONY_61,
        SymfonySetList::SYMFONY_62,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION
    ]);

    $rectorConfig->rules([
        NewlineAfterStatementRector::class, // https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#newlineafterstatementrector
        NewlineBeforeNewAssignSetRector::class, // https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#newlinebeforenewassignsetrector
        SymplifyQuoteEscapeRector::class, // https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#symplifyquoteescaperector
        VarConstantCommentRector::class, // https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#varconstantcommentrector
        ArrayMergeOfNonArraysToSimpleArrayRector::class, // https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#arraymergeofnonarraystosimplearrayrector
        // Naming
        RenameForeachValueVariableToMatchMethodCallReturnTypeRector::class, // https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#renameforeachvaluevariabletomatchmethodcallreturntyperector

        // PHP 8.1
        \Rector\Php81\Rector\Class_\ConstantListClassToEnumRector::class, // https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#constantlistclasstoenumrector
        \Rector\Php81\Rector\Class_\SpatieEnumClassToEnumRector::class, // https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#spatieenumclasstoenumrector

        \Rector\Symfony\Rector\MethodCall\ContainerGetToConstructorInjectionRector::class,
        \Rector\Symfony\Rector\ClassMethod\CommandConstantReturnCodeRector::class,
        \Rector\Symfony\Rector\Class_\CommandPropertyToAttributeRector::class,
        \Rector\Symfony\Rector\MethodCall\ChangeStringCollectionOptionToConstantRector::class,
        \Rector\Symfony\Rector\MethodCall\GetHelperControllerToServiceRector::class,
        \Rector\Symfony\Rector\ClassMethod\GetRequestRector::class,
        \Rector\Symfony\Rector\StaticPropertyFetch\KernelTestCaseContainerPropertyDeprecationRector::class,
        \Rector\Symfony\Rector\MethodCall\LiteralGetToRequestClassConstantRector::class,
        \Rector\Symfony\Rector\ClassMethod\ParamConverterAttributeToMapEntityAttributeRector::class,
        \Rector\Symfony\Rector\MethodCall\RedirectToRouteRector::class,
        \Rector\Symfony\Rector\ClassMethod\ReplaceSensioRouteAnnotationWithSymfonyRector::class,
        \Rector\Symfony\Rector\ClassMethod\ResponseReturnTypeControllerActionRector::class,
        \Rector\Symfony\Rector\BinaryOp\ResponseStatusCodeRector::class,
        \Rector\Symfony\Rector\MethodCall\SimplifyFormRenderingRector::class,
    ]);
};
