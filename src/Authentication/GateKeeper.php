<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Authentication;

class GateKeeper
{
    /** @var null|User */
    private $user;

    public function isAuthorized(): bool
    {
        return $this->user !== null;
    }

    public function authorize(User $user): void
    {
        $this->user = $user;
    }

    public function deAuthorize(): void
    {
        $this->user = null;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
