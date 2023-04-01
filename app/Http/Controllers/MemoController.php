<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Memo;

class MemoController extends Controller
{
    protected $memos;

    // public function __construct($memos)
    public function __construct()
    {
        // $this->memos = $memos;
    }

    public function index(Request $request)
    {
        $requ = $request->input();
        $requ['keyword'] = $request->input('keyword');
        $memos = Memo::where('user_id', Auth::user()->id)
                    ->where('content', 'LIKE', "%{$requ['keyword']}%")
                    ->orderBy('id', 'desc')
                    ->offset($requ['limit_offset'])
                    ->limit($requ['limit_size'])
                    ->get();
        return $memos;
    }

    public function save(Request $request)
    {
        $requ = $request->input();
        if (empty($requ['update']) === false) {
            foreach ($requ['update'] as $memo) {
                $this->edit($memo['id'], $memo);
            }
        }
        if (empty($requ['insert']) === false) {
            return $this->store($requ['insert']);
        }
        return 0;
    }

    private function store($requ)
    {
        $user = Auth::user();
        $memo = new Memo;
        $memo->content = $requ['content'] ?? '';
        $memo->user_id = $user->id;
        $memo->user_name = $user->name;
        $memo->save();
        return $memo->id;
    }

    private function edit(int $id, $requ): bool
    {
        Memo::where([
            'user_id' => Auth::user()->id,
            'id'      => $id,
        ])->update(['content' => $requ['content']]);
        return true;
    }

    private function getInsertData($requ): array
    {
        $insertMemos = array_filter($requ, function($e) {
            return isset($e['id']) === false;
        });
        return $insertMemos[0] ?? [];
    }

    private function getUpdateData($requ): array
    {
        return array_filter($requ, function($e) {
            return isset($e['id']);
        });
    }

    public function destroy(Request $request)
    {
        $requ = $request->input();
        $memos = Memo::where('user_id', Auth::user()->id)
                    ->whereIn('id', $requ)
                    ->delete();
        return response()->noContent();
    }
}
