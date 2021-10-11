<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Activity;
use AcMarche\Bottin\Bce\Repository\ActivityRepository;

class ActivityHandler implements ImportHandlerInterface
{
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
     * @param array|Activity[] $activitys
     */
    public function handle(array $activitys)
    {
        foreach ($activitys as $data) {
            if (!$this->activityRepository->checkExist($data->activity, $data->language, $data->category)) {
                $activity = $data;
                $this->activityRepository->persist($activity);
            }
        }
        $this->activityRepository->flush();
    }
}
