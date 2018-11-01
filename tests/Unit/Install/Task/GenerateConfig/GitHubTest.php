<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeedTest\Unit\Install\Task\GenerateConfig;

use Composer\IO\IOInterface;
use PeeHaa\AwesomeFeed\Install\Output;
use PeeHaa\AwesomeFeed\Install\Task\GenerateConfig\GitHub;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GitHubTest extends TestCase
{
    /** @var MockObject|Output */
    private $output;

    private $configuration;

    /** @var MockObject|IOInterface */
    private $cliPrompt;

    public function setUp()
    {
        $this->output    = $this->createMock(Output::class);
        $this->cliPrompt = $this->createMock(IOInterface::class);

        $this->configuration = [];
    }

    public function testRunOutputsInfo()
    {
        $github = new GitHub();

        $this->output
            ->method('info')
            ->with('Verifying GitHub configuration')
        ;

        $this->cliPrompt
            ->method('ask')
            ->willReturn('foo')
        ;

        $this->cliPrompt
            ->method('askAndHideAnswer')
            ->willReturnOnConsecutiveCalls('bar', 'baz')
        ;

        $this->assertTrue(is_array($github->run($this->output, $this->configuration, $this->cliPrompt)));
    }

    public function testRunCreatesBaseKey()
    {
        $github = new GitHub();

        $this->cliPrompt
            ->method('ask')
            ->willReturn('foo')
        ;

        $this->cliPrompt
            ->method('askAndHideAnswer')
            ->willReturnOnConsecutiveCalls('bar', 'baz')
        ;

        $configuration = $github->run($this->output, $this->configuration, $this->cliPrompt);

        $this->assertArrayHasKey('gitHub', $configuration);
    }

    public function testRunAsksForClientId()
    {
        $github = new GitHub();

        $this->cliPrompt
            ->expects($this->once())
            ->method('ask')
            ->willReturn('foo')
            ->with('What is the GitHub client id (https://developer.github.com/apps/building-oauth-apps/creating-an-oauth-app/): ')
        ;

        $this->cliPrompt
            ->method('askAndHideAnswer')
            ->willReturnOnConsecutiveCalls('bar', 'baz')
        ;

        $this->assertTrue(is_array($github->run($this->output, $this->configuration, $this->cliPrompt)));
    }

    public function testRunAsksForClientIdRepeatedly()
    {
        $github = new GitHub();

        $this->cliPrompt
            ->expects($this->exactly(2))
            ->method('ask')
            ->willReturnOnConsecutiveCalls('', 'foo')
            ->with('What is the GitHub client id (https://developer.github.com/apps/building-oauth-apps/creating-an-oauth-app/): ')
        ;

        $this->cliPrompt
            ->method('askAndHideAnswer')
            ->willReturnOnConsecutiveCalls('bar', 'baz')
        ;

        $this->assertTrue(is_array($github->run($this->output, $this->configuration, $this->cliPrompt)));
    }

    public function testRunAsksForClientSecret()
    {
        $github = new GitHub();

        $this->cliPrompt
            ->method('ask')
            ->willReturn('foo')
        ;

        $this->cliPrompt
            ->expects($this->at(1))
            ->method('askAndHideAnswer')
            ->willReturn('bar')
            ->with('What is the GitHub client secret (https://developer.github.com/apps/building-oauth-apps/creating-an-oauth-app/): ')
        ;

        $this->cliPrompt
            ->expects($this->at(2))
            ->method('askAndHideAnswer')
            ->willReturn('bar')
        ;

        $this->assertTrue(is_array($github->run($this->output, $this->configuration, $this->cliPrompt)));
    }


    public function testRunAsksForClientSecretRepeatedly()
    {
        $github = new GitHub();

        $this->cliPrompt
            ->method('ask')
            ->willReturn('foo')
        ;

        $this->cliPrompt
            ->expects($this->at(1))
            ->method('askAndHideAnswer')
            ->willReturn('')
            ->with('What is the GitHub client secret (https://developer.github.com/apps/building-oauth-apps/creating-an-oauth-app/): ')
        ;

        $this->cliPrompt
            ->expects($this->at(2))
            ->method('askAndHideAnswer')
            ->willReturn('bar')
            ->with('What is the GitHub client secret (https://developer.github.com/apps/building-oauth-apps/creating-an-oauth-app/): ')
        ;

        $this->cliPrompt
            ->expects($this->at(3))
            ->method('askAndHideAnswer')
            ->willReturn('bar')
        ;

        $this->assertTrue(is_array($github->run($this->output, $this->configuration, $this->cliPrompt)));
    }

    public function testRunAsksForAccessToken()
    {
        $github = new GitHub();

        $this->cliPrompt
            ->method('ask')
            ->willReturn('foo')
        ;

        $this->cliPrompt
            ->expects($this->at(1))
            ->method('askAndHideAnswer')
            ->willReturn('bar')
        ;

        $this->cliPrompt
            ->expects($this->at(2))
            ->method('askAndHideAnswer')
            ->willReturn('bar')
            ->with('What is your personal GitHub access token (https://github.com/settings/tokens): ')
        ;

        $this->assertTrue(is_array($github->run($this->output, $this->configuration, $this->cliPrompt)));
    }

    public function testRunAsksForAccessTokenRepeatedly()
    {
        $github = new GitHub();

        $this->cliPrompt
            ->method('ask')
            ->willReturn('foo')
        ;

        $this->cliPrompt
            ->expects($this->at(1))
            ->method('askAndHideAnswer')
            ->willReturn('bar')
        ;

        $this->cliPrompt
            ->expects($this->at(2))
            ->method('askAndHideAnswer')
            ->willReturn('')
            ->with('What is your personal GitHub access token (https://github.com/settings/tokens): ')
        ;

        $this->cliPrompt
            ->expects($this->at(3))
            ->method('askAndHideAnswer')
            ->willReturn('bar')
            ->with('What is your personal GitHub access token (https://github.com/settings/tokens): ')
        ;

        $this->assertTrue(is_array($github->run($this->output, $this->configuration, $this->cliPrompt)));
    }

    public function testRunOutputsSuccess()
    {
        $github = new GitHub();

        $this->output
            ->expects($this->once())
            ->method('success')
            ->with('GitHub configuration generated')
        ;

        $this->cliPrompt
            ->method('ask')
            ->willReturn('foo')
        ;

        $this->cliPrompt
            ->method('askAndHideAnswer')
            ->willReturn('bar')
        ;

        $this->assertTrue(is_array($github->run($this->output, $this->configuration, $this->cliPrompt)));
    }

    public function testRunSetsNewConfiguration()
    {
        $github = new GitHub();

        $this->cliPrompt
            ->method('ask')
            ->willReturn('foo')
        ;

        $this->cliPrompt
            ->expects($this->at(1))
            ->method('askAndHideAnswer')
            ->willReturn('bar')
        ;

        $this->cliPrompt
            ->expects($this->at(2))
            ->method('askAndHideAnswer')
            ->willReturn('baz')
        ;

        $this->assertSame([
            'gitHub' => [
                'clientId'     => 'foo',
                'clientSecret' => 'bar',
                'accessToken'  => 'baz',
            ],
        ], $github->run($this->output, $this->configuration, $this->cliPrompt));
    }

    public function testRunMaintainsCurrentConfiguration()
    {
        $github = new GitHub();

        $configuration = [
            'gitHub' => [
                'clientId'     => 'foo',
                'clientSecret' => 'bar',
                'accessToken'  => 'baz',
            ],
        ];

        $this->assertSame($configuration, $github->run($this->output, $configuration, $this->cliPrompt));
    }
}
