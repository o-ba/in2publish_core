<?php

declare(strict_types=1);

namespace In2code\In2publishCore\Domain\Model;

interface Record
{
    public const LOCAL = 'local';
    public const FOREIGN = 'foreign';
    public const S_ADDED = 'added';
    public const S_CHANGED = 'changed';
    public const S_MOVED = 'moved';
    public const S_SOFT_DELETED = 'soft_deleted';
    public const S_DELETED = 'deleted';
    public const S_UNCHANGED = 'unchanged';

    public function getClassification(): string;

    /**
     * @return array-key
     */
    public function getId();

    public function getLocalProps(): array;

    public function getForeignProps(): array;

    /**
     * @return scalar
     */
    public function getProp(string $prop);

    public function getPropsBySide(string $side): array;

    /**
     * @return array The list of names of props that are different.
     */
    public function getChangedProps(): array;

    public function addChild(Record $childRecord): void;

    public function removeChild(Record $record): void;

    /**
     * @return array<string, array<array-key, Record>
     */
    public function getChildren(): array;

    public function addParent(Record $parentRecord): void;

    public function removeParent(Record $record): void;

    /**
     * @return array<Record>
     */
    public function getParents(): array;

    public function setTranslationParent(Record $translationParent): void;

    public function getTranslationParent(): ?Record;

    /**
     * @return array<int, array<array-key, Record>>
     */
    public function getTranslations(): array;

    public function isChanged(): bool;

    /**
     * @return string One of the S_* constants
     */
    public function getState(): string;

    /**
     * @return string One of the S_* constants
     */
    public function getStateRecursive(): string;

    /**
     * Prefers the local value. Mostly used to build the record tree.
     *
     * @return int
     */
    public function getLanguage(): int;

    /**
     * Prefers the local value. Mostly used to build the record tree.
     *
     * @return int
     */
    public function getTransOrigPointer(): int;
}
