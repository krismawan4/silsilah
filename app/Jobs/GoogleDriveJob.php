<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\File;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;

class GoogleDriveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $pathImg;

    private $fileNameToStore;

    private $folder;

    private $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pathImg, $fileNameToStore, $folder, $data)
    {
        $this->pathImg = $pathImg;
        $this->fileNameToStore = $fileNameToStore;
        $this->folder = $folder;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $newimg = new File($this->pathImg);
        $path = Storage::disk('google')->putFileAs($this->folder, $newimg, $this->fileNameToStore);
        $img_url = Storage::disk('google')->url($path);
        DB::table($this->data->getTable())
            ->where('id', $this->data->id)
            ->update(['photo_path' => NULL, 'image_drive_url' => $img_url, 'image_type' => 'GoogleDrive']);
        Storage::disk('local')->delete("public/$this->folder/".basename($this->pathImg));
    }
}
