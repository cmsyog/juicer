# pump 
php template engine, just like the light javascript templete engine -  [Juicer](http://juicer.name/)

###Usage
* See  .
 
###Widgets usage example
```html
 $tpl = file_get_contents('demo.pump');
$data = array(
    'list' => array(
        array(
            'name' => '讨论组',
            'age' => 18,
        ),
        array(
            'name' => '私は',
            'age' => 28,
        ),
    ),

    'maps' => array(
        array(
            'name' => 'li',
            'age' => 38,
        ),
        array(
            'name' => 'الصين',
            'age' => 48,
        ),
    ),
    'page'=>3,
);
$pump = new pump();
//$pump->setTags('','');
echo $pump->ParseTemplate($tpl, $data);
```
