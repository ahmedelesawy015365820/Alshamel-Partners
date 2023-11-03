<?php

namespace App\Traits;

trait VideoLink
{

    public function video()
    {
        // morphed one
        return $this->morphOne(\App\Models\VideoLink::class, 'model');
    }

    public function getVideoLinkAttribute()
    {
        $video = $this->video()->first();
        if (!$video) {
            return null;
        }

        $link = $video->link;
        //  return with iframe and check which type it
        // if youtube link
        if (strpos($link, 'youtube') !== false) {
            $link = str_replace('watch?v=', 'embed/', $link);
            return '<iframe width="100%" height="315" src="' . $link . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        } elseif (strpos($link, 'vimeo') !== false) {
            $link = str_replace('vimeo.com/', 'player.vimeo.com/video/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        } elseif (strpos($link, 'drive.google') !== false) {
            $link = str_replace('open?id=', 'uc?id=', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from facebook
        elseif (strpos($link, 'facebook') !== false) {
            $link = str_replace('facebook.com/', 'facebook.com/plugins/video.php?href=', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from instagram
        elseif (strpos($link, 'instagram') !== false) {
            $link = str_replace('instagram.com/', 'instagram.com/p/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from twitter
        elseif (strpos($link, 'twitter') !== false) {
            $link = str_replace('twitter.com/', 'twitter.com/i/status/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from tiktok
        elseif (strpos($link, 'tiktok') !== false) {
            $link = str_replace('tiktok.com/', 'tiktok.com/embed/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from dailymotion
        elseif (strpos($link, 'dailymotion') !== false) {
            $link = str_replace('dailymotion.com/', 'dailymotion.com/embed/video/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from twitch
        elseif (strpos($link, 'twitch') !== false) {
            $link = str_replace('twitch.tv/', 'player.twitch.tv/?video=', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from soundcloud
        elseif (strpos($link, 'soundcloud') !== false) {
            $link = str_replace('soundcloud.com/', 'w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from spotify
        elseif (strpos($link, 'spotify') !== false) {
            $link = str_replace('spotify.com/', 'open.spotify.com/embed/track/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from apple music
        elseif (strpos($link, 'music.apple') !== false) {
            $link = str_replace('music.apple.com/', 'embed.music.apple.com/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from deezer
        elseif (strpos($link, 'deezer') !== false) {
            $link = str_replace('deezer.com/', 'www.deezer.com/plugins/player?format=classic&autoplay=false&playlist=false&width=700&height=350&color=007FEB&layout=dark&size=medium&type=tracks&id=', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google photos
        elseif (strpos($link, 'photos.google') !== false) {
            $link = str_replace('photos.google.com/', 'photos.google.com/share/AF1Qip', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google drive
        elseif (strpos($link, 'drive.google') !== false) {
            $link = str_replace('drive.google.com/', 'drive.google.com/file/d/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google docs
        elseif (strpos($link, 'docs.google') !== false) {
            $link = str_replace('docs.google.com/', 'docs.google.com/file/d/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google slides
        elseif (strpos($link, 'slides.google') !== false) {
            $link = str_replace('slides.google.com/', 'docs.google.com/presentation/d/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google sheets
        elseif (strpos($link, 'sheets.google') !== false) {
            $link = str_replace('sheets.google.com/', 'docs.google.com/spreadsheets/d/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google forms
        elseif (strpos($link, 'forms.google') !== false) {
            $link = str_replace('forms.google.com/', 'docs.google.com/forms/d/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google sites
        elseif (strpos($link, 'sites.google') !== false) {
            $link = str_replace('sites.google.com/', 'sites.google.com/view/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google maps
        elseif (strpos($link, 'maps.google') !== false) {
            $link = str_replace('maps.google.com/', 'maps.google.com/maps?q=', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google calendar
        elseif (strpos($link, 'calendar.google') !== false) {
            $link = str_replace('calendar.google.com/', 'calendar.google.com/calendar/embed?src=', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google keep
        elseif (strpos($link, 'keep.google') !== false) {
            $link = str_replace('keep.google.com/', 'keep.google.com/u/0/#NOTE/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google translate
        elseif (strpos($link, 'translate.google') !== false) {
            $link = str_replace('translate.google.com/', 'translate.google.com/translate?sl=auto&tl=auto&u=', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google classroom
        elseif (strpos($link, 'classroom.google') !== false) {
            $link = str_replace('classroom.google.com/', 'classroom.google.com/u/0/c/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google hangouts
        elseif (strpos($link, 'hangouts.google') !== false) {
            $link = str_replace('hangouts.google.com/', 'hangouts.google.com/call/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google duo
        elseif (strpos($link, 'duo.google') !== false) {
            $link = str_replace('duo.google.com/', 'duo.google.com/call/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google meet
        elseif (strpos($link, 'meet.google') !== false) {
            $link = str_replace('meet.google.com/', 'meet.google.com/lookup/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google chat
        elseif (strpos($link, 'chat.google') !== false) {
            $link = str_replace('chat.google.com/', 'chat.google.com/u/0/room/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        }
        // video from google photos
        elseif (strpos($link, 'photos.google') !== false) {
            $link = str_replace('photos.google.com/', 'photos.google.com/share/', $link);
            return '<iframe src="' . $link . '" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        } else {
            return '<iframe width="100%" height="315" src="' . $link . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        }

    }

    //  set video_link attribute
    public function setVideoLinkAttribute($value)
    {
        dd("D");
        $this->attributes['video_link'] = $this->getVideoLink($value);
    }

}
