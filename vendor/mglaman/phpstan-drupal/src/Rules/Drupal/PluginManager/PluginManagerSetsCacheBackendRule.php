<?php declare(strict_types=1);

namespace mglaman\PHPStanDrupal\Rules\Drupal\PluginManager;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Type;
use function array_map;
use function count;
use function sprintf;
use function strpos;

/**
 * @extends AbstractPluginManagerRule<ClassMethod>
 */
class PluginManagerSetsCacheBackendRule extends AbstractPluginManagerRule
{
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$scope->isInClass()) {
            throw new ShouldNotHappenException();
        }

        if ($scope->isInTrait()) {
            return [];
        }

        if ($node->name->name !== '__construct') {
            return [];
        }

        $scopeClassReflection = $scope->getClassReflection();

        if (!$this->isPluginManager($scopeClassReflection)) {
            return [];
        }

        $hasCacheBackendSet = false;
        $misnamedCacheTagWarnings = [];

        foreach ($node->stmts ?? [] as $statement) {
            if ($statement instanceof Node\Stmt\Expression) {
                $statement = $statement->expr;
            }
            if (($statement instanceof Node\Expr\MethodCall) &&
                ($statement->name instanceof Node\Identifier) &&
                $statement->name->name === 'setCacheBackend') {
                // setCacheBackend accepts a cache backend, the cache key, and optional (but suggested) cache tags.
                $setCacheBackendArgs = $statement->getArgs();
                if (count($setCacheBackendArgs) < 2) {
                    continue;
                }
                $hasCacheBackendSet = true;

                $cacheKey = array_map(
                    static fn (Type $type) => $type->getValue(),
                    $scope->getType($setCacheBackendArgs[1]->value)->getConstantStrings()
                );
                if (count($cacheKey) === 0) {
                    continue;
                }

                if (isset($setCacheBackendArgs[2])) {
                    $cacheTagsType = $scope->getType($setCacheBackendArgs[2]->value);
                    foreach ($cacheTagsType->getConstantArrays() as $constantArray) {
                        foreach ($constantArray->getValueTypes() as $valueType) {
                            foreach ($valueType->getConstantStrings() as $cacheTagConstantString) {
                                foreach ($cacheKey as $cacheKeyValue) {
                                    if (strpos($cacheTagConstantString->getValue(), $cacheKeyValue) === false) {
                                        $misnamedCacheTagWarnings[] = $cacheTagConstantString->getValue();
                                    }
                                }
                            }
                        }
                    }
                }

                break;
            }
        }

        $errors = [];
        if (!$hasCacheBackendSet) {
            $errors[] = RuleErrorBuilder::message('Missing cache backend declaration for performance.')
            ->identifier('pluginManagerSetsCacheBackend.missingCacheBackend')
            ->build();
        }
        foreach ($misnamedCacheTagWarnings as $cacheTagWarning) {
            $errors[] = RuleErrorBuilder::message(
                sprintf('%s cache tag might be unclear and does not contain the cache key in it.', $cacheTagWarning)
            )
            ->identifier('pluginManagerSetsCacheBackend.unclearCacheTag')
            ->build();
        }

        return $errors;
    }
}
