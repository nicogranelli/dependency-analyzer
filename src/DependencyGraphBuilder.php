<?php
declare(strict_types=1);

namespace DependencyAnalyzer;

use DependencyAnalyzer\DependencyGraph\ExtraPhpDocTagResolver;
use DependencyAnalyzer\DependencyGraphBuilder\UnknownReflectionClass;
use DependencyAnalyzer\Exceptions\LogicException;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use PHPStan\Reflection\ClassReflection;
use ReflectionClass;

class DependencyGraphBuilder
{
    protected $graph;

    /**
     * @var ClassReflection[] $classReflections
     */
    protected $classes = [];

    /**
     * @var array $dependencyMap
     */
    protected $dependencyMap = [];

    /**
     * @var ExtraPhpDocTagResolver
     */
    protected $extraPhpDocTagResolver;

    public function __construct(ExtraPhpDocTagResolver $extraPhpDocTagResolver)
    {
        $this->graph = new Graph;
        $this->extraPhpDocTagResolver = $extraPhpDocTagResolver;
    }

    protected function getVertex(ReflectionClass $class): Vertex
    {
        if ($this->graph->hasVertex($class->getName())) {
            return $this->graph->getVertex($class->getName());
        }

        $vertex = $this->graph->createVertex($class->getName());
        $vertex->setAttribute('reflection', $class);
        $canOnlyUsedByTags = $this->extraPhpDocTagResolver->resolveCanOnlyUsedByTag($class);
        $vertex->setAttribute(ExtraPhpDocTagResolver::ONLY_USED_BY_TAGS, $canOnlyUsedByTags);

        return $vertex;
    }

    protected function getUnknownClassVertex(string $className): Vertex
    {
        if ($this->graph->hasVertex($className)) {
            $vertex = $this->graph->getVertex($className);
            if (!$vertex->getAttribute('reflection') instanceof UnknownReflectionClass) {
                throw new LogicException("{$className} is not UnknownClassReflection");
            }

            return $vertex;
        }

        $vertex = $this->graph->createVertex($className);
        $vertex->setAttribute('reflection', new UnknownReflectionClass($className));
        $vertex->setAttribute(ExtraPhpDocTagResolver::ONLY_USED_BY_TAGS, []);

        return $vertex;
    }

    /**
     * @param ReflectionClass $dependerReflection
     * @param ReflectionClass $dependeeReflection
     */
    public function addDependency(ReflectionClass $dependerReflection, ReflectionClass $dependeeReflection): void
    {
        $depender = $this->getVertex($dependerReflection);
        $dependee = $this->getVertex($dependeeReflection);

        foreach ($depender->getEdgesTo($dependee) as $edge) {
            if ($edge->getAttribute('type') === DependencyGraph::TYPE_SOME_DEPENDENCY) {
                return;
            }
        }

        $edge = $depender->createEdgeTo($dependee);
        $edge->setAttribute('type', DependencyGraph::TYPE_SOME_DEPENDENCY);
    }

    public function addUnknownDependency(ReflectionClass $dependerReflection, string $dependeeName)
    {
        $depender = $this->getVertex($dependerReflection);
        $dependee = $this->getUnknownClassVertex($dependeeName);

        foreach ($depender->getEdgesTo($dependee) as $edge) {
            if ($edge->getAttribute('type') === DependencyGraph::TYPE_SOME_DEPENDENCY) {
                return;
            }
        }

        $edge = $depender->createEdgeTo($dependee);
        $edge->setAttribute('type', DependencyGraph::TYPE_SOME_DEPENDENCY);
//        $unknownClassReflection = new UnknownReflectionClass($dependeeName);
//        $unknownClassReflection->addDepender($dependerReflection->getDisplayName());
//        $this->addDependencyMap($this->getClassReflectionId($dependerReflection), $this->getClassReflectionId($unknownClassReflection));
    }

    public function addMethodCall(ClassReflection $depender, ClassReflection $dependee, string $methodName)
    {
        // TODO
//        $this->addDependencyMap($this->getClassReflectionId($depender), $this->getClassReflectionId($dependee), 'method_call', $methodName);

        // depender = ClassReflection
        // depender part = hoge method
        //
        // dependency type = method call/property fetch/ constant fetch
        //
        // dependee = ClassReflection
        // dependee part = fuga method/fuga property/fugaconstant
    }

    public function addPropertyFetch()
    {
//        $this->addDependencyMap($this->getClassReflectionId($depender), $this->getClassReflectionId($dependee), 'property_fetch', $propertyName);
    }

    public function build(): DependencyGraph
    {
        return new DependencyGraph($this->graph);
    }
}
