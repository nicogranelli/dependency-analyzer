<?php
declare(strict_types=1);

namespace DependencyAnalyzer\DirectedGraph\Formatter;

class UmlFormatter
{
    /**
     * @var \DependencyAnalyzer\DirectedGraph
     */
    private $graph;

    public function __construct(\DependencyAnalyzer\DirectedGraph $graph)
    {
        $this->graph = $graph;
    }

    public function format()
    {
        $output = '@startuml' . PHP_EOL;

        foreach ($this->graph->getEdges() as $edge) {
            $depender = $edge->getVertexStart();
            $dependee = $edge->getVertexEnd();
            $output .= "{$depender->getId()} --> {$dependee->getId()}" . PHP_EOL;
        }

        $output .= '@enduml';

        return $output;
    }
}
