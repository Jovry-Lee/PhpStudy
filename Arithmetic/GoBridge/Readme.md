题目

馒头和奶昔是对dog夫妻，它们有两个baby：淘淘和皮皮。一天晚上，它们一家四口出去游玩，回家过程中迷路了，困在了一座桥边上。馒头家只带了一个手电筒，由于是荒山夜晚，没有手电筒谁也不敢私自过桥。桥呢又比较窄，一次最多只能过2只狗。馒头家每个成员单独过桥所需要的时间依次是：1min、2min、5min、8min。如果两个人一起过桥，需耗费的时间是走得慢的那个成员所需要的时间。馒头一家都想早点过桥回家，现在请你帮助它们设计一个过桥方案，使的总耗费时间最少。馒头脑子不太好用，希望你能够给ta一个通用的方案，以便下次和朋友出去玩时使用，即帮忙给出一个适用任何成员数量的最优过桥方案。注：每个成员单独过桥的时间是已知的。


分析

题目主要就以下两个点：

输入是：成员数量，以及每个成员单独过桥的时间。
输出是所有人过桥的最短时间
问题是怎样的方案耗时才是最短呢？显然，第一印象是：过桥最快的那个成员，依次把其余成员带过桥（因为只有一个手电筒）。比如，下面这个案例：

A、B、C、D四个人的过桥时间分别是：1、8、9、10分钟。这种情况下的最短过桥时间方案是：

A送D过桥，10分钟
A回去，1分钟
A送C过桥，9分钟
A回去，1分钟
A送B过桥，8分钟
一共10+1+9+1+8=29分钟

但是，最优解还可能发生在另外一种情况，这也是很容易想到的情况：最慢的成员一定要过桥，那么它的时间是必须花费的，那么谁和最慢的成员过桥会最省时间呢？你猜对了，就是过桥最慢的成员带着次慢的成员一起过桥，比如下面这个案例：

还是上面的场景，更改下各自的过桥时间，A、B、C、D单独过桥的时间依次为：1、2、5、8分钟。此时最短过桥方案是：

A和B先过桥，花费2分钟
A回去，花费1分钟
C和D一起过桥，花费8分钟
B回去，花费2分钟
A和B一起过桥，2分钟
一共花费：2+1+8+2+2=15分钟

上面第一步是最快的两个先过桥，因为第二快的成员只有要回去接人，也就是最慢和次慢的成员过桥后不能再让它们把手电筒送回来。所以是最快的两个先过桥，然后是最慢的两个过桥，最后是次快的成员把手电筒送回去。

示例的代码运行结果为:
```
$ php Bridge.php
请输入总人数: 4
请输入上面每个成员的过桥时间:
1
2
5
8
成员过桥最少花费时间是: 15
```