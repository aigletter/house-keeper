<?php

namespace Aigletter\tests\unit;

use Aigletter\HouseKeeper\HouseKeeper;
use PHPUnit\Framework\TestCase;

class HouseKeeperTest extends TestCase
{
    public function setUp()
    {
        $config = $this->getTestConfig();
        foreach ($config['directories'] as $directory) {
            if (!file_exists($directory['directoryPath'])) {
                mkdir($directory['directoryPath']);
            }

            for ($i = 0; $i < 10; $i++) {
                file_put_contents($directory['directoryPath'] . '/' . $i . '.txt', '');
            }
        }
    }

    public function tearDown()
    {
        $config = $this->getTestConfig();
        foreach ($config['directories'] as $directory) {
            exec("rm -rf {$directory['directoryPath']}");
        }
    }

    public function test()
    {
        sleep(1);

        $config = $this->getTestConfig();
        $instance = new HouseKeeper($config);

        sleep(1);

        file_put_contents($config['directories'][0]['directoryPath'] . '/10.txt', '');

        $instance->run();

        $this->assertFileExists($config['directories'][0]['directoryPath'] . '/5.txt');
        $this->assertFileExists($config['directories'][0]['directoryPath'] . '/6.txt');
        $this->assertFileExists($config['directories'][0]['directoryPath'] . '/10.txt');
        $this->assertCount(5, scandir($config['directories'][0]['directoryPath']));
        $this->assertCount(2, scandir($config['directories'][1]['directoryPath']));
    }

    protected function getTestConfig()
    {
        return json_decode(json_encode([
            'directories' => [
                [
                    'directoryPath' => __DIR__ . '/../../test-directory-1',
                    'expirationTime' => date('Y-m-d H:i:s'),
                    'ignoringFiles' => [
                        '5.txt',
                        '6.txt'
                    ]
                ],
                [
                    'directoryPath' => __DIR__ . '/../../test-directory-2',
                    'expirationTime' => time(),
                    'ignoringFiles' => [

                    ]
                ]
            ]
        ]), true);
    }
}