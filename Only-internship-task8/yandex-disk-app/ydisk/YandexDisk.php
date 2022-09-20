<?php
require_once  __DIR__ . '/../composer/vendor/autoload.php';


class YandexDisk
{
    protected $token = "y0_AgAAAABUFsGbAAhsxgAAAADPSRkA6hADqjxJRIuqk7t7hfBBrlXIdOY";

    protected $disk;

    public function __construct()
    {
        $disk = new Arhitector\Yandex\Disk;
        $this->disk = $disk->setAccessToken($this->token);
    }

    public function createFile($file)
    {
        $resource = $this->disk->getResource($file['name']);
        if (!$resource->has()) {
            $resource->upload($file['tmp_name']);
        }
    }

    public function readFile($path_file)
    {
        $resource = $this->disk->getResource($path_file, 0);
        if ($resource->has()){
            return $resource->toArray();
        } else {
            return 'Resource not found';
        }
    }

    public function renameFile($path, $name)
    {
        $resource = $this->disk->getResource($path, 0);
        if (!$resource->has()) {
            return false;
        }
        $newPath = dirname() . $name;
        try {
            $resource->move($newPath, true);
            return $this->getFileArrFromResource($resource);
        } catch (Exception $exception) {
            return false;
        }
    }

    public function deleteFile($path_file)
    {
        $resource = $this->disk->getResource($path_file, 0);
        if ($resource->has()){
            return $resource->delete();
        } else {
            return 'Resource not found';
        }
    }

    public function getAll()
    {
        $collection = $this->disk->getResources('999', '0');
        $collection->toObject();
        $collection->getIterator();
        return $collection;
    }

    private function getFileArrFromResource($resource)
    {
        $file = $resource->toArray();
        return [
          "name" => $file["name"],
          "path" => $file["path"],
          "created" => date("d.m.Y H:i:s", strtotime($file["created"]))
        ];
    }

}