<?php
/**
 * Created by PhpStorm.
 * @author Nicolas Desprez <nicolas.desprez@dnconcept.fr>
 */

namespace ArticleBundle\Service;

use Symfony\Component\HttpKernel\KernelInterface;


/**
 * Class LocalFile
 * @author Nicolas Desprez <nicolas.desprez@dnconcept.fr>
 * @package ArticleBundle\Service
 */
class LocalFile
{

    /** @var KernelInterface */
    private $kernel;

    /** @var string */
    private $store_folder;

    /**
     * LocalFile constructor.
     * @param KernelInterface $kernel
     * @param string $store_folder
     */
    public function __construct(KernelInterface $kernel, $store_folder)
    {
        $this->kernel = $kernel;
        $this->store_folder = $store_folder;
    }

    /**
     * @param $data
     * @throws \Exception
     */
    public function store($data)
    {
        $directoryToStore = $this->kernel->getRootDir() . "/$this->store_folder";
        if (!is_dir($directoryToStore)) {
            throw new \Exception("The directory $directoryToStore does not exists !");
        }
        if (!is_array($data)) {
            throw new \Exception("The data must be an array !");
        }
        $timestamp = time();
        $filename = "$directoryToStore/file$timestamp.dat";
        $dataEncoded = json_encode($data);
        file_put_contents($filename, $dataEncoded);
    }

}