<?php

namespace Intervolga\Sed\Subscription;

use Bitrix\Main\Localization\Loc;
use RuntimeException;
use function ShowError;

/**
 * @author Nikita Kalinin <kalinin@intervolga.ru>
 */


final class Subscriptions {
    private static $serviceInstance = null;

    private function __construct() {
    }

    public static function checkForUiComponent(): bool {
        $status = Subscriptions::service()->checkInstanceSubscription();
        if ($status == SubscriptionService::STATUS_OK) {
            return true;
        } elseif ($status == SubscriptionService::STATUS_EXPIRED) {
            ShowError(Loc::getMessage('IV_SED_SUB_EXPIRED'));
            return true;
        } elseif ($status == SubscriptionService::STATUS_USAGE_FORBIDDEN) {
            ShowError(Loc::getMessage('IV_SED_SUB_USAGE_FORBIDDEN'));
            return false;
        } else {
            throw new RuntimeException("Unknown subscription status: ${status}.");
        }
    }

    private static function service(): SubscriptionService {
        if (Subscriptions::$serviceInstance == null) {
            Subscriptions::$serviceInstance = new SubscriptionService();
        }

        return Subscriptions::$serviceInstance;
    }
}