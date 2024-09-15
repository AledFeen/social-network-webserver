<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostTag;
use App\Models\PreferredTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PreferredTagService
{
    public function ignore(array $request) : bool
    {
        $tags = PostTag::where('post_id', $request['post_id'])->get();

        DB::beginTransaction();
        try {

            foreach ($tags as $tag) {
                PreferredTag::where('tag', $tag->tag)
                    ->where('user_id', Auth::id())
                    ->delete();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return false;
        }
    }
}
