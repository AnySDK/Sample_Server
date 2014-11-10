/**
 * AnySDK ֧��֪ͨǩ���㷨����
 *
 *	1. �����в�Ϊ�յĲ������ղ�������ĸ�������У�sign����������ǩ����
 *	2. �������Ĳ�������Ӧ�Ĳ���ֵ�ַ�����ʽ��˳��ƴ����һ��
 *	3. ��һ��md5����ת����Сд���õ��ļ��ܴ�1��
 *	4. �ڼ��ܴ�1ĩβ׷��private_key����һ��md5���ܲ�ת����Сд���õ����ַ�������ǩ��sign��ֵ
 *	3. �õ���ǩ��ֵ������е�sign�Աȣ���ͬ����֤�ɹ�
 */

import com.chukong.anysdk.*;
import java.util.List;
import java.util.Collections;
import java.io.*;
import java.util.Comparator;

/**
 * AnySDK ֧��֪ͨ��ǩdemo
 *
 * demo �ṩ�����з�ʽ������֤
 *     javac Demo.java
 *     java Demo
 * 
 * demo ���� jdk1.7.0_u51 �²���ͨ��
 * @author libo<libo@chukong-inc.com>
 * @date 2014-08-13
 */

public class Demo {

	/**
	 * ���Բο�����:
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
	 * ��֪ͨ���������ȡ����ǩ��ֵ
	 */
	private static String originSign = "";
	
	public static void main(String[] args){
		// serverlet��
		originSign = "ea11ec63eabf3c96565cb779df56580b";
		
		// serverlet����ʹ�� getValues ���ɴ�ǩ�ַ���, ��Ҫ���� getValues ����
		paramValues = "19999991PB06971408131039426501499912014-08-13 10:39:430jinbigold13{\"amount\":\"1\",\"app_id\":\"1738\",\"cp_order_id\":\"PB069714081310394265014\",\"ext1\":\"\",\"ext2\":\"\",\"trans_id\":\"20282\",\"trans_status\":\"1\",\"user_id\":\"1799\",\"sign\":\"08dfe21e1f4f26e334ec3b9b7f419b731dcd8255\"}1799";
		
		PayNotify paynotify = new PayNotify();
		
		/**
		 * AnySDK����� PrivateKey
		 * 
		 * ��ʽʹ��ʱ�ǵ���AnySDK�������ʽ��PrivateKey
		 */
		paynotify.setPrivateKey("anysdkPrivateKeyxxxxxx");
	
		// ������ǩ����
		System.out.println("�ο�ǩ��ֵ: " + originSign + "\n");
		System.out.println("��ǩ�ַ���: " + paramValues + "\n");
		System.out.println("����õ���ǩ��ֵ: " + paynotify.getSign(paramValues) + "\n");
		if (paynotify.checkSign(paramValues, originSign)){
			System.out.println("��֤ǩ���ɹ�\n");
		} else {
			System.out.println("��֤ǩ��ʧ��");
		}
	}
	
	/**
	 * ����������С�������򣬽���磺adfd,bcdr,bff,zx
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
	 * �� HTTP������� ���ɴ�ǩ�ַ���, �˷�����Ҫ�� serverlet �²���, ���Ե�ʱ��ȡ��ע��, ������������
	 */
	/*public static String getValues(HttpServletRequest request){
		Enumeration<String> requestParams=request.getParameterNames();//������еĲ�����
		List<String> params=new ArrayList<String>();
		while (requestParams.hasMoreElements()) {
			params.add((String) requestParams.nextElement());
		}
		sortParamNames(params);// ����������С�������򣬽���磺adfd,bcdr,bff,zx

		String paramValues="";
		for (String param : params) {//ƴ�Ӳ���ֵ
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
