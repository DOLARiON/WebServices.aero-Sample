# WebServices.aero - Пример подключения на PHP 5.3+

Краткий экскурс по подключению к JSON-API.<br>
Рабочий пример представленного кода: http://sample.demo.webservices.aero/

Тестовый Субагентский блок: http://agent.demo.webservices.aero/<br>
Тестовый Travel Office: http://office.demo.webservices.aero/

Тестовый ключ: **C330CA8C-DCDF-4CA8-A5E0-F5E4E1612440**<br>
WS TEST: http://ws.demo.webservices.aero/

PS. На сайте: http://json.parser.online.fr можно раскодировать JSON и посмотреть массив.

## Авторизация
```
{
    // Тип авторизации: Site, Application
    "Type":"Site",
    
    // Принадлежность компании: Agent — Субагент, Corporate — Корпорат
    "System":"Agent",
    
    // Ключ авторизации
    "Key":"XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
    
    // IP-адрес конечного пользователя. Используется для установки лимита кол-ва запросов в День/Час/Минуту/Секунду
    "UserIP":"127.0.0.1",
    
    // Уникальный идентификатор пользователя в системе Субагента. Может быть произвольным значением. Не обязательно к заполнению
    "UserUUID":"",
    
    // UTM-метки для учета в статистике
    "UTM_Source":"",
    "UTM_Medium":"",
    "UTM_Campaign":"",
    "UTM_Term":"",
    "UTM_Content":""
}
```

## Структура запроса:

```
{
    // Статично
    "jsonrpc":"2.0",
    
    // Любой желаемый цифровой ID запроса вашей системы... мы его не учитываем никаким образом... можно оставить "1"
    "id":1,
    
    // Запрашиваемый метод
    "method":"Ping",
    
    "params":[
        // Авторизационная запись
        {"Type":"Site","System":"Agent","Key":"XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX","UserIP":"127.0.0.1","UserUUID":""},
        
        // Центральная часть запроса
        {"Test":"OK!"},
        
        // Дополнительные параметры запроса
        {"Compress":"","Format":"Combined","Return":"ByTimelimit","Language":"RU","Currency":["RUB"],"Timelimit":120}
    ]
}
```

## Пример PING-запроса:

```
POST http://ws.demo.webservices.aero/ HTTP/1.1
Host: ws.demo.webservices.aero
Accept: */*
Connection: Keep-Alive
Content-Length: 291
Content-Type: application/x-www-form-urlencoded

{"jsonrpc":"2.0","id":1,"method":"Ping","params":[{"Type":"Site","System":"Agent","Key":"XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX","UserIP":"127.0.0.1","UserUUID":""},{"Test":"OK!"},{"Compress":"","Format":"Combined","Return":"ByTimelimit","Language":"RU","Currency":["RUB"],"Timelimit":120}]}
````

## 1. AviaSearch
Поиск авиабилетов.

```
POST http://ws.demo.webservices.aero/ HTTP/1.1
Host: ws.demo.webservices.aero
Accept: */*
Connection: Keep-Alive
Content-Length: 502
Content-Type: application/x-www-form-urlencoded

{"jsonrpc":"2.0","id":1,"method":"AviaSearch","params":[{"Type":"Site","System":"Agent","Key":"XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX","UserIP":"127.0.0.1","UserUUID":""},{"Routes":[{"Departure":"MOW","Arrival":"LED","Date":"2013-03-15"},{"Departure":"LED","Arrival":"MOW","Date":"2013-03-20"}],"Logic":"Default","Class":"Econom","Travellers":{"ADT":1,"CHD":0,"INF":0}},{"Compress":"","Format":"Combined","Return":"ByTimelimit","Language":"RU","Currency":["RUB","USD","EUR"],"TimeLimit":120}]}
```

## 2. AviaInformation
Получение BookID бронирования.

Центральная часть запроса:
```
{
    "RequestID":"52a5bf68af632ced0d000013",
    "Variants":[
        "53549E96-256E-417B-BC3B-EC105FB9E937",
        "876B600E-A2E8-4FD3-A131-44AA27FCE284"
    ]
}
```

## 3. AviaBook
Получение PNR.

Центральная часть запроса:
```
{
    "BookID":"5292d908af632c632b00000d",
    "Travellers":{
        "ADT":[
            {
                "Sex":"Male",
                "Surname":"IVANOV",
                "Name":"IVAN",
                "Birthday":{
                    "Day":"20",
                    "Month":"6",
                    "Year":"1983"
                },
                "Citizen":"RU",
                "Document":{
                    "Number":"4505917654",
                    "ExpireDate":{
                        "Day":"20",
                        "Month":"6",
                        "Year":"2028"
                    }
                },
                "Bonus":{
                    "Company":"PS",
                    "Number":"123456"
                }
            }
        ]
    },
    "Contacts":{
        "PhoneMobile":"+7 (495) 762-1684",
        "PhoneHome":"84957621684",
        "Email":"eg@edgroup.ru"
    }
}
```

## 4. AviaCheck
Проверка статуса PNR.

Центральная часть запроса:
```
{
    "PNR":"296KKV",
    "Surname":"IVANOV"
}
```

## 5. PaymentAction
Получение URL для переадресации пользователя в плат.шлюз.

Центральная часть запроса:
```
{
    "ID":"2825-2883",
    "Gate":"CreditCard",
    "ReturnSuccess":"http://testsite.ru/avia/check.html?pnr=296KKV&surname=IVANOV&PayStatus=Success",
    "ReturnFailure":"http://testsite.ru/avia/check.html?pnr=296KKV&surname=IVANOV&PayStatus=Failure",
    "ReturnPending":"http://testsite.ru/avia/check.html?pnr=296KKV&surname=IVANOV&PayStatus=Pending"
}
```

## 6. AviaCancel
Отмена бронирования.

Центральная часть запроса:
```
{
    "PNR":"296KKV",
    "Surname":"IVANOV"
}
```












