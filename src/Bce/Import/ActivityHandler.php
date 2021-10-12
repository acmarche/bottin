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
     * @return Activity[]
     *
     * @throws \Exception
     */
    public function readFile(string $fileName): iterable
    {
        return $this->csvReader->readFileAndConvertToClass($fileName);
    }

    /**
     * @param Activity $data
     */
    public function handle($data)
    {
        if ($activity = $this->activityRepository->checkExist($data->naceCode, $data->entityNumber)) {
            $activity->activityGroup = $data->activityGroup;
            $activity->classification = $data->classification;
            $activity->naceVersion = $data->naceVersion;
        } else {
            $activity = $data;
            $this->activityRepository->persist($activity);
        }
    }

    /**
     * @param Activity $data
     */
    public function writeLn($data): string
    {
        return $data->entityNumber;
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
