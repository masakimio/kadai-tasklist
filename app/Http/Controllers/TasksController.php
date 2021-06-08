<?php
    
namespace App\Http\Controllers;     

use Illuminate\Http\Request;
    
use App\Task;
    
    class TasksController extends Controller
    {
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
         // getでtasks/にアクセスされた場合の「一覧表示処理」
        public function index()
        {
            $data = [];
            if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            // ユーザの投稿の一覧を作成日時の降順で取得
            // （後のChapterで他ユーザの投稿も取得するように変更しますが、現時点ではこのユーザの投稿のみ取得します）
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);

            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
            
        }

            // Welcomeビューでそれらを表示
            return view('welcome', $data);
        }
        

     
    
        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
         // getでtasks/createにアクセスされた場合の「新規登録画面表示処理」
        public function create()
        {
           $task = new Task;

        // メッセージ作成ビューを表示
        return view('tasks.create', [
            'task' => $task,
        ]);
        }
    
        /**
         * Store a newly created resource in storage.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
         // postでtasks/にアクセスされた場合の「新規登録処理」
        public function store(Request $request)
        {
            // バリデーション
            $request->validate([
                'status' => 'required|max:10', 
                'content' => 'required|max:255',
            ]);
            
            // メッセージを作成
            $task = new Task;
            $task->status = $request->status;
            $task->content = $request->content;
            $task->user_id = \Auth::id();
            
            $task->save();

        // 前のURLへリダイレクトさせる
            return redirect('/');
        }
        /**
         * Display the specified resource.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
         //getでtasks/（任意のid）にアクセスされた場合の「取得表示処理」
        public function show($id)
        {
          // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);


        // 自分のものかチェック
        if (\Auth::id() === $task->user_id) {
            // 自分のもの
            return view('tasks.show', [
            'task' => $task,
        ]);
        } else {
            // 他人のもの
           return back();
        }
        return view('tasks.show', [
            'task' => $task,
        ]);
        }
        /**
         * Show the form for editing the specified resource.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         * 
         */
         // getでtasks/（任意のid）/editにアクセスされた場合の「更新画面表示処理」
        public function edit($id)
        {
            // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);

        if (\Auth::id() === $task->user_id) {
            // 自分のもの
            return view('tasks.edit', [
            'task' => $task,
        ]);
        } else {
            // 他人のもの
            return back();
        }
        // メッセージ編集ビューでそれを表示
        
        }
    
        /**
         * Update the specified resource in storage.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
         // putまたはpatchでtasks/（任意のid）にアクセスされた場合の「更新処理」
        public function update(Request $request, $id)
        {
            
             // バリデーション
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
             // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);
        // メッセージを更新
        $task->status = $request->status;    // 追加
        $task->content = $request->content;
        
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
        }
    
        /**
         * Remove the specified resource from storage.
         *
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
          // deleteでtasks/（任意のid）にアクセスされた場合の「削除処理」
        public function destroy($id)
        {
             // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);
         if (\Auth::id() === $task->user_id) {
            $task->delete();
        }

        // 前のURLへリダイレクトさせる
        return redirect('/');
        }
}
