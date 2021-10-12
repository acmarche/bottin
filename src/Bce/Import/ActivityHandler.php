<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Activity;
use AcMarche\Bottin\Bce\Repository\ActivityRepository;
use AcMarche\Bottin\Bce\Utils\CsvReader;

class ActivityHandler implements ImportHandlerInterface
{
    private ActivityRepository $activityRepository;
    private CsvReader $csvReader;

    public function __construct(ActivityRepository $activityRepository, CsvReader $csvReader)
    {
        $this->activityRepository = $activityRepository;
        $this->csvReader = $csvReader;
    }

    /**
     * @throws \Exception
     */
    public function readFile(string $fileName): iterable
    {
        return $this->csvReader->readCSVGenerator($fileName);
    }

    /**
     * @param array $data
     */
    public function handle($data)
    {
        if (!$this->activityRepository->checkExist($data[3], $data[0])) {
            $activity = new Activity();
            $activity->entityNumber = $data[0];
            $activity->naceCode = $data[3];
            $this->activityRepository->persist($activity);
        }
        $this->updateActivity($activity, $data);
    }

    /**
     * "EntityNumber","ActivityGroup","NaceVersion","NaceCode","Classification".
     */
    private function updateActivity(Activity $activity, array $data)
    {
        $activity->activityGroup = $data[1];
        $activity->classification = $data[4];
        $activity->naceVersion = $data[2];
    }

    /**
     * @param Activity $data
     */
    public function writeLn($data): string
    {
        return $data[0];
    }

    public function flush(): void
    {
        $this->activityRepository->flush();
    }

    public static function getDefaultIndexName(): string
    {
        return 'activity';
    }
}
