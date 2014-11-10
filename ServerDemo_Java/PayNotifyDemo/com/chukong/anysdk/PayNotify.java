package com.chukong.anysdk;

import java.lang.*;
import java.util.Collections;
import java.util.List;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.Comparator;
import java.util.Enumeration;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.io.UnsupportedEncodingException;

/**
 * AnySDK ֧��֪ͨ��ǩ�㷨
 * 
 * @author libo<libo@chukong-inc.com>
 * @date 2014-08-13
 */
public class PayNotify{

	/**
	 * AnySDK ����� PrivateKey
	 *
	 * ��ʽʹ�õ�ʱ��ǵ�ʹ��AnySDK�������ʽ�� PrivateKey
	 */
	private static String _privateKey = "";
	
	/**
	 * ȫ������,����base64
	 */
    private final String[] _strDigits = { "0", "1", "2", "3", "4", "5",
            "6", "7", "8", "9", "a", "b", "c", "d", "e", "f" };

	public void PayNotify () {}		
	
	/**
	 * ��֤ǩ��
	 *
	 * @param String paramValues ��ǩ�ַ���
	 * @param String originSign ��AnySDK���յ���signֵ
	 * @return boolean 
	 */
	public boolean checkSign (String paramValues, String originSign) {
		if (originSign == null) {
			return false;
		}
		String newSign = getSign(paramValues);
		return newSign.equals(originSign);
	}
	
	/**
	 * ���� private_key
	 */
	public void setPrivateKey(String privateKey){
		_privateKey = privateKey;
	}
	
	/**
	 * �����ǩ�ַ�����signֵ
	 * 
	 * @param String paramValues ��ǩ�ַ���
	 * @return String �������õ���signǩ��
	 */
	public String getSign(String paramValues){
		String md5Values=MD5Encode(paramValues);
		md5Values=MD5Encode(md5Values.toLowerCase()+_privateKey).toLowerCase();
		return md5Values;
	}
	
	/**
	 * MD5�����㷨
	 * 
	 * @param String sourceStr ��������ַ���
	 * @return String md5ֵ
	 */
	public String MD5Encode(String sourceStr) {
		String signStr=null;
		try {
			byte[] bytes = sourceStr.getBytes("utf-8");
			MessageDigest md5 = MessageDigest.getInstance("MD5");
			md5.update(bytes);
			byte[] md5Byte = md5.digest();
			if (md5Byte != null) {
				signStr = _byteToString( md5Byte);
			}
		} catch (NoSuchAlgorithmException e) {
		} catch (UnsupportedEncodingException e) {
		}
		return signStr;
	}
	
    /**
	 * ������ʽΪ���ָ��ַ���
	 */
    private String _byteToArrayString(byte bByte) {
        int iRet = bByte;
        // System.out.println("iRet="+iRet);
        if (iRet < 0) {
            iRet += 256;
        }
        int iD1 = iRet / 16;
        int iD2 = iRet % 16;
        return _strDigits[iD1] + _strDigits[iD2];
    }

    /**
	 * ������ʽֻΪ����
	 */
    private String _byteToNum(byte bByte) {
        int iRet = bByte;
        //System.out.println("iRet1=" + iRet);
        if (iRet < 0) {
            iRet += 256;
        }
        return String.valueOf(iRet);
    }

    /**
	 * ת���ֽ�����Ϊ16�����ִ�
	 */
    private String _byteToString(byte[] bByte) {
        StringBuffer sBuffer = new StringBuffer();
        for (int i = 0; i < bByte.length; i++) {
            sBuffer.append(_byteToArrayString(bByte[i]));
        }
        return sBuffer.toString();
    }
}
