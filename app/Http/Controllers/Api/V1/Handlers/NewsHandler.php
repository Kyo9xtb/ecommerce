<?php

namespace App\Http\Controllers\Api\V1\Handlers;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use App\Helper\CommandDataHelper;
use App\Http\Resources\NewsResource;
use App\Http\Responses\Api\NewsResponse;
use App\Repositories\News\NewsInterface;
use Illuminate\Support\Facades\Storage;

class NewsHandler
{
    public function __construct(
        public NewsInterface $newsInterface,
    ) {}

    public function handle($command)
    {
        return match ($command->request->getMethod()) {
            'GET'    => $this->handleGet($command),
            'POST'   => $this->handleCreate($command),
            'PUT'    => $this->handleUpdate($command),
            'DELETE' => $this->handleDelete($command->id),
            default  => throw new JsonApiException('Method not supported', ResponseStatusCode::PARAMS_INVALID),
        };
    }

    private function handleGet($command)
    {
        return $command->request->route('slug')
            ? $this->findNewsBySlug($command->request->route('slug'))
            : $this->fetchAll();
    }

    private function fetchAll(): NewsResponse
    {
        $result = $this->newsInterface->fetchAll();

        if (!$result) {
            throw new JsonApiException('No data found', ResponseStatusCode::PARAMS_INVALID);
        }

        return new NewsResponse("Success", NewsResource::collection($result)->toArray(request()));
    }

    private function findNewsBySlug($slug)
    {
        $result = $this->newsInterface->findNewsBySlug($slug);

        if (!$result) {
            throw new JsonApiException('No data found', ResponseStatusCode::PARAMS_INVALID);
        }

        return new NewsResponse("Success", (new NewsResource($result))->resolve());
    }

    private function handleCreate($command): NewsResponse
    {
        $this->validateUniqueNews($command);

        extract($this->processFiles($command));

        $fields = ['title', 'slug', 'meta_title', 'description', 'meta_description', 'content', 'author', 'status'];
        $inputData = array_filter(CommandDataHelper::extract($fields, $command), fn($v) => !is_null($v));

        $inputData['thumbnail'] = $thumbnail;

        $result = $this->newsInterface->createNews($inputData);

        if (!$result) {
            throw new JsonApiException('Create news failed', ResponseStatusCode::PARAMS_INVALID);
        }

        $this->moveFiles($result->id, $thumbnail);

        return new NewsResponse('Create news success', (new NewsResource($result))->resolve());
    }

    private function handleUpdate($command): NewsResponse
    {
        $idReq = (int) $command->id;
        $newsExist = $this->newsInterface->findNewsById($idReq);

        if (!$newsExist) {
            throw new JsonApiException('News not found', ResponseStatusCode::PARAMS_INVALID);
        }

        $this->validateUniqueNews($command, $idReq);

        $hasNewFile = $command->request->hasFile('thumbnail');
        $thumbnail = $newsExist->thumbnail;

        if ($hasNewFile) {
            if ($thumbnail) {
                $this->deleteOldFiles($newsExist, $idReq);
            }

            extract($this->processFiles($command));
        }


        $fields = ['title', 'slug', 'meta_title', 'description', 'meta_description', 'content', 'author', 'status'];

        $inputData = CommandDataHelper::extract($fields, $command);


        $inputData['thumbnail'] = $thumbnail;

        $result = $this->newsInterface->updateNews($idReq, $inputData);


        if (!$result) {
            throw new JsonApiException(
                'Update failed',
                ResponseStatusCode::PARAMS_INVALID
            );
        }
        if ($hasNewFile && $thumbnail)
            $this->moveFiles($idReq, $thumbnail);

        return new NewsResponse("Update news success", (new NewsResource($result))->resolve());
    }

    private function handleDelete($id): NewsResponse
    {
        $result = $this->newsInterface->deleteNews($id);

        if (!$result) {
            throw new JsonApiException('Delete failed', ResponseStatusCode::PARAMS_INVALID);
        }

        $directory = "news/$id";
        if (Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->deleteDirectory($directory);
        }

        return new NewsResponse("Delete success");
    }

    private function processFiles($command): array
    {
        $thumbnail = null;

        if ($command->request->hasFile('thumbnail')) {
            $filename = time() . '.' . $command->request->file('thumbnail')->getClientOriginalExtension();
            $command->request->file('thumbnail')->storeAs('public/news', $filename);
            $thumbnail = $filename;
        }

        return compact('thumbnail');
    }

    private function moveFiles(int $newsId, ?string $thumbnail): void
    {
        if ($thumbnail) {
            Storage::disk('public')->move("news/$thumbnail", "news/$newsId/$thumbnail");
        }
    }

    private function deleteOldFiles($news, int $id): void
    {
        if ($news->thumbnail) {
            $path = "news/{$id}/{$news->thumbnail}";
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    private function validateUniqueNews($command, ?int $excludeId = null): void
    {
        $errors = [];

        $titleExist = $this->newsInterface->findNewsByTitle($command->title);
        $slugExist  = $this->newsInterface->findNewsBySlug($command->slug);

        if ($titleExist && $titleExist->id !== $excludeId) {
            $errors[] = 'Title news already exists';
        }
        if ($slugExist && $slugExist->id !== $excludeId) {
            $errors[] = 'Slug already exists';
        }

        if ($errors) {
            throw new JsonApiException(implode(' & ', $errors), ResponseStatusCode::PARAMS_INVALID);
        }
    }
}
