
<?php
    require_once("db.php");
    if( !empty($_REQUEST) ){
        $post_array=[];
        $post_array = $_REQUEST;
        if (!empty($post_array['data'])){
            //防一手xss 跟 sql Injection
            $data = filterWords($post_array['data']);
        }
        $result = (object)[];
        $result->status = false;
        
        switch ($post_array['action']){
            case "insert":
                $sql = "INSERT INTO  `post_datas` (`data`) VALUE ('".$data."') ";
                $query_result = mysqli_query($link,$sql);
                if ($query_result){
                    $result->status = true;
                }
                break;
            case "update":    
                $sql = "UPDATE  `post_datas` SET `data` = '".$data['content']."' WHERE `id`= ".$data['id'].";";
                $query_result = mysqli_query($link,$sql);
                if ($query_result){
                    $result->status = true;
                }
                break;
            case "delete":
                $sql = "DELETE FROM `post_datas` WHERE `id`= ".$data.";";
                $query_result = mysqli_query($link,$sql);
                if ($query_result){
                    $result->status = true;
                }
                break;
            case "select":
                $sql = "SELECT * FROM `post_datas`";
                $query_result = mysqli_query($link,$sql);
                if ($query_result){
                    $result->status = true;
                    if( mysqli_num_rows($query_result) > 0 ){
                        while ($row = mysqli_fetch_object($query_result)) {

                            $datas[] = $row;
                        }
                        $result->data = json_encode($datas);
                    }
                }
                break;
        }
        print_r(json_encode($result));
        return;

    }
    function filterWords($str)
    {
        $farr = array(
                "/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
                "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
                "/select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is"
        );
        $str = preg_replace($farr,'',$str);
        return $str;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>簡易布告欄系統</title>
    <style>
        .blueBtn {
            appearance: button;
            background-color: #1899D6;
            border: solid transparent;
            border-radius: 16px;
            border-width: 0 0 4px;
            box-sizing: border-box;
            color: #FFFFFF;
            cursor: pointer;
            display: inline-block;
            font-family: din-round,sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: .8px;
            line-height: 20px;
            margin: 0;
            outline: none;
            overflow: visible;
            padding: 13px 16px;
            text-align: center;
            text-transform: uppercase;
            touch-action: manipulation;
            transform: translateZ(0);
            transition: filter .2s;
            user-select: none;
            -webkit-user-select: none;
            vertical-align: middle;
            white-space: nowrap;
           
        }
        .blueBtn:after {
            background-clip: padding-box;
            background-color: #1CB0F6;
            border: solid transparent;
            border-radius: 16px;
            border-width: 0 0 4px;
            bottom: -4px;
            content: "";
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            z-index: -1;
        }
        .blueBtn:hover:not(:disabled) {
            filter: brightness(1.1);
            -webkit-filter: brightness(1.1);
        }
        .PostArea{
            display:none;
        }
        .block{
            display: inline-block;
            float:left;
            width: 100%;
        }
    </style>
</head>
<body>
    <script src="https://cdn.ckeditor.com/4.7.3/standard/ckeditor.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
        <div class="block">
            <div id="NewPost">
                <h1>新增布告</h1>
                <textarea name="editor1" id="editor1"></textarea>
            </div>
            <button class="blueBtn" onclick="insertPost()">新增布告</button>
        </div>
        <div class="block">
            <h1>布告展示</h1>
            <div id="content"></div>
            <div class="block" style="border-style:solid;" >
                <h2 style="border-style:outset;display: inline-block;margin-left:5px; width:10%;">  
                </h2>
                <div style="border-style:solid;display: inline-block;margin: 5px; width:98%;word-wrap: break-word;"></div>
            </div>
        </div>
    <script>
        CKEDITOR.replace("editor1");
        getAllPost();
        function showPostArea(){
            $("#NewPost").show();
        }
        function insertPost(){
            CKEDITOR.instances.editor1.updateElement();
            var datas = {
                action: "insert",
                data: $('#editor1').val(),
            }

            $.ajax({
                url:"blog.php",
                method:"post",
                data: datas,
                success: function(res){
                var result = JSON.parse(res);
                if ( result.status == true ){
                    alert('新增成功')
                    getAllPost()
                }
                else{
                    alert('新增失敗')
                }
                }
            });
        }
        function deletePost(id){
            var datas = {
                action: "delete",
                data: id,
            }

            $.ajax({
                url:"blog.php",
                method:"post",
                data: datas,
                success: function(res){
                var result = JSON.parse(res);
                if ( result.status == true ){
                    alert('刪除成功');
                    getAllPost()
                }
                else{
                    alert('刪除失敗')
                }
                }
            });
        }
        function updatePost(id){
            CKEDITOR.replace('content_editor'+id);
            $('#sub'+id).show();

        }
        function submitUpdate(id){
            for(instance in CKEDITOR.instances)
                CKEDITOR.instances[instance].updateElement();
            $('#sub'+id).hide();
            var datas = {
                action: "update",
                data: {
                    id: id,
                    content: $('#content_editor'+id).val(),
                },
            }

            $.ajax({
                url:"blog.php",
                method:"post",
                data: datas,
                success: function(res){

                    var result = JSON.parse(res);
                    if ( result.status == true ){
                        alert('提交成功');
                        getAllPost()
                    }
                    else{
                        alert('提交失敗')
                    }
                }
            });
        }
        function getAllPost(){
            $.ajax({
                url:"blog.php",
                method:"post",
                data: {
                    action: "select",
                },
                success: function(res){

                var result = JSON.parse(res);

                if ( result.status == true ){
                    $('#content').html("");
                    JSON.parse(result.data).forEach(function(post_data){
                        console.log(post_data)
                        if ( post_data.is_delete!='Y'){
                            var html_temp = '<div class="block" style="border-style:solid;"><h2 style="border-style:outset;display: inline-block;margin-left:5px; width:10%;">'+post_data.id+'</h2><div style="border-style:solid;display: inline-block;margin: 5px; width:98%;word-wrap: break-word;">'+post_data.data+'</div><textarea style="display:none;"name="content_editor'+post_data.id+'" id="content_editor'+post_data.id+'"></textarea><button class="blueBtn" onclick="deletePost('+post_data.id+')">刪除布告</button><button class="blueBtn" onclick="updatePost('+post_data.id+')">修改布告</button><button class="blueBtn" id="sub'+post_data.id+'" style="display:none;"onclick="submitUpdate('+post_data.id+')">提交修改</button></div></div>';
                            $('#content').append(html_temp);
                        }

                    });
                }
                else{
                    alert('取得帖子失敗')
                }
                }
            });

        }
    </script>
</body>
</html>
