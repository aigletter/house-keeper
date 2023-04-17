<?php

namespace Aigletter\HouseKeeper;

class HouseKeeper
{
    protected $config;

    public function __construct(array $config) {
        $this->config = $config;
    }

    public function run()
    {
        foreach ($this->config['directories'] as $directory) {
            if (file_exists($directory['directoryPath'])) {
                $this->clean($directory);
            }
        }
    }

    public function clean($config)
    {
        $directoryIterator = new \DirectoryIterator($config['directoryPath']);
        $expirationTime = $this->makeExpirationTime($config['expirationTime']);
        $directoryIterator = new DirectoryFilterIterator(
            $directoryIterator,
            $expirationTime,
            $config['ignoringFiles']
        );

        foreach ($directoryIterator as $directory) {
            unlink($directory->getRealPath());
        }
    }

    protected function makeExpirationTime($config)
    {
        if ($config instanceof \DateTimeInterface) {
            return $config;
        }

        if (is_int($config)) {
            $expirationTime = new \DateTime();
            $expirationTime->setTimestamp($config);
            return $expirationTime;
        }

        return new \DateTime($config);
    }
}