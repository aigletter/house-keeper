<?php

namespace Aigletter\HouseKeeper;


class DirectoryFilterIterator extends \FilterIterator
{
    protected $expirationTime;

    protected $ignoringFiles;

    public function __construct(
        \Iterator $iterator,
        \DateTimeInterface $expirationTime,
        array $ignoringFiles = []
    ) {
        parent::__construct($iterator);
        $this->expirationTime = $expirationTime;
        $this->ignoringFiles = $ignoringFiles;
    }

    public function accept()
    {
        if ($this->current()->isDot()) {
            return false;
        }

        if (in_array($this->current()->getFileName(), $this->ignoringFiles)) {
            return false;
        }

        $createdAt = new \DateTime();
        $createdAt->setTimestamp($this->current()->getCTime());

        return $createdAt < $this->expirationTime;
    }
}