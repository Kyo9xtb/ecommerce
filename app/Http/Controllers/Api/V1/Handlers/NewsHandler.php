<?php

namespace App\Http\Controllers\Api\V1\Handlers;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use App\Helper\CommandDataHelper;
use App\Http\Resources\NewsResource;
use App\Http\Responses\Api\NewsResponse;
use App\Repositories\News\NewsInterface;

class NewsHandler
{
    public function __construct(
        public NewsInterface $newsInterface,
    ) {}

    public function handle($command)
    {
        $method = $command->request->getMethod();

        return match ($method) {
            'GET' => $this->handleGet($command),
            'POST'   => $this->handleCreate($command),
            'PUT'    => $this->handleUpdate($command),
            'DELETE' => $this->handleDelete($command->id),
            default  => throw new JsonApiException('Method not supported', ResponseStatusCode::PARAMS_INVALID),
        };
    }

    private function handleGet($command)
    {
        $slug = $command->request->route('slug');

        if ($slug) {
            return $this->findNewsBySlug($slug);
        }

        return $this->fetchAll();
    }

    private function fetchAll(): NewsResponse
    {
        $result = $this->newsInterface->fetchAll();

        if (!$result) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return new NewsResponse(
            message: "Success",
            data: NewsResource::collection($result)->toArray(request()),
        );
    }

    private function findNewsBySlug($slug)
    {
        $result = $this->newsInterface->findNewsBySlug($slug);

        if (!$result) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return new NewsResponse(
            message: "Success",
            data: (new NewsResource($result))->resolve(),
        );
    }

    private function handleCreate($command): NewsResponse
    {
       $file =  $command->request->file('thumbnail');
       dd($file);
        $titleExist = $this->newsInterface->findNewsByTitle($command->title);
        $slugExist = $this->newsInterface->findNewsBySlug($command->slug);

        if ($titleExist || $slugExist) {
            throw new JsonApiException(
                'Title or slug already exists',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        $fields = [
            'full_name',
            'email',
            'phone',
            'contact_content',
            'contact_result',
            'status',
            'title'
        ];

        $inputData = array_filter(
            CommandDataHelper::extract($fields, $command),
            fn($value) => !is_null($value)
        );

        $inputData['thumbnail'] = 'thumbnail.png';

        $result =  $this->newsInterface->createNews($inputData);

        if (!$result) {
            throw new JsonApiException(
                'No data found',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return new NewsResponse(
            message: "Success",
            data: (new NewsResource($result))->resolve(),
        );
    }

    private function handleUpdate($command)
    {
        $newsExist = $this->newsInterface->findNewsById($command->id);
        if (!$newsExist) {
            throw new JsonApiException(
                'News already exists',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        $titleExist = $this->newsInterface->findNewsByTitle($command->title);
        $slugExist = $this->newsInterface->findNewsBySlug($command->slug);

        if (($titleExist && $titleExist->id !== $command->id) || ($slugExist &&  $slugExist->id !== $command->id)) {
            throw new JsonApiException(
                'Title or slug already exists',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        $fields = [
            'full_name',
            'email',
            'phone',
            'contact_content',
            'contact_result',
            'status',
            'title'
        ];

        $inputData = CommandDataHelper::extract($fields, $command);

        $inputData['thumbnail'] = 'thumbnail.png';

        $result = $this->newsInterface->updateNews($command->id, $inputData);

        if (!$result) {
            throw new JsonApiException(
                'Update failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return new NewsResponse(
            message: "Update success",
            data: (new NewsResource($result))->resolve(),
        );
    }

    private function handleDelete($id)
    {

        $result = $this->newsInterface->deleteNews($id);

        if (!$result) {
            throw new JsonApiException(
                'Delete failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }

        return new NewsResponse(
            message: "Delete success",
            data: (new NewsResource($result))->resolve(),
        );
    }
}
