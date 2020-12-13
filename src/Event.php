<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks;

use Craftzing\Laravel\LokaliseWebhooks\Exceptions\UnexpectedWebhookPayload;
use Spatie\WebhookClient\Models\WebhookCall;

final class Event
{
    private const NAMESPACE = 'lokalise-webhooks::';
    public const PROJECT_IMPORTED = self::NAMESPACE . 'project.imported';
    public const PROJECT_EXPORTED = self::NAMESPACE . 'project.exported';
    public const PROJECT_SNAPSHOT = self::NAMESPACE . 'project.snapshot';
    public const PROJECT_LANGUAGES_ADDED = self::NAMESPACE . 'project.languages.added';
    public const PROJECT_LANGUAGE_REMOVED = self::NAMESPACE . 'project.language.removed';
    public const PROJECT_LANGUAGE_SETTINGS_CHANGED = self::NAMESPACE . 'project.language.settings_changed';
    public const PROJECT_KEY_ADDED = self::NAMESPACE . 'project.key.added';
    public const PROJECT_KEY_MODIFIED = self::NAMESPACE . 'project.key.modified';
    public const PROJECT_KEYS_DELETED = self::NAMESPACE . 'project.keys.deleted';
    public const PROJECT_KEY_COMMENT_ADDED = self::NAMESPACE . 'project.key.comment.added';
    public const PROJECT_TRANSLATION_UPDATED = self::NAMESPACE . 'project.translation.updated';
    public const PROJECT_TRANSLATION_PROOFREAD = self::NAMESPACE . 'project.translation.proofread';
    public const PROJECT_CONTRIBUTOR_ADDED = self::NAMESPACE . 'project.contributor.added';
    public const PROJECT_CONTRIBUTOR_DELETED = self::NAMESPACE . 'project.contributor.deleted';
    public const PROJECT_TASK_CREATED = self::NAMESPACE . 'project.task.created';
    public const PROJECT_TASK_CLOSED = self::NAMESPACE . 'project.task.closed';
    public const PROJECT_TASK_DELETED = self::NAMESPACE . 'project.task.deleted';
    public const PROJECT_TASK_LANGUAGE_CLOSED = self::NAMESPACE . 'project.task.language.closed';
    public const PING = self::NAMESPACE . 'ping';

    private string $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function fromWebhookCall(WebhookCall $webhookCall): self
    {
        $payload = $webhookCall->getAttribute('payload') ?: [];

        if ($event = ($payload['event'] ?? null)) {
            return new self(self::NAMESPACE . $event);
        }

        // Lokalise uses a ping event to ensure a webhook actually works. This event has a different payload
        // structure compared to the actual webhook events. We should ba able to handle these as well as
        // Lokalise won't let you configure webhook handlers that don't return a successful response.
        if ($payload === ['ping']) {
            return new self(self::PING);
        }

        throw UnexpectedWebhookPayload::missingEvent();
    }

    public function __toString(): string
    {
        return $this->name();
    }

    public function name(): string
    {
        return $this->name;
    }
}
