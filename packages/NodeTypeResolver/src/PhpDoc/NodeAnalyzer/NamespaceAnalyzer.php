<?php declare(strict_types=1);

namespace Rector\NodeTypeResolver\PhpDoc\NodeAnalyzer;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Use_;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Php\TypeAnalyzer;

final class NamespaceAnalyzer
{
    /**
     * @var TypeAnalyzer
     */
    private $typeAnalyzer;

    public function __construct(TypeAnalyzer $typeAnalyzer)
    {
        $this->typeAnalyzer = $typeAnalyzer;
    }

    public function resolveTypeToFullyQualified(string $type, Node $node): string
    {
        $useNodes = $node->getAttribute(AttributeKey::USE_NODES);
        if ($useNodes === null) {
            $useNodes = [];
        }

        $useStatementMatch = $this->matchUseStatements($type, $useNodes);
        if ($useStatementMatch) {
            return $useStatementMatch;
        }

        if ($this->typeAnalyzer->isPhpReservedType($type)) {
            return $type;
        }

        // return \absolute values without prefixing
        if (Strings::startsWith($type, '\\')) {
            return ltrim($type, '\\');
        }

        $namespace = $node->getAttribute(AttributeKey::NAMESPACE_NAME);

        return ($namespace ? $namespace . '\\' : '') . $type;
    }

    /**
     * @param Use_[] $useNodes
     */
    private function matchUseStatements(string $type, array $useNodes): ?string
    {
        foreach ($useNodes as $useNode) {
            $useUseNode = $useNode->uses[0];
            if ($useUseNode->alias) {
                $nodeUseName = $useUseNode->alias->name;
            } else {
                $nodeUseName = $useUseNode->name->toString();
            }

            if (Strings::endsWith($nodeUseName, '\\' . $type)) {
                return $nodeUseName;
            }

            // exactly the same
            if ($type === $useUseNode->name->toString()) {
                return $type;
            }

            // alias
            if ($type === $useUseNode->getAlias()->toString()) {
                return $nodeUseName;
            }

            // Some\Start <=> Start\End
            $nodeUseNameParts = explode('\\', $nodeUseName);
            $typeParts = explode('\\', $type);

            $lastNodeUseNamePart = array_pop($nodeUseNameParts);
            $firstTypePart = array_shift($typeParts);

            if ($lastNodeUseNamePart === $firstTypePart) {
                return sprintf(
                    '%s\%s\%s',
                    implode('\\', $nodeUseNameParts),
                    $lastNodeUseNamePart,
                    implode('\\', $typeParts)
                );
            }
        }

        return null;
    }
}
