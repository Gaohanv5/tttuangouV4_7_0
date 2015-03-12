  URL-Rewrite是一种URL地址静态化技术，可有效增加搜索引擎的收录。

  下面分别介绍天天团购系统在IIS 服务器下的Rewrite设置方法（Apache服务器默认即可支持）：


一、路径模式伪静态；

1、php中默认均开启了Rewrite模块，通常来说iis服务器也不需要额外的设置；

2、进入天天团购系统后台->系统设置->URL地址设置，将Rewrite方式改为 路径模式即可立即生效；

备注：设置成功后，请测试网站是否可以正常访问，如有问题请咨询空间商php环境中是否开启了rewrite模块；


二、标准Rewrite模式：

1.在IIS的Isapi上添加个筛选器，筛选器名称Rewrite，可执行文件选择此目录的 Rewrite.dll文件；
（如果没有选择此目录的 Rewrite.dll文件，请手工添加此目录下的httpd.ini中的Rewrite规则）；

2.重新启动IIS；

3.进入天天团购后台->系统设置->URL地址设置，选择Rewrite方式为 标准Rewrite模式；

4.URL标准静态化设置成功。

