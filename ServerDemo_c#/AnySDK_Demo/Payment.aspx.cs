using System;
using System.Collections.Generic;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Security.Cryptography;
using System.Text;
using System.Collections.Specialized;
using System.Configuration;

namespace AnySDK_Demo
{
    public partial class Payment : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            string strMsg = "ok";
            try
            {
                var data = getData();
                if (data["sign"].ToString() == getSignForAnyValid() && data["pay_status"].ToString() == "1")
                {
                    //支付成功的处理
                    //注意判断金额，客户端可能被修改从而使用低金额购买高价值道具

                }            
            }
            catch (Exception ex)
            {
                //错误处理
                strMsg = ex.ToString();
            }

            Response.Write(strMsg);
        }

        Dictionary<string, object> getData()
        {
            Dictionary<string, object> data = new Dictionary<string, object>();
            foreach (string key in Request.Form)
            {
                data[key] = Request.Form[key];
            }
            return data;
        }

        //获得anysdk支付通知 sign,将该函数返回的值与any传过来的sign进行比较验证
        string getSignForAnyValid()
        {
            NameValueCollection requestParams = Request.Form;//获得所有的参数名
            List<string> ps = new List<string>();
            foreach (string key in requestParams)
            {
                ps.Add(key);
            }

            sortParamNames(ps);// 将参数名从小到大排序，结果如：adfd,bcdr,bff,zx

            string paramValues = "";
            foreach (string param in ps)
            {//拼接参数值
                if (param == "sign")
                {
                    continue;
                }
                string paramValue = requestParams[param];
                if (paramValue != null)
                {
                    paramValues += paramValue;
                }
            }
            string md5Values = MD5Encode(paramValues);
            md5Values = MD5Encode(md5Values.ToLower() + ConfigurationManager.AppSettings["AnySDK_Key"].ToString()).ToLower();
            return md5Values;
        }

        //MD5编码
        static string MD5Encode(string sourceStr)
        {
            MD5 md5 = new MD5CryptoServiceProvider();
            byte[] src = Encoding.UTF8.GetBytes(sourceStr);
            byte[] res = md5.ComputeHash(src, 0, src.Length);
            return BitConverter.ToString(res).ToLower().Replace("-", "");
        }
        //将参数名从小到大排序，结果如：adfd,bcdr,bff,zx
        static void sortParamNames(List<string> paramNames)
        {
            paramNames.Sort((string str1, string str2) => { return str1.CompareTo(str2); });
        }
    }
}