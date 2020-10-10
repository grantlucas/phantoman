<?php declare(strict_types = 1);

namespace Codeception\Extension\CommandHandler\Mapper;

interface CommandMapperInterface
{
    /**
     * @param array $params
     *
     * @return string
     */
    public function mapParamsToCommandLineArgs(array $params): string;
}
