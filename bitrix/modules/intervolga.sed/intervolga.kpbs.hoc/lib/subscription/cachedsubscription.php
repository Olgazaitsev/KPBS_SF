<?php

namespace Intervolga\Sed\Subscription;

use DateTimeImmutable;

/**
 * @author Nikita Kalinin <kalinin@intervolga.ru>
 */


class CachedSubscription {
    private $cached;
    private $subscription;

    public function __construct(DateTimeImmutable $cached, Subscription $subscription) {
        $this->cached = $cached;
        $this->subscription = $subscription;
    }

    public function getCached(): DateTimeImmutable {
        return $this->cached;
    }

    public function getSubscription(): Subscription {
        return $this->subscription;
    }
}