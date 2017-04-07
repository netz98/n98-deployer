<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace N98\Deployer\Server;

use Deployer\Server\Configuration;
use Deployer\Server\ServerInterface;
use N98\Deployer\TestFramework\DeployerMock;

class ServerMock implements ServerInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * Local constructor.
     *
     * @param $config
     */
    public function __construct(Configuration $config = null)
    {
        $this->configuration = $config;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        $callback = DeployerMock::getCallback('connect');
        if (is_callable($callback)) {
            $callback(...func_get_args());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run($command)
    {
        $callback = DeployerMock::getCallback('run');
        if (is_callable($callback)) {
            $callback(...func_get_args());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function upload($local, $remote)
    {
        $callback = DeployerMock::getCallback('upload');
        if (is_callable($callback)) {
            $callback(...func_get_args());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function download($local, $remote)
    {
        $callback = DeployerMock::getCallback('download');
        if (is_callable($callback)) {
            $callback(...func_get_args());
        }
    }
}
