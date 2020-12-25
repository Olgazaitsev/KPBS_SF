<?php
/**
 * @author Nikita Kalinin <kalinin@intervolga.ru>
 */

namespace Intervolga\Sed\Subscription;


use RuntimeException;

class SubscriptionExpiredException extends RuntimeException implements SubscriptionException {

}