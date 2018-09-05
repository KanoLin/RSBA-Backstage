<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Activity;
use App\MemberList;
use App\MemberNow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Validator;

class RSBAPublisherController extends Controller
{
    //活动发起人修改志愿者活动
    public function modify_volunteer(Request $request, $id)
    {
        $name = $request->session()->get('name');
        $activity = Activity::find($id);
        
        
        
        if ($activity == null)
            return response()->json([
            'err_code' => 4,
            'err_msg' => '活动不存在！'
        ]);

        if ($activity->publisher != $name)
            return response()->json([
            'err_code' => 5,
            'err_msg' => '不是发起人！'
        ]);
        if ($activity->time < date("Y-m-d H:i:s"))
            return response()->json([
            'err_code' => 6,
            'err_msg' => '活动已开始！'
        ]);

        if ($activity->member > $request->member)
            return response()->json([
            'err_code' => 7,
            'err_msg' => '人数不能小于当前人数！'
        ]);


        DB::beginTransaction();
        $activity->details = $request->details;
        $activity->time = $request->action_time;
        $activity->member = $request->member;



        if ($activity->save()) {
            DB::commit();
            return response()->json([
                'err_code' => 0,
                'err_msg' => ''
            ]);
        } else {
            DB::rollBack();
            return response()->json([
                'err_code' => 3,
                'err_msg' => '保存失败！'
            ]);
        }
    }
    
    
    //活动发起人修改福利活动
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
        if ($activity->publisher != $name)
            return response()->json([
            'err_code' => 5,
            'err_msg' => '不是发起人！'
        ]);
        if ($activity->time < date("Y-m-d H:i:s"))
            return response()->json([
            'err_code' => 6,
            'err_msg' => '活动已开始！'
        ]);

        DB::beginTransaction();
        $activity->details = $request->details;
        $activity->time = $request->book_time;
        $activity->award = $request->award;

        $member_arr = $request->member_list;
        for ($i = 0; $i < 10; $i++) {
            $ml->{'dep' . $i} = $member_arr[$i];
        }

        if ($activity->save() && $ml->save()) {
            DB::commit();
            return response()->json([
                'err_code' => 0,
                'err_msg' => ''
            ]);
        } else {
            DB::rollBack();
            return response()->json([
                'err_code' => 3,
                'err_msg' => '保存失败！'
            ]);
        }
    }

    //活动发起人删除活动
    public function kill(Request $request, $id)
    {
        $name = $request->session()->get('name');
        $activity = Activity::find($id);
        $ml = MemberList::find($id);
        if ($activity == null)
            return response()->json([
            'err_code' => 4,
            'err_msg' => '活动不存在！'
        ]);
        if ($activity->publisher != $name)
            return response()->json([
            'err_code' => 5,
            'err_msg' => '不是发起人！'
        ]);
        Activity::find($id)->user()->detach();
        Activity::destroy($id);
        MemberList::destroy($id);
        MemberNow::destroy($id);

        return response()->json([
            'err_code' => 0,
            'err_msg' => ''
        ]);
    }

    public function upload_img(Request $request,$id)
    {
        $name=$request->session()->get('name');
        $activity=Activity::find($id);
        if (empty($activity))
            return response()->json([
            'err_code' => 4,
            'err_msg' => '活动不存在！'
        ]);
        if ($activity->publisher != $name)
            return response()->json([
            'err_code' => 5,
            'err_msg' => '不是发起人！'
        ]);
        if ($activity->time < date("Y-m-d H:i:s"))
            return response()->json([
            'err_code' => 6,
            'err_msg' => '活动已开始！'
        ]);
        $message=[
            'required'=>'图片不能为空哦！',
            'image'=>'必须为图片格式哦！',
            'max'=>'图片大小不超过200kb哦！'

        ];

        $validator=Validator::make($request->all(),[
            'image'=>'required|image|max:200'
        ],$message);

        $errcode=0;
        $errmsg='';
        if ($validator->fails()){
            $errcode=11;
            foreach ($validator->errors()->all() as $err)
            $errmsg=$errmsg.$err;
            return response()->json([
                'err_code'=>$errcode,
                'err_msg'=>$errmsg
            ]);
        }
        $img=$request->file('image');
        $fileextension=$img->getClientOriginalExtension();
        $path=$img->storeAs(
            'RSBA-img',$id//.'.'.$fileextension
        );
        //$bool=Storage::disk('img')->putFileAs('/',$img,$id.'.'.$fileextension);
        if (!$path){
            $errcode=-2;
            $errmsg='服务器保存失败！';
        }
        return response()->json([
            'err_code'=>$errcode,
            'err_msg'=>$errmsg
        ]);
    }
}