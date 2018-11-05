<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Presentation\Template;

use CodeCollab\CsrfToken\Token;
use CodeCollab\I18n\Translator;
use PeeHaa\AwesomeFeed\Authentication\GateKeeper;
use PeeHaa\AwesomeFeed\Router\UrlBuilder;
use PeeHaa\AwesomeFeed\WebSocket\Configuration;

class Html
{
    private $basePage;

    private $templatePath;

    private $translator;

    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.WriteOnlyProperty
    private $csrfToken;

    private $urlBuilder;

    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.WriteOnlyProperty
    private $gateKeeper;

    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.WriteOnlyProperty
    private $webSocketConfiguration;

    private $_variables;

    public function __construct(
        string $basePage,
        string $templatePath,
        Translator $translator,
        Token $csrfToken,
        UrlBuilder $urlBuilder,
        GateKeeper $gateKeeper,
        Configuration $webSocketConfiguration
    ) {
        $this->basePage               = $basePage;
        $this->templatePath           = $templatePath;
        $this->translator             = $translator;
        $this->csrfToken              = $csrfToken;
        $this->urlBuilder             = $urlBuilder;
        $this->gateKeeper             = $gateKeeper;
        $this->webSocketConfiguration = $webSocketConfiguration;
    }

    public function render(string $template, array $data = []): string
    {
        $backupVariables = $this->_variables;

        if (count($data) > 0) {
            $this->_variables = $data;
        }

        try {
            ob_start();

            (function() use ($template) {
                extract($this->_variables);

                /** @noinspection PhpIncludeInspection */
                require $this->templatePath . $template;
            })();
        } finally {
            $output = ob_get_clean();
        }

        $this->_variables = $backupVariables;

        return $output;
    }

    public function renderPage(string $template, array $data = []): string
    {
        $this->_variables = $data;

        /** @noinspection PhpUnusedLocalVariableInspection */
        $content = $this->render($template, $data);

        try {
            ob_start();

            // phpcs:ignore SlevomatCodingStandard.Functions.UnusedInheritedVariablePassedToClosure.UnusedInheritedVariable
            (function() use ($content) {
                extract($this->_variables);

                /** @noinspection PhpIncludeInspection */
                require $this->templatePath . $this->basePage;
            })();
        } finally {
            $output = ob_get_clean();
        }

        return $output;
    }

    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod
    private function translate(string $key, array $data = []): string
    {
        return $this->translator->translate($key, $data);
    }

    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod
    private function escape(string $data): string
    {
        if (strpos($data, 'javascript:') === 0) {
            $data = substr($data, 11);
        }

        return htmlspecialchars($data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod
    private function url(string $name, ...$variables): string
    {
        return $this->urlBuilder->build($name, ...$variables);
    }
}
