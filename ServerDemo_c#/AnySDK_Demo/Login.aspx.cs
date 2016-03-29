using System;
using System.Collections.Generic;
using System.Collections.Specialized;
using System.Text;
using System.IO;
using System.Net;
using System.Configuration;
using System.Web;
using System.Web.Script.Serialization;
using System.Web.UI;
using System.Web.UI.WebControls;

namespace AnySDK_Demo
{
    public partial class Login : System.Web.UI.Page
    {
        //登陆地址
        string loginCheckUrl = "http://oauth.anysdk.com/api/User/LoginOauth/";

        //连接超时
        int connectTimeOut = 3000;

        protected void Page_Load(object sender, EventArgs e)
        {
            string strMsg = "";
            try
            {
                strMsg = postLogin();
                
                JavaScriptSerializer serializer = new JavaScriptSerializer();
                Dictionary<string, object> dicMsg = serializer.Deserialize<Dictionary<string, object>>(strMsg);
                if (dicMsg["status"].ToString() == "ok")
                {                    
                    //这里可以做数据验证等其他操作
                    //Dictionary<string, object> common = (Dictionary<string, object>)rets["common"];
                    dicMsg["ext"] = "test";
                    strMsg = serializer.Serialize(dicMsg);
                }
            }
            catch (Exception ex)
            {
                strMsg = ex.ToString();
            }
            Response.Write(strMsg);
        }

        string postLogin()
        {         
            HttpWebRequest requester = WebRequest.Create(new Uri(loginCheckUrl)) as HttpWebRequest;
            requester.Method = "POST";
            requester.Timeout = connectTimeOut;
            requester.ContentType = "application/x-www-form-urlencoded";
            byte[] bs = Encoding.UTF8.GetBytes(getQueryString());
            requester.ContentLength = bs.Length;
            using (Stream reqStream = requester.GetRequestStream())
            {
                reqStream.Write(bs, 0, bs.Length);
            }

            HttpWebResponse responser = requester.GetResponse() as HttpWebResponse;
            using (StreamReader reader = new StreamReader(responser.GetResponseStream(), Encoding.UTF8))
            {
                return reader.ReadToEnd();
            }
        }

        string getQueryString()
        {
            NameValueCollection req = Request.Form;
            string args = "";
            foreach (string key in req.AllKeys)
            {
                args += key + "=" + req[key] + "&";
            }
            args = args.Substring(0, args.Length - 1);
            return args;
        }
    }
}