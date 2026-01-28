<?php

namespace App\Enum;

enum TaskType : String {
    case TODO='TODO';
    case EMAIL='EMAIL';
    case CALL='CALL';
    case LINKED_IN_CONNECT='LINKED_IN_CONNECT';

    public function label(): string
    {
        return match($this)
        {
            self::TODO => __('TODO'),
            self::EMAIL => __('Email'),
            self::CALL => __('Call'),
            self::LINKED_IN_CONNECT => __('LinkedIn connect'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::TODO => 'images/tasks-icons/checkMark.svg',
            self::EMAIL => 'images/tasks-icons/directHit.svg',
            self::CALL => 'images/tasks-icons/phone.svg',
            self::LINKED_IN_CONNECT => 'images/emoji-icons/handshake.svg',
        };
    }
}
