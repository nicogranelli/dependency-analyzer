<?php
declare(strict_types=1);

namespace DependencyAnalyzer;

use DependencyAnalyzer\DependencyDumper\FileDependencyResolver;
use DependencyAnalyzer\Exceptions\UnexpectedException;
use PHPStan\DependencyInjection\ContainerFactory;
use PHPStan\File\FileFinder;

class DependencyDumper
{
    /**
     * @var FileDependencyResolver
     */
    protected $fileDependencyResolver;

    /**
     * @var FileFinder
     */
    protected $fileFinder;

    public function __construct(FileDependencyResolver $fileDependencyResolver, FileFinder $fileFinder)
    {
        $this->fileDependencyResolver = $fileDependencyResolver;
        $this->fileFinder = $fileFinder;
    }

    public static function createFromConfig(string $currentDir, string $tmpDir, array $additionalConfigFiles): self
    {
        $phpStanContainer = (new ContainerFactory($currentDir))->create($tmpDir, $additionalConfigFiles, []);

        return new self(
            $phpStanContainer->getByType(FileDependencyResolver::class),
            $phpStanContainer->getByType(FileFinder::class)
        );
    }

    public function dump(array $paths): DependencyGraph
    {
        $dependencies = [];
        foreach ($this->getFilesRecursive($paths) as $file) {
            $fileDependencies = $this->fileDependencyResolver->dump($file);

            $dependencies = array_merge($dependencies, $fileDependencies);
        }

        return DependencyGraph::createFromArray($dependencies);
    }

    protected function getFilesRecursive(array $paths): array
    {
        try {
            $fileFinderResult = $this->fileFinder->findFiles($paths);
        } catch (\PHPStan\File\PathNotFoundException $e) {
            throw new UnexpectedException('path was not found: ' . $e->getPath());
        }

        return $fileFinderResult->getFiles();
    }
}
