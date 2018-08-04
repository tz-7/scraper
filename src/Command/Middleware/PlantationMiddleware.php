<?php

namespace Tz7\WebScraper\Command\Middleware;


use League\Tactician\Middleware;
use Tz7\WebScraper\Command\Command;
use Tz7\WebScraper\Response\CommandSeed;
use Tz7\WebScraper\Response\Seed;
use Tz7\WebScraper\Response\SeedCanBePlantedInterface;


class PlantationMiddleware implements Middleware
{
    /**
     * @param Command   $command
     * @param callable $next
     *
     * @return void
     */
    public function execute($command, callable $next)
    {
        $next($command);

        $seed = $command->getSeed();

        if ($seed === null)
        {
            return;
        }

        $command->setSeed($this->plantSeed($seed, $next));
    }

    /**
     * @param Seed     $seed
     * @param callable $next
     *
     * @return mixed
     */
    private function plantSeed(Seed $seed, callable $next)
    {
        if ($seed instanceof SeedCanBePlantedInterface)
        {
            $seed->plant(
                function($data) use ($next)
                {
                    if ($data instanceof Seed)
                    {
                        return $this->plantSeed($data, $next);
                    }

                    return $data;
                }
            );

            return $seed;
        }

        if ($seed instanceof CommandSeed)
        {
            $command = $seed->getData();

            $this->execute($command, $next);

            return $command->getSeed();
        }

        return $seed;
    }
}
