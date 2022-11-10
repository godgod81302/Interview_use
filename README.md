# Interview_use
為節省時間 我這邊不做分離 直接放在blog.php那隻 ( 不過php部分跟前端code是完全分離 且透過ajax溝通，並未混用  
簡易建置只需要設定好xampp 直接將上面三隻php放到htdoc下，再將test.sql匯入db即可  
接著請直接訪問http://localhost/blog.php  
Api url都是推到同一支，前端用post傳參，  
post data參數範例如下  
{  
    action: "insert",  
    data: "希望存入的內文",  
}  
{  
    action: "delete",  
    data: id,                                        =>欲刪除的id  
}  
{  
    action: "update",  
    data: {  
        id: id,                                      =>欲更新的id  
        content: 希望更新的內文",  
    },  
}  
{  
    action: "select",                                =>目前沒做單一查詢 全撈即可  
}  
  
api回傳格式  
{  
  status: true/false,                               =>表示請求成功與否  
  data: "",                                         =>回傳由資料庫撈取的資料 (如果有的話  
}  
  
心得:可以的話還是laravel方便，不過心血來潮用原生php寫，想想sql那段用bindValue比較安全，防sql injection效果較好。  
