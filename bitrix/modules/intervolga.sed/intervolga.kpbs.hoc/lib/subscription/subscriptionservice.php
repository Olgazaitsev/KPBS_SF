<?php

namespace Intervolga\Sed\Subscription;

/**
 * @author Nikita Kalinin <kalinin@intervolga.ru>
 */

use Bitrix\Main\Config\Option;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\HttpClient;
use DateTimeImmutable;
use function array_key_exists;
use function is_array;
use function is_string;
use function json_decode;
use const DATE_ATOM;


class SubscriptionService {
    const CACHE_KEY = 'intervolga.sed.subscriptionService.instance';
    const CACHE_TRUST_TIME = 60 * 10; // 10 min.
    const CACHE_TTL = 60 * 60 * 24 * 38; // 38 days.

    private const LICENSE_SERVER = 'https://sed-subscription.nondsm.ivb24.ru';
    private const TIMEOUT_CONNECTION = 2;
    private const TIMEOUT_GET = 1;

    public const STATUS_OK = 'intervolga.sed.subscriptionService.status.OK';
    public const STATUS_EXPIRED = 'intervolga.sed.subscriptionService.status.EXPIRED';
    public const STATUS_USAGE_FORBIDDEN = 'intervolga.sed.subscriptionService.status.USAGE_FORBIDDEN';

    public function checkInstanceSubscription(): string {
        $subscription = $this->getInstanceSubscription();

        if ($subscription == null) {
            return SubscriptionService::STATUS_USAGE_FORBIDDEN;
        }

        if ($subscription->getValidThrough()->getTimestamp() > time()) {
            return SubscriptionService::STATUS_OK;
        }

        $graceSeconds = $subscription->getGraceDays() * 24 * 60 * 60;
        $endTimestamp = $subscription->getValidThrough()->modify('+1 day')->getTimestamp();
        if ($endTimestamp + $graceSeconds > time()) {
            return SubscriptionService::STATUS_EXPIRED;
        } else {
            return SubscriptionService::STATUS_USAGE_FORBIDDEN;
        }
    }

    public function getInstanceSubscription(): ?Subscription {
        $cachedSubscription = $this->getCachedInstanceSubscription();
        if ($cachedSubscription == null || $this->needUpdate($cachedSubscription)) {
            try {
                $subscription = $this->getSubscription($this->getInstanceSubscriptionKey());
                $this->cacheSubscription($subscription);
                return $subscription;
            } catch (SubscriptionException $e) {
                return $cachedSubscription != null ? $cachedSubscription->getSubscription() : null;
            }
        }
        return $cachedSubscription != null ? $cachedSubscription->getSubscription() : null;
    }

    private function getCachedInstanceSubscription(): ?CachedSubscription {
        try {
            $cache = Cache::createInstance();
            $cache->startDataCache(SubscriptionService::CACHE_TTL, SubscriptionService::CACHE_KEY);
            $vars = $cache->getVars();
            if (is_array($vars) && array_key_exists('subscription', $vars)) {
                $license = $vars['subscription'];
            } else {
                return null;
            }

            if (!is_array($license)) {
                throw new InvalidKeyException('Unable to read subscription from cache: cannot decode data.');
            }
            if (!array_key_exists('__cached', $license) || !is_string($license['__cached'])) {
                throw new InvalidKeyException('Unable to read subscription from cache: __cached missing.');
            }
            if (!array_key_exists('id', $license) || !is_string($license['id'])) {
                throw new InvalidKeyException('Unable to read subscription from cache: id missing.');
            }

            $cached = DateTimeImmutable::createFromFormat(DATE_ATOM, $license['__cached']);
            if (!$cached) {
                throw new InvalidKeyException('Unable to read subscription from cache: malformed cache date.');
            }

            $subscription = Subscription::fromArray($license['id'], $license);

            return new CachedSubscription($cached, $subscription);
        } catch (SubscriptionException $e) {
            return null;
        }
    }

    private function needUpdate(CachedSubscription $cs): bool {
        return $cs->getCached()->getTimestamp() + SubscriptionService::CACHE_TRUST_TIME < time();
    }

    private function cacheSubscription(Subscription $subscription): void {
        $data = $subscription->toArray();
        $data['__cached'] = date(DATE_ATOM);

        $cache = Cache::createInstance();
        $cache->forceRewriting(true);
        $cache->startDataCache(SubscriptionService::CACHE_TTL, SubscriptionService::CACHE_KEY);
        $cache->endDataCache(array('subscription' => $data));
    }

    public function expiredMessage(): string {
        return Loc::getMessage('IV_SED_SUB_EXPIRED');
    }

    public function getInstanceSubscriptionKey(): string {
        return Option::get('intervolga.sed', 'subscriptionKey');
    }

    public function setInstanceSubscriptionKey(string $key): void {
        Option::set('intervolga.sed', 'subscriptionKey', $key);
    }

    public function checkSubscription(string $key): void {
        $subscription = $this->getSubscription($key);
        if (!$this->isValidSubscription($subscription)) {
            throw new SubscriptionExpiredException('The specified subscription has expired.');
        }
    }

    private function isValidSubscription(Subscription $s): bool {
        return $s->getValidThrough()->modify('+1 day')->getTimestamp() > time();
    }

    private function getSubscription(string $key): Subscription {
        $httpClient = new HttpClient(array(
                'socketTimeout' => SubscriptionService::TIMEOUT_CONNECTION,
                'streamTimeout' => SubscriptionService::TIMEOUT_GET,
        ));

        $licenseJson = $httpClient->get($this->buildLicenseUrl($key));
        if ($licenseJson == false) {
            throw new LicenseServerInaccessibleException('Unable to get subscription details: license server connection could not be established.');
        }

        if ($httpClient->getStatus() !== 200) {
            throw new LicenseServerInaccessibleException("Unable to get subscription details: unexpected response status {$httpClient->getStatus()} from license server.");
        }

        $license = json_decode($licenseJson, true);
        if (!is_array($license)) {
            throw new InvalidKeyException('Unable to get subscription details: cannot decode data from license server.');
        }

        return Subscription::fromArray($key, $license);
    }

    private function buildLicenseUrl(string $key): string {
        return SubscriptionService::LICENSE_SERVER . '/' . $key . '.sed.json';
    }
}