<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\Rector\MethodCall\ChangeStringCollectionOptionToConstantRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(ChangeStringCollectionOptionToConstantRector::class);
};
