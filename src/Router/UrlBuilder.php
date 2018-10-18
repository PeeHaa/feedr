<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Router;

use PeeHaa\AwesomeFeed\Router\Exception\UndefinedUrl;

class UrlBuilder
{
    private $urls = [];

    private $globalDefaults = [];

    public function __construct(array $globalDefaults = [])
    {
        $this->globalDefaults = $globalDefaults;
    }

    public function addUrl(string $name, string $path): void
    {
        $this->urls[$name] = $this->formatParameters($path);
    }

    private function formatParameters(string $path): string
    {
        $pathParts = $this->getPathParts($path);

        $formattedParts = [];

        foreach ($pathParts as $pathPart) {
            if (!$this->isPartVariable($pathPart)) {
                $formattedParts[] = $pathPart;

                continue;
            }

            $formattedParts[] = $this->getVariablePartName($pathPart);
        }

        return '/' . implode('/', $formattedParts);
    }

    private function getPathParts(string $path): array
    {
        return explode('/', trim($path, '/'));
    }

    private function isPartVariable(string $part): bool
    {
        return (bool) preg_match('~^{[^{}]+}$~', $part);
    }

    private function getVariablePartName($part): string
    {
        preg_match('~^{_?([^:]*).*}$~', $part, $matches);

        return sprintf('{%s}', $matches[1]);
    }

    public function build(string $name, array $variables = []): string
    {
        if (!isset($this->urls[$name])) {
            throw new UndefinedUrl(sprintf('Trying to build an undefined URL `%s`.', $name));
        }

        $url = $this->urls[$name];

        foreach ($variables as $key => $value) {
            $url = str_replace(sprintf('{%s}', $key), $value, $url);
        }

        foreach ($this->globalDefaults as $key => $value) {
            $url = str_replace(sprintf('{%s}', $key), $value, $url);
        }

        return $url;
    }
}
