<?php

namespace App\Security;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

/**
 * Class JwtProvider
 */
class JwtProvider
{
    private $secret;

    /**
     * JwtProvider constructor.
     * @param string $secret JWT key
     */
    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * Get JWT token
     * @return string
     */
    public function __invoke(): string
    {
        return (new Builder())
            ->withClaim('mercure', ['publish' => ['*']])
            ->getToken(new Sha256(), new Key($this->secret));
    }
}
