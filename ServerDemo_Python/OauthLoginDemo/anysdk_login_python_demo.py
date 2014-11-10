import httplib

def login_game_xxx(request):
    #contributed by 杜可夫 duke.cliff@icloud.com
    html = ''
    ret = 'login failed'

    private_key = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
    #loginCheckUrl = 'http://oauth.anysdk.com/api/User/LoginOauth/'

    if request.method == 'POST':
        post_key = request.POST.get('private_key', '')
        if post_key == private_key:
            post_data = {}
            post_data = request.POST

            anysdk_conn = httplib.HTTPConnection('oauth.anysdk.com')
            headers = {"Content-Type": "application/x-www-form-urlencoded"}

            post_str = ''
            first = True
            for key, value in post_data.items():
                if not first:
                    post_str += '&'
                first = False
                post_str += key + '=' + value

            anysdk_conn.request("POST", '/api/User/LoginOauth/', post_str, headers)

            response = anysdk_conn.getresponse()

            if response.status == 200:
                content = response.read()
                print "any_sdk login result:", content
                resp_dict = json.loads(content)
                resp_dict['ext'] = 'game_tag'
                ret = resp_dict

    html = simplejson.dumps(ret, ensure_ascii=False)
    response = HttpResponse(html)

    return response