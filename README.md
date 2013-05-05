ZFrame 
======
一个自我提升学习的，自己用的框架。
#注意#
框架很多东西来自YII
###入口文件###
*注意路径
<pre><code>
    require dirname(dirname(__FILE__)).'/frame/Z.php';
	$config =  include dirname(__FILE__).'/Protected/config/config.php';

	Z\Z::createWebApplication($config)->run();
</code></pre>

###目录规范###

    1.所有的文件名称和类名相同(除去名字空间)
    2.所有目录大写，通名字空间相同
    3.项目目录实例
        *index.php
        *Protected
        *--Controller
        *--...
###目录文件规范###
* 如果项目中的文件是包含一个可引用的类：目录必须同名字空间
* 如果项目中一个目录下没有可用的类，例如配置文件或者是缓存文件，
  请使用小写的目录名称和小写的文件名称
