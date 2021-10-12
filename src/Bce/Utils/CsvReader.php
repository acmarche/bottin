<?php

namespace AcMarche\Bottin\Bce\Utils;

use AcMarche\Bottin\Bce\Entity\Enterprise;
use SplFileObject;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CsvReader
{
    private SerializerInterface $serializer;
    private ParameterBagInterface $parameterBag;

    public function __construct(
        ParameterBagInterface $parameterBag,
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @throws \Exception
     */
    public function readFile(string $fileName): iterable
    {
        $varDirectory = $this->parameterBag->get('kernel.project_dir').'/var/cbe';
        $file = $varDirectory.'/'.$fileName.'.csv';

        if (!is_readable($file)) {
            throw new \Exception('File not found '.$file);
        }

        if ('enterprise' === $fileName) {
            $objects = [];
            foreach ($this->readCSVGenerator($file) as $data) {
                if(is_bool($data)){
                    dump($data);
                }
                $enterprise = new Enterprise();
                $enterprise->enterpriseNumber = $data[0];
                $enterprise->status = $data[1];
                $enterprise->juridicalSituation = (int)$data[2];
                $enterprise->typeOfEnterprise = (int)$data[3];
                $enterprise->juridicalForm = (int)$data[4];
                $enterprise->startDate = $data[5];
                $objects[] = $enterprise;
                dump($data[0]);
                dump(memory_get_usage());
            }

            return $objects;
        }

        $objects = [];
        $class = 'AcMarche\Bottin\Bce\Entity\\'.ucfirst($fileName).'[]';
        try {
            $objects = $this->serializer->deserialize(file_get_contents($file), $class, 'csv', [
            ]);
        } catch (\Exception$exception) {
            dump($exception->getMessage());
        }

        return $objects;
    }

    public function readCSVGenerator(string $file)
    {
        $handle = fopen($file, 'r');

        while (!feof($handle)) {
            yield fgetcsv($handle);
        }

        fclose($handle);
    }

    /**
     * Bug offset.
     */
    public function getEnterprises(string $file): iterable
    {
        $fileObj = new SplFileObject($file);

        $fileObj->setFlags(
            SplFileObject::READ_CSV
            | SplFileObject::SKIP_EMPTY
            | SplFileObject::READ_AHEAD
            | SplFileObject::DROP_NEW_LINE
        );
        $fileObj->setCsvControl(',');

        foreach ($fileObj as $data) {
            dump(memory_get_usage());
            $enterprise = new Enterprise();
            $enterprise->enterpriseNumber = $data[0];
            $enterprise->status = $data[1];
            $enterprise->juridicalSituation = (int)$data[2];
            $enterprise->typeOfEnterprise = (int)$data[3];
            $enterprise->juridicalForm = (int)$data[4];
            $enterprise->startDate = $data[5];
            yield $enterprise;
        }

        return $fileObj;

        $rows = [];
        if (($handle = fopen($file, 'r')) !== false) {
            while (($data = fgetcsv($handle, null, ',')) !== false) {
                $enterprise = new Enterprise();
                $enterprise->enterpriseNumber = $data[0];
                $enterprise->status = $data[1];
                $enterprise->juridicalSituation = (int)$data[2];
                $enterprise->typeOfEnterprise = (int)$data[3];
                $enterprise->juridicalForm = (int)$data[4];
                $enterprise->startDate = $data[5];

                dump(memory_get_usage());
                yield $enterprise;
            }
            fclose($handle);
        }

        return $rows;
    }
}
