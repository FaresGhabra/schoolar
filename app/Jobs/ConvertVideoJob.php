<?php

namespace App\Jobs;

use App\Models\SchoolCourses\CourseVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Storage;

use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;

class ConvertVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $path, $filename, $video_id;

    /**
     * Create a new job instance.
     *
     * @param  string  $videoPath
     * @return void
     */
    public function __construct($path, $filename, $video_id)
    {
        $this->path = $path;
        $this->filename = $filename;
        $this->video_id = $video_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $video = CourseVideo::find($this->video_id);
        if ($video === null)
            return;

        $file = Storage::path($this->path . $this->filename);
        $filename = $this->filename;
        // Save the converted video to the desired location
        // Create a new FFMpeg instance
        $ffmpeg = FFMpeg::create();
        // Open the uploaded video file and get the duration
        $video = $ffmpeg->open($file);
        $video
            ->filters()
            ->resize(new Dimension(320, 240));


        // Define the resolutions to transcode the video to
        $resolutions = [
            '240' => [426, 240],
            '360' => [640, 360],
            '480' => [854, 480],
            '720' => [1280, 720],
        ];

        // Transcode the video into each resolution and save it to a new file
        foreach ($resolutions as $name => $resolution) {
            // Define the output format and options
            $format = new X264('aac');
            $format->setKiloBitrate(1000);

            // Transcode the video and save it to a new file
            $outputName = $name . '_' . $filename;
            $video->filters()->resize(new Dimension($resolution[0], $resolution[1]));
            $video->save($format, Storage::path($this->path . $outputName));
        }

    }
}