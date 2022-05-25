# Period
Clase para el manejo de periodos de tiempo.

Esta librería utiliza [DateTime](https://www.php.net/manual/es/class.datetime.php)

En su lugar puede utilizar [Period](https://github.com/frugone/Period) la cual utiliza 
[Carbon](https://carbon.nesbot.com/)


## Instalación
``` sh
composer require frugone/simpleperiod
```

### Periodo de fecha de dos meses
```
$period = Period::months(2);
echo $period; // From: 2021-09-25 00:19:43, To: 2021-11-25 00:19:43 
```

### Periodo de fecha de dos años
```
$period = Period::years(2);
echo $period; // From: 2021-09-25 00:19:43, To: 2021-11-25 00:19:43 
```

### Periodo de 3 semanas anteriores y 2 posteriores
```
$period = Period::weeks(3,2);
echo $period; // From: 2021-11-04 00:19:43, To: 2021-12-09 00:19:43 
```

### Perdiodo de 120 minutos en zona horaria de Uruguay
```
$period = Period::minutes(120)->toTimezone( TimeZone::TZ_UY);
print_r($period);
```
Result: 
```
Libraries\Period Object
(
    [startDate] => DateTime Object
        (
            [date] => 2021-11-24 18:19:43.000000
            [timezone_type] => 3
            [timezone] => America/Montevideo
        )

    [endDate] => DateTime Object
        (
            [date] => 2021-11-24 20:19:43.000000
            [timezone_type] => 3
            [timezone] => America/Montevideo
        )

    [timezone] => UTC
    [outputFormat] => Y-m-d H:i:s
)
```

### Cambiar el formato en que se muestran las fechas
```
$period = Period::months(2);
echo $period; // From: 2021-09-25 00:19:43, To: 2021-11-25 00:19:43 

$period->outputFormat = 'Y-m-d';
echo $period; // From: 2021-09-25, To: 2021-11-25 

```

### Setear timezone de salida
Timezone por defecto 
```
$period = Period::months(2);
print_r($period);
```
Result: 
```
Libraries\Period Object
(
    [startDate] => DateTime Object
        (
            [date] => 2021-09-25 00:19:43.000000
            [timezone_type] => 3
            [timezone] => Europe/Berlin
        )

    [endDate] => DateTime Object
        (
            [date] => 2021-11-25 00:19:43.000000
            [timezone_type] => 3
            [timezone] => Europe/Berlin
        )

    [timezone] => UTC
    [outputFormat] => Y-m-d H:i:s
) 
```

Timezone Uruguay
```
$period->toTimezone(TimeZone::TZ_UY);
print_r($period);
```
Result:  
``` 
Libraries\Period Object
(
    [startDate] => DateTime Object
        (
            [date] => 2021-09-24 19:19:43.000000
            [timezone_type] => 3
            [timezone] => America/Montevideo
        )

    [endDate] => DateTime Object
        (
            [date] => 2021-11-24 20:19:43.000000
            [timezone_type] => 3
            [timezone] => America/Montevideo
        )

    [timezone] => UTC
    [outputFormat] => Y-m-d H:i:s
)
``` 

### Indicando en que timezone fueron ingresadas las fechas podemos convertir estas fechas al timezone adecuado ( por defecto UTC ) por ejemplo para realizar consultas en la db

Supongamos que los usuarios ingresan un rango de fechas para una busqueda,  el usuario ingresará las fechas en su zona horaria pero en la DB los datos están guardados en UTC, En este caso podemos crear el objeto Period  y convertir las fechas a UTC indicando en que timezone fueron ingresadas

Ingreso fechas en zona horaria de Uruguay
``` 
$period = Period::create( '2021-11-05 13:56', '2021-11-09 13:56:39');

```
Convierto las fechas a UTC
```
$period->convertToTimezone(TimeZone::TZ_UY);
print_r($period);
```
Result:
```
Libraries\Period Object
(
    [startDate] => DateTime Object
        (
            [date] => 2021-11-05 16:56:00.000000
            [timezone_type] => 3
            [timezone] => UTC
        )

    [endDate] => DateTime Object
        (
            [date] => 2021-11-09 16:56:39.000000
            [timezone_type] => 3
            [timezone] => UTC
        )

    [timezone] => UTC
    [outputFormat] => Y-m-d H:i:s
)
```

### Funciones utiles para crear graficas

Si queremos graficar los valores de un mes en intervalos de 2 dias

```
$period = Period::months(1);
$range = $period->getDatePeriodByTime( 2 , 'day');

foreach( $range as $step ){
  print_r($step->format('Y-m-d'));
}
```
Result:
```
2021-10-25
2021-10-27
2021-10-29
2021-10-31
2021-11-02
2021-11-04
2021-11-06
2021-11-08
2021-11-10
2021-11-12
2021-11-14
2021-11-16
2021-11-18
2021-11-20
2021-11-22
2021-11-24
```

Si queremos obtener un rango de fechas en una cantidad determina de pasos, por ejemplo 7
```
$range = $period->getDatePeriod(7);
foreach( $range as $step ){
	print_r($step->format('Y-m-d H:i:s'));
}
```
Resutl:
```
2021-10-25 00:19:43
2021-10-29 10:45:26
2021-11-02 21:11:09
2021-11-07 07:36:52
2021-11-11 18:02:35
2021-11-16 04:28:18
2021-11-20 14:54:01
```
