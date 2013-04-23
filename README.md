ZFrame 
======
一个用来自我提升学习的自己用的框架。如果大家看了不要喷我！
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