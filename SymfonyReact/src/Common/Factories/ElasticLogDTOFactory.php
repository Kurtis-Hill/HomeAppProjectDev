<?php

namespace App\Common\Factories;

use App\Common\Builders\Logs\ElasticAlertLogDTOBuilder;
use App\Common\Builders\Logs\ElasticCriticalLogDTOBuilder;
use App\Common\Builders\Logs\ElasticDebugDTOBuilder;
use App\Common\Builders\Logs\ElasticDTOBuilderInterface;
use App\Common\Builders\Logs\ElasticEmergencyDTOBuilder;
use App\Common\Builders\Logs\ElasticErrorDTOBuilder;
use App\Common\Builders\Logs\ElasticInfoLogDTOBuilder;
use App\Common\Builders\Logs\ElasticLogDTOBuilder;
use App\Common\Builders\Logs\ElasticNoticeLogDTOBuilder;
use App\Common\Builders\Logs\ElasticWarningLogDTOBuilder;
use App\Common\DTO\Logs\ElasticAlertLogDTO;
use App\Common\DTO\Logs\ElasticCriticalLogDTO;
use App\Common\DTO\Logs\ElasticDebugLogDTO;
use App\Common\DTO\Logs\ElasticEmergencyLogDTO;
use App\Common\DTO\Logs\ElasticErrorLogDTO;
use App\Common\DTO\Logs\ElasticInfoLogDTO;
use App\Common\DTO\Logs\ElasticLogDTO;
use App\Common\DTO\Logs\ElasticNoticeLogDTO;
use App\Common\DTO\Logs\ElasticWarningLogDTO;

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
