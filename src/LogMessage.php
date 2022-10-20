<?php namespace MyENA;

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class LogMessage
{
    /** @var string */
    private string $level;
    /** @var string */
    private string $message;
    /** @var array */
    private array $context = [];
    /** @var \DateTime */
    private \DateTime $date;

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function __construct(string $level, string $message, array $context)
    {
        $this->level = strtolower($level);
        $this->message = $message;
        $this->context = $context;
        $this->date = new \DateTime();
    }

    /**
     * @return string
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * @param string $level
     * @return LogMessage
     */
    public function setLevel(string $level): LogMessage
    {
        $out = clone $this;
        $out->level = $level;
        return $out;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return LogMessage
     */
    public function setMessage(string $message): LogMessage
    {
        $out = clone $this;
        $out->message = $message;
        return $this;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array $context
     * @return LogMessage
     */
    public function setContext(array $context): LogMessage
    {
        $out = clone $this;
        $out->context = $context;
        return $this;
    }

    public function with(string $key, $value): LogMessage {
        $out = clone $this;
        $out->context[$key] = $value;
        return $out;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return LogMessage
     */
    public function setDate(\DateTime $date): LogMessage
    {
        $out = clone $this;
        $out->date = $date;
        return $this;
    }
}