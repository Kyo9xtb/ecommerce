<?php

namespace App\Repositories\News;


interface NewsInterface
{
    public function fetchAll();
    public function findNewsById($id);
    public function findNewsBySlug($slug);
    public function findNewsByTitle($title);
    public function createNews($data);
    public function updateNews($id, $data);
    public function deleteNews($id);
}
