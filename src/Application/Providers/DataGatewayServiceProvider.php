<?php

declare(strict_types=1);

namespace PhpCfdi\RfcLinc\Application\Providers;

use PDO;
use PhpCfdi\RfcLinc\Application\Config;
use PhpCfdi\RfcLinc\DataGateway\Pdo\PdoFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DataGatewayServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['gateways'] = function (Container $container) {
            $config = $container['config'];
            return new PdoFactory($this->createPdo($config));
        };
    }

    public function createPdo(Config $config): PDO
    {
        if ('' === $config->dbDsn()) {
            throw new \RuntimeException('No database DSN is configured');
        }

        try {
            return new PDO($config->dbDsn(), $config->dbUsername(), $config->dbPassword());
        } catch (\Throwable $exception) {
            throw new \RuntimeException(sprintf(
                "Unable to create PDO using\nDSN: %s,\nUsername: '%s',\nPassword: %s.",
                $config->dbDsn(),
                $config->dbUsername(),
                ('' === $config->dbPassword()) ? 'empty' : 'not empty'
            ), 0, $exception);
        }
    }
}
