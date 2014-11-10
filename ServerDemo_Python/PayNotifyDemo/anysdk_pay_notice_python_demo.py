import hashlib

def anysdk_payment(request):

    '''

    contributed by 杜可夫 (duke.cliff@icloud.com)

    '''

    html = 'ok'

    if request.method == 'POST':

        #private_data may be refererd to as your own tracking number

        private_data = request.POST.get('private_data', '')

        server_id = request.POST.get('server_id', '')

        trade_status = request.POST.get('pay_status', '')

        channel = request.POST.get('channel_number', '')

        user_id = request.POST.get('user_id', '')

        total_fee = float(request.POST.get('amount', '0.0'))

        sign = request.POST.get('sign', '')



        private_key = 'xxxxxxxxxxxxxxxx'



        if trade_status == '1':

            validated_order = False

            temp_list = []

            for key, value in request.POST.items():

                if key != 'sign':

                    temp_list.append([key, value])

            temp_list = sorted(temp_list, cmp=lambda x,y: cmp(x[0], y[0]))

            raw_str = ''

            for item in temp_list:

                raw_str = raw_str + item[1]



            md5_raw_str = hashlib.md5(raw_str).hexdigest().lower()

            local_sign = hashlib.md5(md5_raw_str + private_key).hexdigest().lower()



            if local_sign == sign:

                validated_order = True



            if validated_order:

                #支付完成，并且合法，更新支付状态信息或者通知游戏服务器更新数据...

                pass



    html = 'ok'

    response = HttpResponse(html)

    return response
