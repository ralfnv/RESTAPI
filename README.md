# RESTAPI php 5.4+
## Настройки

Настройки БД находятся в файле

```
config.json
```

Дамп таблицы в

```
sqldump/testProfi.sql
```

*Логин/пароль суперадмина*

```
sadmin/sadmin
```

## Опсание API

### Авторизация
Поле        |  Значение   |     Обязательность  | Описание
------------|-------------|---------------------|---------------------
action      |  auth.login |         +           | Событие
password      |  varchar(30) |         +           | пароль
login      |  varchar(30) |         +           | Логин

**Ответ при успешной авторизации**
```
{ 'state' => true, 'token'=>"токен" }
```
**Формат ответа при не успешной авторизации**
```
{ 'state' => false, "msg": "Неверные логин/пароль" }
```

### Просмотр информации по пользователю

Поле        |  Значение   |     Обязательность  | Описание
------------|-------------|---------------------|---------------------
action      |  user.view |         +           | Событие
token      |  varchar(24) |         +           | токен полученный при авторизации
userData[id]      |  int(11) |         +           | id пользователя


**Ответ зависит от прав пользователя**

для админа/суперадмина
```
{
    "id": "2",
    "name": "Пользователь",
    "age": "19",
    "login": "user",
    "password": "user",
    "permission": "0"
}
```
для обычного пользователя
```
{
    "name": "Пользователь",
    "age": "19"
}
```
## Редактирование пользователя
Поле        |  Значение   |     Обязательность  | Описание
------------|-------------|---------------------|---------------------
action      |  user.edit |         +           | Событие
token      |  varchar(24) |         +           | токен полученный при авторизации
userData[id]      |  int(11) |         +           | id пользователя
userData[name]      |  varchar(150) |         -           | имя (если передаётся, то не может быть пустым)
userData[age]     |  int(3) |         -           | возраст
userData[login]      |  varchar(30) |         -           | логин (если передаётся, то не может быть пустым)
userData[password]      |  varchar(30) |        -           |  пароль (если передаётся, то не может быть пустым)
userData[permission]      |  int(1) |         -          | тип пользователя (возможные начения описаны ниже)

**Ответ**
```				 
{
	"id": "2",
	"name": "Пользователь",
	"age": "19",
	"login": "user",
	"password": "user",
	"permission": "0"
}	
```

## Создание пользователя
Поле        |  Значение   |     Обязательность  | Описание
------------|-------------|---------------------|---------------------
action      |  user.edit |         +           | Событие
token      |  varchar(24) |         +           | токен полученный при авторизации
userData[id]      |  int(11) |         +           | id пользователя
userData[name]      |  varchar(150) |         +           | имя (если передаётся, то не может быть пустым)
userData[age]     |  int(3) |         -           | возраст
userData[login]      |  varchar(30) |         +           | логин (если передаётся, то не может быть пустым)
userData[password]      |  varchar(30) |       +           |  пароль (если передаётся, то не может быть пустым)
userData[permission]      |  int(1) |         +         | тип пользователя (возможные начения описаны ниже)

**Ответ**
```				 
{
	"id": "2",
	"name": "Пользователь",
	"age": "19",
	"login": "user",
	"password": "user",
	"permission": "0"
}	
```

**Возможные значения `userData[permission]`**
* 0 - пользователь (могут ставить админы и суперадминадмины)
* 1 - администратор (могут ставить только суперадминадмины)
* 2 - суперадмин (не доступно для установки)

В случае попытки передать некорректные права получим 
```
{ "state": false, "msg": "Не корректные права пользователя" }
```

### Удаление пользователя
Поле        |  Значение   |     Обязательность  | Описание
------------|-------------|---------------------|---------------------
action      |  user.delete |         +           | Событие
token      |  varchar(24) |         +           | токен полученный при авторизации
userData[id]      |  int(11) |         +           | id пользователя

