<?php

namespace App\Factories\Common;

use App\Builders\Logs\ElasticAlertLogDTOBuilder;
use App\Builders\Logs\ElasticCriticalLogDTOBuilder;
use App\Builders\Logs\ElasticDebugDTOBuilder;
use App\Builders\Logs\ElasticDTOBuilderInterface;
use App\Builders\Logs\ElasticEmergencyDTOBuilder;
use App\Builders\Logs\ElasticErrorDTOBuilder;
use App\Builders\Logs\ElasticInfoLogDTOBuilder;
use App\Builders\Logs\ElasticLogDTOBuilder;
use App\Builders\Logs\ElasticNoticeLogDTOBuilder;
use App\Builders\Logs\ElasticWarningLogDTOBuilder;

class ElasticLogDTOFactory
{
    private ElasticAlertLogDTOBuilder $elasticAlertLogDTOBuilder;

    private ElasticCriticalLogDTOBuilder $elasticCriticalLogDTOBuilder;

    private ElasticDebugDTOBuilder $elasticDebugDTOBuilder;

    private ElasticEmergencyDTOBuilder $elasticEmergencyDTOBuilder;

    private ElasticErrorDTOBuilder $elasticErrorDTOBuilder;

    private ElasticInfoLogDTOBuilder $elasticInfoLogDTOBuilder;

    private ElasticLogDTOBuilder $elasticLogDTOBuilder;

    private ElasticNoticeLogDTOBuilder $elasticNoticeLogDTOBuilder;

    private ElasticWarningLogDTOBuilder $elasticWarningLogDTOBuilder;

    public function __construct(
        ElasticAlertLogDTOBuilder $elasticAlertLogDTOBuilder,
        ElasticCriticalLogDTOBuilder $elasticCriticalLogDTOBuilder,
        ElasticDebugDTOBuilder $elasticDebugDTOBuilder,
        ElasticEmergencyDTOBuilder $elasticEmergencyDTOBuilder,
        ElasticErrorDTOBuilder $elasticErrorDTOBuilder,
        ElasticInfoLogDTOBuilder $elasticInfoLogDTOBuilder,
        ElasticLogDTOBuilder $elasticLogDTOBuilder,
        ElasticNoticeLogDTOBuilder $elasticNoticeLogDTOBuilder,
        ElasticWarningLogDTOBuilder $elasticWarningLogDTOBuilder,
    ) {
        $this->elasticAlertLogDTOBuilder = $elasticAlertLogDTOBuilder;
        $this->elasticCriticalLogDTOBuilder = $elasticCriticalLogDTOBuilder;
        $this->elasticDebugDTOBuilder = $elasticDebugDTOBuilder;
        $this->elasticEmergencyDTOBuilder = $elasticEmergencyDTOBuilder;
        $this->elasticErrorDTOBuilder = $elasticErrorDTOBuilder;
        $this->elasticInfoLogDTOBuilder = $elasticInfoLogDTOBuilder;
        $this->elasticLogDTOBuilder = $elasticLogDTOBuilder;
        $this->elasticNoticeLogDTOBuilder = $elasticNoticeLogDTOBuilder;
        $this->elasticWarningLogDTOBuilder = $elasticWarningLogDTOBuilder;
    }

    public function getElasticDTOBuilder(string $logLevel): ElasticDTOBuilderInterface
    {
        return match ($logLevel) {
            'alert' => $this->elasticAlertLogDTOBuilder,
            'critical' => $this->elasticCriticalLogDTOBuilder,
            'debug' => $this->elasticDebugDTOBuilder,
            'emergency' => $this->elasticEmergencyDTOBuilder,
            'error' => $this->elasticErrorDTOBuilder,
            'info' => $this->elasticInfoLogDTOBuilder,
            'notice' => $this->elasticNoticeLogDTOBuilder,
            'warning' => $this->elasticWarningLogDTOBuilder,
            default => $this->elasticLogDTOBuilder,
        };
    }
}
