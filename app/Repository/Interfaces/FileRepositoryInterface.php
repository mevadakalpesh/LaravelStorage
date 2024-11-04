<?php

namespace App\Repository\Interfaces;
use App\Models\File;

interface FileRepositoryInterface 
{
    public function createMedia(array $data = []) :File;
    
    public function getFiles(
      $selectRaw = '*',
      array $where = [],
      array $with = []
    );
    
    public function getFile(
      $selectRaw = '*',
      array $where = [],
      array $with = []
    );
    
    public function getFilesWithIn(
      $selectRaw = '*',
      string $whereInCol = null,
      array $whereIn = [],
      array $where = [],
      array $with = [],
      string $sortCol = 'id',
      string $sortType = 'desc'
      );
      
    public function getZipsData($zipPath);
}