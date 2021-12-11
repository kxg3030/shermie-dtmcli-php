<p align="center" style="font-weight: bolder">dtmcli-php</p>
<p align="center">

![license](https://img.shields.io/github/license/kxg3030/dtmcli-php)

</p>

### 项目介绍
<hr>

支持[DTM跨语言分布式事务管理](https://dtm.pub)所编写的php客户端，只支持http调用

### 支持事务类型

- Tcc事务和子事务嵌套
- Xa事务
- 消息事务
- Saga事务
- 子事务屏障

### 安装
   ```javascript
composer require sett/dtmcli-php
```

### 示例

 - tcc
    ```javascript
    // 127.0.0.1:36789为dtm默认端口
    $trans   = new TccTrans("127.0.0.1:36789");
    // 获取新事务ID
    $gid     = $trans->createNewGid();
    // 事务操作 
    $success = $trans->withOperate($gid, function (TccTrans $tccTrans) use ($baseUrl) {
        $result = $tccTrans->callBranch(
            ["amount" => 30],
            "$baseUrl/dtm/tcc/transOut",
            "$baseUrl/dtm/tcc/transOutConfirm",
            "$baseUrl/dtm/tcc/transOutCancel"
        );
        if (!$result) {
            echo "call branch fail\n";
            return false;
        }
        return $tccTrans->callBranch(
            ["amount" => 30],
            "$baseUrl/dtm/tcc/transIn",
            "$baseUrl/dtm/tcc/transInConfirm",
            "$baseUrl/dtm/tcc/transInCancel"
        );
    });
    ```
   
 - saga
    ```javascript
    $trans = new SagaTrans("127.0.0.1:36789");
    $gid   = $trans->createNewGid();
    $trans
        ->withGid($gid)
        ->withOperate("$baseUrl/dtm/saga/transOut", "$baseUrl/dtm/saga/transOutRevert", ["amount" => 30])
        ->withOperate("$baseUrl/dtm/saga/transIn", "$baseUrl/dtm/saga/transInRevert", ["amount" => 30]);
    $success = $trans->submit();
    ```
 
 - 事务消息
    ```javascript
    $trans = new MsgTrans("127.0.0.1:36789");
    $gid   = $trans->createNewGid();
    $trans
        ->withOperate("$baseUrl/dtm/msg/transOut", ["amount" => 30])
        ->withOperate("$baseUrl/dtm/msg/transIn", ["amount" => 30])
        ->withQueryUrl("$baseUrl/dtm/msg/query")
        ->prepare();
    $success = $trans->submit();
    ```
   
 - 子事务屏障
     ```javascript
     class UserDatabase implements IDatabase
      {
   
       public function execute(string $query) {
           // TODO: Implement execute() method.
       }
   
       public function query(string $query): bool {
           // TODO: Implement query() method.
       }
   
       public function rollback() {
           // TODO: Implement rollback() method.
       }
   
       public function commit() {
           // TODO: Implement commit() method.
       }
      }

   $baseUrl = "http://127.0.0.1:18310";
   try {
         $trans    = new BarrierTrans([
         "trans_type" => "tcc",
         "gid"        => "ac130059_4pQHea5Xtsq",
         "op"         => "prepare",
         "branch_id"  => "01"
         ]);
         $database = new UserDatabase();
         $success  = $trans->call($database, function (IDatabase $database) {
            // 使用当前数据库连接操作,保证所有操作都在一个本地事务中
            // do what you want...
            return true;
         });
         echo "transaction result {$success}";
   } catch (Exception $exception) {
         var_dump($exception->getTraceAsString());
         echo "exception with error " . $exception->getMessage();
   }
     ```



