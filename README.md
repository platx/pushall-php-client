#PushAll.ru PHP Client
This is php client for **PushAll.ru API**, helps to send push messages via PushAll service.
##Usage
1. In your php file type this: 
```php
    require {dir}'/pushall/PushAll.php';
```
Where `{dir}` - full server path to a directory with our pushall folder.
2. Than you must to initialize PushAll client with following code:
```php
    /**
     * Client initialization
     */
    $pushAll = new PushAll(
        5271, 
        'da588eeab42e032070d0c1fae169b912'
    );
```
3. To send a push message use following code:
```php
    /**
     * Send a message
     */
    $result = $pushAll->send(array(
        'type' => PushAll::TYPE_SELF,
        'title' => 'some title',
        'text' => 'some message'
    ));
```