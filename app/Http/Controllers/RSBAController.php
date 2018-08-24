<?php
namespace App\Http\Controllers;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Activity;
use App\MemberList;
use App\MemberNow;


class RSBAController extends Controller
{

    public function volunteer(Request $request)
    {

        if (!$request->has(['title', 'details', 'action_time', 'member']))
            return response()->json([
            'err_code' => 2,
            'err_msg' => '数据不足！',
        ]);
        $va = new Activity;
        $va->title = $request->title;
        $va->type = 0;
        $va->publisher = 'test';//$request->session()->get('user');
        $va->details = $request->details;
        $va->time = $request->action_time;
        $va->member = $request->member;


        $ml = new MemberList;

        $mn = new MemberNow;


        if ($va->save() && $ml->save() && $mn->save())
            return response()->json([
            'err_code' => 0,
            'err_msg' => '',
            'id' => $va->id
        ]);
        else return response()->json([
            'err_code' => 3,
            'err_msg' => '保存失败！'
        ]);
    }

    public function award(Request $request)
    {

        if (!$request->has(['title', 'details', 'book_time', 'award', 'member_list']))
            return response()->json([
            'err_code' => 2,
            'err_msg' => '数据不足！',
        ]);
        $va = new Activity;
        $va->title = $request->title;
        $va->type = 1;
        $va->publisher = 'test';//$request->session()->get('user');
        $va->details = $request->details;
        $va->time = $request->book_time;
        $va->award = $request->award;


        $ml = new MemberList;
        $member_arr = $request->member_list;
        for ($i = 0; $i < 10; $i++) {
            $ml->{$i} = $member_arr[$i];
        }

        $mn = new MemberNow;
        if ($va->save() && $ml->save() && $mn->save())
            return response()->json([
            'err_code' => 0,
            'err_msg' => '',
            'id' => $va->id
        ]);
        else return response()->json([
            'err_code' => 3,
            'err_msg' => '保存失败！'
        ]);
    }

    public function member_query(Request $request, $id)
    {

        $ml = MemberList::find($id);
        $mn = MemberNow::find($id);
        if (($ml == null) || ($mn == null))
            return response()->json([
            'err_code' => 4,
            'err_msg' => '活动不存在'
        ]);
        $ml = $ml->toArray();
        $mn = $mn->toArray();
        unset($ml['id']);
        unset($mn['id']);
        unset($ml['created_at']);
        unset($mn['created_at']);
        unset($ml['updated_at']);
        unset($mn['updated_at']);
        $member_arr = array_values($ml);
        $current_arr = array_values($mn);
        return response()->json([
            'err_code' => 0,
            'err_msg' => '',
            'data' => [
                'member_list' => $member_arr,
                'current_member_list' => $current_arr
            ]
        ]);
    }

    public function modify_volunteer(Request $request, $id)
    {
        $name = $request->session()->get('name');
        $activity = Activity::find($id);

        if ($activity == null)
            return response()->json([
            'err_code' => 4,
            'err_msg' => '活动不存在！'
        ]);

        if ($activity->publisher != '$name')
            return response()->json([
            'err_code' => 5,
            'err_msg' => '不是发起人！'
        ]);
        if ($activity->time > date("Y-M-D h:m:s"))
            return response()->json([
            'err_code' => 6,
            'err_msg' => '活动已开始！'
        ]);

        if ($activity->member > $request->member)
            return response()->json([
            'err_code' => 7,
            'err_msg' => '人数不能小于当前人数！'
        ]);

        $activity->details = $request->details;
        $activity->time = $request->action_time;
        $activity->member = $request->member;
        
        if ($activity->save())
            return response()->json([
            'err_code' => 0,
            'err_msg' => ''
        ]);
        else return response()->json([
            'err_code' => 3,
            'err_msg' => '保存失败！'
        ]);
    }
    
    
    
    public function modify_award(Request $request, $id)
    {
        $name = $request->session()->get('name');
        $activity = Activity::find($id);
        $ml = MemberList::find($id);
        if ($activity == null)
            return response()->json([
            'err_code' => 4,
            'err_msg' => '活动不存在！'
        ]);
        if ($activity->publisher != '$name')
            return response()->json([
            'err_code' => 5,
            'err_msg' => '不是发起人！'
        ]);
        if ($activity->time > date("Y-M-D h:m:s"))
            return response()->json([
            'err_code' => 6,
            'err_msg' => '活动已开始！'
        ]);

        $activity->details = $request->details;
        $activity->time = $request->book_time;
        $activity->award = $request->award;

        $member_arr = $request->member_list;
        for ($i = 0; $i < 10; $i++) {
            $ml->{$i} = $member_arr[$i];
        }
        if ($activity->save() && $ml->save())
            return response()->json([
            'err_code' => 0,
            'err_msg' => ''
        ]);
        else return response()->json([
            'err_code' => 3,
            'err_msg' => '保存失败！'
        ]);
    }

    public function kill(Request $request,$id)
    {
        $name = $request->session()->get('name');
        $activity = Activity::find($id);
        $ml = MemberList::find($id);
        if ($activity == null)
            return response()->json([
            'err_code' => 4,
            'err_msg' => '活动不存在！'
        ]);
        if ($activity->publisher != '$name')
            return response()->json([
            'err_code' => 5,
            'err_msg' => '不是发起人！'
        ]);
        Activity::destroy($id);
        MemberList::destroy($id);
        MemberNow::destroy($id);

        return response()->json([
            'err_code' => 0,
            'err_msg' => ''
        ]);
    }


}