<?php

require __DIR__.'/pushall/PushAll.php';

use platx\pushall\PushAll;


/**
 * Client initialization
 */
$pushAll = new PushAll(5271, 'da588eeab42e032070d0c1fae169b912', PushAll::RESPONSE_TYPE_ARRAY);


/**
 * Send a message
 */
$result = $pushAll->send(array(
    'type' => PushAll::TYPE_SELF,
    'title' => 'test 2',
    'text' => 'test 2 test test 2'
));

echo '<pre>';
print_r($result);
echo '</pre>';