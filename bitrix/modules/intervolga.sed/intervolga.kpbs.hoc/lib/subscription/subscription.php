<?php

namespace Intervolga\Sed\Subscription;

use DateTimeImmutable;
use function array_key_exists;
use function is_string;

/**
 * @author Nikita Kalinin <kalinin@intervolga.ru>
 */


class Subscription {
    private $id;
    private $holder;
    private $contacts;
    private $issued;
    private $validThrough;
    private $graceDays;

    public function __construct(string $id, string $holder, string $contacts, DateTimeImmutable $issued, DateTimeImmutable $validThrough, int $graceDays) {
        $this->id = $id;
        $this->holder = $holder;
        $this->contacts = $contacts;
        $this->issued = $issued;
        $this->validThrough = $validThrough;
        $this->graceDays = $graceDays;
    }

    public static function fromArray(string $id, array $raw): Subscription {
        if (!array_key_exists('holder', $raw) || !is_string($raw['holder'])) {
            throw new InvalidKeyException('Malformed subscription details: holder is missing or of wrong type.');
        }

        if (!array_key_exists('contacts', $raw) || !is_string($raw['contacts'])) {
            throw new InvalidKeyException('Malformed subscription details: contacts are missing or of wrong type.');
        }

        if (!array_key_exists('issued', $raw) || !is_string($raw['issued'])) {
            throw new InvalidKeyException('Malformed subscription details: issued date are missing or of wrong type.');
        }

        if (!array_key_exists('validThrough', $raw) || !is_string($raw['validThrough'])) {
            throw new InvalidKeyException('Malformed subscription details: validThrough are missing or of wrong type.');
        }

        if (!array_key_exists('graceDays', $raw) || (int) $raw['graceDays'] <= 0) {
            throw new InvalidKeyException('Malformed subscription details: graceDays is missing or of wrong type.');
        }

        $issued = DateTimeImmutable::createFromFormat('Y-m-d', $raw['issued']);
        if (!($issued instanceof DateTimeImmutable)) {
            throw new InvalidKeyException('Malformed subscription details: issued date do not follow the required format.');
        }

        $validThrough = DateTimeImmutable::createFromFormat('Y-m-d', $raw['validThrough']);
        if (!($validThrough instanceof DateTimeImmutable)) {
            throw new InvalidKeyException('Malformed subscription details: issued date do not follow the required format.');
        }

        return new Subscription(
                $id,
                $raw['holder'],
                $raw['contacts'],
                $issued,
                $validThrough,
                (int) $raw['graceDays']
        );
    }

    public function toArray(): array {
        return array(
                'id' => $this->id,
                'holder' => $this->holder,
                'contacts' => $this->contacts,
                'issued' => $this->issued->format('Y-m-d'),
                'validThrough' => $this->validThrough->format('Y-m-d'),
                'graceDays' => $this->graceDays,
        );
    }

    public function getId(): string {
        return $this->id;
    }

    public function getHolder(): string {
        return $this->holder;
    }

    public function getContacts(): string {
        return $this->contacts;
    }

    public function getIssued(): DateTimeImmutable {
        return $this->issued;
    }

    public function getValidThrough(): DateTimeImmutable {
        return $this->validThrough;
    }

    public function getGraceDays(): int {
        return $this->graceDays;
    }
}