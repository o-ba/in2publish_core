<?php

declare(strict_types=1);

namespace In2code\In2publishCore\Features\ContextMenuPublishEntry\Controller;

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

use In2code\In2publishCore\Component\PostPublishTaskExecution\Service\TaskExecutionService;
use In2code\In2publishCore\Component\RecordHandling\RecordFinder;
use In2code\In2publishCore\Component\RecordHandling\RecordPublisher;
use In2code\In2publishCore\Service\Permission\PermissionService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

use function json_encode;

class PublishPageAjaxController
{
    /** @var PermissionService */
    protected $permissionService;

    /** @var TaskExecutionService */
    protected $taskExecutionService;

    /** @var RecordFinder */
    protected $recordFinder;

    /** @var RecordPublisher */
    protected $recordPublisher;

    public function __construct(
        RecordFinder $recordFinder,
        RecordPublisher $recordPublisher,
        PermissionService $permissionService,
        TaskExecutionService $taskExecutionService
    ) {
        $this->recordFinder = $recordFinder;
        $this->recordPublisher = $recordPublisher;
        $this->permissionService = $permissionService;
        $this->taskExecutionService = $taskExecutionService;
    }

    public function publishPage(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();

        $page = $request->getQueryParams()['page'] ?? null;

        $content = [
            'success' => false,
            'label' => 'Unknown error',
            'lArgs' => [],
            'error' => true,
        ];

        if (!$this->permissionService->isUserAllowedToPublish()) {
            $content['label'] = 'context_menu_publish_entry.forbidden';
            $content['error'] = false;
        }

        if (null === $page) {
            $content['label'] = 'context_menu_publish_entry.missing_page';
        } else {
            try {
                $record = $this->recordFinder->findRecordByUidForPublishing((int)$page, 'pages');

                if (null !== $record && $record->isPublishable()) {
                    $this->recordPublisher->publishRecordRecursive($record);
                    $rceResponse = $this->taskExecutionService->runTasks();
                    if ($rceResponse->isSuccessful()) {
                        $content['success'] = true;
                        $content['error'] = false;
                        $content['label'] = 'context_menu_publish_entry.page_published';
                    } else {
                        $content['label'] = 'context_menu_publish_entry.publishing_error';
                    }
                    $content['lArgs'][] = BackendUtility::getRecordTitle('pages', $record->getLocalProperties());
                } else {
                    $content['error'] = false;
                    $content['label'] = 'context_menu_publish_entry.not_publishable';
                }
            } catch (Throwable $exception) {
                $content['label'] = (string)$exception;
            }
        }

        $lArgs = !empty($content['lArgs']) ? $content['lArgs'] : null;
        $content['message'] = LocalizationUtility::translate($content['label'], 'in2publish_core', $lArgs);
        $response->getBody()->write(json_encode($content));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
