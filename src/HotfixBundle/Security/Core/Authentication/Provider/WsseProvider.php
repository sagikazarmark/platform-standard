<?php

namespace HotfixBundle\Security\Core\Authentication\Provider;

use Oro\Bundle\UserBundle\Security\WsseAuthProvider;

class WsseProvider extends WsseAuthProvider
{
    /**
     * @var int
     */
    protected $clockSkew;

    /**
     * @param int $clockSkew
     */
    public function setClockSkew($clockSkew)
    {
        $this->clockSkew = $clockSkew;
    }

    protected function isTokenFromFuture($created)
    {
        return strtotime($created) - $this->clockSkew > strtotime($this->getCurrentTime());
    }
}
