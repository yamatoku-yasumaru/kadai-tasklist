<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        // 認証済みの場合
        if(\Auth::check()){ 
            // 認証済みユーザを取得
            $user = \Auth::user();
            // タスク一覧を作成日時の降順で取得(自身のタスクのみ取得)
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);
            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
        }
        
        // dashboardビューでそれらを表示
        return view('dashboard', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $task = new Task;
        
        return view('tasks.create',[
            'task' => $task,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required',
        ]);
        
         /*
        $task = new Task;
        $task->status = $request->status;
        $task->content = $request->content;
        $task->save();
        */
        
        // 認証済みユーザのタスクとして作成
        $request->user()->tasks()->create([
            'status' => $request->status,
            'content' => $request->content,
        ]);
        
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   public function show($id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        // 認証済みユーザの場合、タスク詳細ビューを表示
        if (\Auth::id() === $task->user_id) {
            
            return view("tasks.show", [
                "task" => $task,
            ]);
        }
        
        // トップページにリダイレクトさせる
        return redirect("/")
            ->with("failed");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        // 認証済みユーザの場合、タスク編集ビューを表示
        if (\Auth::id() === $task->user_id) {
            
            return view('tasks.edit',[
                'task' => $task,
            ]);
        }
        
        // トップページにリダイレクトさせる
        return redirect("/")
            ->with("failed");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required',
            ]);
        
        // idの値でタスクを検索して取得    
        $task = Task::findOrFail($id);
        
        $task->status = $request->status;
        $task->content = $request->content;
        $task->save();

        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        //認証済みユーザがその投稿の所有者である場合は投稿を削除
        if (\Auth::id() === $task->user_id) {
            $task->delete();
            return redirect("/")
                ->with('success','Delete Successful');
        }

        // トップページにリダイレクトさせる
        return redirect("/")
            ->with('Delete Failed');
    }
}
