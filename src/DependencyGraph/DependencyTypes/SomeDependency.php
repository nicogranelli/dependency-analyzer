<?php
declare(strict_types=1);

namespace DependencyAnalyzer\DependencyGraph\DependencyTypes;

use DependencyAnalyzer\DependencyGraph;

class SomeDependency extends Base
{
    public function getType(): string
    {
        return DependencyGraph::TYPE_SOME_DEPENDENCY;
    }

    public function isEqual(Base $that): bool
    {
        return $this->getType() === $that->getType();
    }
}