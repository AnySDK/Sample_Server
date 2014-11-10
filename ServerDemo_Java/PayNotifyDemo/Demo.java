/**
 * AnySDK 支付通知签名算法描述
 *
 *	1. 对所有不为空的参数按照参数名字母升序排列，sign参数不参与签名；
 *	2. 将排序后的参数名对应的参数值字符串方式按顺序拼接在一起；
 *	3. 做一次md5处理并转换成小写，得到的加密串1；
 *	4. 在加密串1末尾追加private_key，做一次md5加密并转换成小写，得到的字符串就是签名sign的值
 *	3. 得到的签名值与参数中的sign对比，相同则验证成功
 */

import com.chukong.anysdk.*;
import java.util.List;
import java.util.Collections;
import java.io.*;
import java.util.Comparator;

/**
 * AnySDK 支付通知验签demo
 *
 * demo 提供命令行方式运行验证
 *     javac Demo.java
 *     java Demo
 * 
 * demo 已在 jdk1.7.0_u51 下测试通过
 * @author libo<libo@chukong-inc.com>
 * @date 2014-08-13
 */

public class Demo {

	/**
	 * 测试参考参数:
	 *      amount=1
	 *		channel_number=999999
	 *		game_user_id=1
	 *		game_id=
	 *		order_id=PB069714081310394265014
	 *		order_type=999
	 *		pay_status 1
	 *		pay_time 2014-08-13 10:39:43
	 *		private_data=
	 *		product_count=0
	 *		product_id=jinbi
	 *		product_name=gold
	 *		server_id=13
	 *		source={"amount":"1","app_id":"1738","cp_order_id":"PB069714081310394265014","ext1":"","ext2":"","trans_id":"20282","trans_status":"1","user_id":"1799","sign":"08dfe21e1f4f26e334ec3b9b7f419b731dcd8255"}
	 *		user_id=1799
	 */
	private static String paramValues = "";

	/**
	 * 从通知参数里面获取到的签名值
	 */
	private static String originSign = "";
	
	public static void main(String[] args){
		// serverlet下
		originSign = "ea11ec63eabf3c96565cb779df56580b";
		
		// serverlet下需使用 getValues 生成待签字符串, 先要完善 getValues 方法
		paramValues = "19999991PB06971408131039426501499912014-08-13 10:39:430jinbigold13{\"amount\":\"1\",\"app_id\":\"1738\",\"cp_order_id\":\"PB069714081310394265014\",\"ext1\":\"\",\"ext2\":\"\",\"trans_id\":\"20282\",\"trans_status\":\"1\",\"user_id\":\"1799\",\"sign\":\"08dfe21e1f4f26e334ec3b9b7f419b731dcd8255\"}1799";
		
		PayNotify paynotify = new PayNotify();
		
		/**
		 * AnySDK分配的 PrivateKey
		 * 
		 * 正式使用时记得用AnySDK分配的正式的PrivateKey
		 */
		paynotify.setPrivateKey("anysdkPrivateKeyxxxxxx");
	
		// 这是验签测试
		System.out.println("参考签名值: " + originSign + "\n");
		System.out.println("待签字符串: " + paramValues + "\n");
		System.out.println("计算得到的签名值: " + paynotify.getSign(paramValues) + "\n");
		if (paynotify.checkSign(paramValues, originSign)){
			System.out.println("验证签名成功\n");
		} else {
			System.out.println("验证签名失败");
		}
	}
	
	/**
	 * 将参数名从小到大排序，结果如：adfd,bcdr,bff,zx
	 * 
	 * @param List<String> paramNames 
	 */
	public void sortParamNames(List<String> paramNames) {
			Collections.sort(paramNames, new Comparator<String>() {
				public int compare(String str1,String str2) {
					return str1.compareTo(str2);
				}
			});
	}
	
	/**
	 * 从 HTTP请求参数 生成待签字符串, 此方法需要在 serverlet 下测试, 测试的时候取消注释, 引入该引入的类
	 */
	/*public static String getValues(HttpServletRequest request){
		Enumeration<String> requestParams=request.getParameterNames();//获得所有的参数名
		List<String> params=new ArrayList<String>();
		while (requestParams.hasMoreElements()) {
			params.add((String) requestParams.nextElement());
		}
		sortParamNames(params);// 将参数名从小到大排序，结果如：adfd,bcdr,bff,zx

		String paramValues="";
		for (String param : params) {//拼接参数值
			if (param.equals("sign")) {
				originSign = request.getParameter(param);
				continue;
			}
			String paramValue=request.getParameter(param);
			if (paramValue!=null) {
				paramValues+=paramValue;
			}
		}
		
		return paramValues;
	}
	*/
}
