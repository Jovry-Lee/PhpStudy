##### 题目
现在只有两只杯子，容量分别是：5升和7升，问题是：在只用这两个杯子的前提下，如何才能得到4升水？假设：水可以无限使用。

##### 分析
这类题有一个套路，小容量的杯子不断往大杯子里面倒水，大杯子满了之后就把大杯子全倒掉。先举个简单的例子，比如：3升和5升的杯子，得到4升水，下面步骤中的第一个数字表示3升杯子中的水量、第二个数字5升杯子中的水量，开始时都为0：

0，0
3，0
0，3
3，3
1，5
1，0
0，1
3，1
0，4
bingo，得到4升水了
到这你可能发现一些规律了：

小杯不断往大杯中倒水
大杯满了的时候，大杯全部倒掉
小杯继续往大杯倒水
重复上面的步骤，直到得到目标水量，或者实现不了目标而退出循环
这是不是很像数学中的某一种运算呢？对，就是“%”取余运算。就拿上面的案例来说：

3 % 5 = 3，第一杯倒完后大杯中有3升水
6 % 5 = 1，6表示当前倒的是第二小杯水，第二小杯水倒完的时候，大杯可以得到1升水
9 % 5 = 4，表示第三小杯水倒完后，我们就能得到4升水了。

再举个例子，比如本题中的5升和7升杯子，如何得到4升水？

5 % 7 = 5
10 % 7 = 3
15 % 7 = 1
20 % 7 = 6
25 % 7 = 4
bingo，得到目标4升水了。


package WaterPouring;
import java.util.Scanner;

public class WaterPouring {
    public static void main(String[] args) {
        //实例代码解决：用两个较小杯子得到指定数量水的问题
        Scanner sc = new Scanner(System.in);
        System.out.println("请输入两个小杯子的容量，用空格隔开。");

        int cup1 = sc.nextInt();
        int cup2 = sc.nextInt();

        System.out.println("请输入你希望得到多少升水，整形数字。");
        int target = sc.nextInt();

        if(cup1>cup2) {//保证杯子1的容量较小
            int tmp = cup1;
            cup1 = cup2;
            cup2 = tmp;
        }
        //因为每次都是小杯子装满水往大杯子中倒水，倒完后小杯子剩余水量总是0
        //所以只需要跟踪大杯子剩余水量即可知道整个倒水的操作过程
        System.out.println("第二个杯子的水量为: " + 0);

      //先倒一次水，主要是为了处理倒水失败的情况
        int flag = cup1 % cup2;
        System.out.println("第二个杯子的水量为: "+cup1);

        if(flag == target) {
            System.out.println("小杯往大杯倒一次水即可实现目标");
            return;
        }

        int count = 2;
        while(true) {
            int remain = (count * cup1) % cup2;
            System.out.println("第二个杯子的水容量: "+remain);

            if(remain == target) {
                System.out.println("倒水成功，得到了目标水量");
                break;
            }else if(remain == flag) {//得到循环数列，实现不了目标
                System.out.println("倒水失败，得不到目标水量");
                break;
            }

            count++;
        }
    }
}
