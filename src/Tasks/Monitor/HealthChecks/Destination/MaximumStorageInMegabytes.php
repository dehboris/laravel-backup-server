<?php

namespace Spatie\BackupServer\Tasks\Monitor\HealthChecks\Destination;

use Spatie\BackupServer\Models\Destination;
use Spatie\BackupServer\Tasks\Monitor\HealthCheckResult;

class MaximumStorageInMegabytes extends DestinationHealthCheck
{
    private int $configuredMaximumStorageInMegabytes;

    public function __construct(int $configuredMaximumStorageInMegabytes)
    {
        $this->configuredMaximumStorageInMegabytes = $configuredMaximumStorageInMegabytes;
    }

    public function getResults(Destination $destination): HealthCheckResult
    {
        $actualSizeInMegabytes = $destination->completedBackups()->sum('real_size_in_kb') * 1024;

        $maximumSizeInMegabytes = $this->maximumSizeInMegabytes($destination);

        if ($actualSizeInMegabytes > $maximumSizeInMegabytes) {
            HealthCheckResult::failed("The actual storage used ({$actualSizeInMegabytes} MB) is greater than the allowed storage used ({$maximumSizeInMegabytes}).");
        }

        return HealthCheckResult::ok();
    }

    protected function maximumSizeInMegabytes(Destination $destination): int
    {
        return $destination->healthy_maximum_storage_in_megabytes ?? $this->configuredMaximumStorageInMegabytes;
    }
}
