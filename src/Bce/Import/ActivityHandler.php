<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Activity;
use AcMarche\Bottin\Bce\Repository\ActivityRepository;
use AcMarche\Bottin\Bce\Utils\SymfonyStyleFactory;

class ActivityHandler
{
    use SymfonyStyleFactory;

    private ActivityRepository $activityRepository;

    public function __construct(ActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public static function getDefaultIndexName(): string
    {
        return 'activity';
    }

    /**
     * @param iterable|Activity[] $activitys
     */
    public function handle(iterable $activitys):?object
    {
        foreach ($activitys as $data) {
            if (!$this->activityRepository->checkExist($data->naceCode, $data->entityNumber)) {
                $activity = $data;
                $this->activityRepository->persist($activity);
            }
            $this->writeLn($data->entityNumber);
        }
        $this->activityRepository->flush();
    }
    public function flush(): void
    {
        // TODO: Implement flush() method.
    }
}
