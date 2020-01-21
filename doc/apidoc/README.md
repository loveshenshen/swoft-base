#API生成工具使用示例
```
apidoc -i ../../api/common/controllers -o ../../api/web/api/ -f ".*\.php$"
```
常用参数说明：
```
-i 表示输入，后面是文件夹路径
-o 表示输出，后面是文件夹路径
-c 默认会带上-c，在当前路径下寻找配置文件(apidoc.json)，如果找不到则会在package.json中寻找 "apidoc": { }
-f 为文件过滤，后面是正则表达式，示例中为只选php文件
-e 与-f类似，还有一个 -e 的选项，表示要排除的文件/文件夹，也是使用正则表达式
```