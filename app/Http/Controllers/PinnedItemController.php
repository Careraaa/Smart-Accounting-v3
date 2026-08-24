<?php

namespace App\Http\Controllers;

use App\Models\PinnedItem;
use Illuminate\Http\Request;

class PinnedItemController extends Controller
{
    public function toggle(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string|max:255',
            'url'   => 'required|string|max:500',
        ]);

        $existing = PinnedItem::where('user_id', auth()->id())
            ->where('url', $data['url'])
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            $maxSort = PinnedItem::where('user_id', auth()->id())->max('sort_order') ?? 0;
            PinnedItem::create([
                'user_id'    => auth()->id(),
                'label'      => $data['label'],
                'url'        => $data['url'],
                'sort_order' => $maxSort + 1,
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['pinned' => $existing ? false : true]);
        }

        return redirect()->back();
    }
}
