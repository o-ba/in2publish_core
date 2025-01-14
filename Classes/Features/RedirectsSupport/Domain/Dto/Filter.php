<?php

declare(strict_types=1);

namespace In2code\In2publishCore\Features\RedirectsSupport\Domain\Dto;

/*
 * Copyright notice
 *
 * (c) 2021 in2code.de and the following authors:
 * Oliver Eglseder <oliver.eglseder@in2code.de>
 *
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

class Filter
{
    /** @var null|string */
    protected $domain;

    /** @var null|string */
    protected $source;

    /** @var null|string */
    protected $target;

    /** @var null|int */
    protected $code;

    /** @var null|string */
    protected $association;

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): void
    {
        $this->domain = $domain ?: null;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): void
    {
        $this->source = $source ?: null;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): void
    {
        $this->target = $target ?: null;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(?int $code): void
    {
        $this->code = $code;
    }

    public function getAssociation(): ?string
    {
        return $this->association;
    }

    public function setAssociation(?string $association): void
    {
        $this->association = $association ?: null;
    }

    public function modifyQuery(QueryInterface $query): void
    {
        $and = [];

        if (null !== $this->domain) {
            $and[] = $query->equals('source_host', $this->domain);
        }
        if (null !== $this->source) {
            $and[] = $query->like('source_path', '%' . $this->source . '%');
        }
        if (null !== $this->target) {
            $and[] = $query->like('target', '%' . $this->target . '%');
        }
        if (null !== $this->code) {
            $and[] = $query->equals('target_statuscode', $this->code);
        }
        if (null !== $this->association) {
            if ('present' === $this->association) {
                $and[] = $query->logicalOr(
                    [
                        $query->logicalNot($query->equals('tx_in2publishcore_foreign_site_id', null)),
                        $query->logicalNot($query->equals('tx_in2publishcore_page_uid', null)),
                        $query->equals('source_host', '*'),
                    ]
                );
            }
            if ('missing' === $this->association) {
                $and[] = $query->logicalAnd(
                    [
                        $query->equals('tx_in2publishcore_foreign_site_id', null),
                        $query->equals('tx_in2publishcore_page_uid', null),
                        $query->logicalNot(
                            $query->equals('source_host', '*')
                        ),
                    ]
                );
            }
        }

        $count = count($and);
        if ($count > 1) {
            $query->matching($query->logicalAnd($and));
        } elseif ($count === 1) {
            $query->matching($and[0]);
        }
    }
}
