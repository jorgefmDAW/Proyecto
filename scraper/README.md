# Fantasy LaLiga Relevo Web Scraper
Este script escrito en python que busca en **[Futbol Fantasy](https://www.futbolfantasy.com)** la informacion de todos los jugadores de LaLiga y crea un archivo en **files/playersdata.txt** donde se guardan:
1. El equipo.
2. Nombre.
3. Posición.
4. Puntos (Solo de los partidos jugados).

## Instalación
```bash
 git clone https://github.com/aerg04/Fantasy-LaLiga-Relevo-Scraper.git
```
### Dependencias 
1. Request 2.32.3
2. Beautiful Soup 4.12.3

En caso de no tenerlas.

```bash
 pip install requests beautifulsoup4
```

## Ejecución
Ejecutar **main.py** aclaratoria dura alrededor de **1 minuto y 30 segundos en finalizar**.

## Como fue hecho
Utilizando Requests y BeautifulSoup, se pidio la pagina de cada jugador y luego se procedio a inspeccionar el codigo html, respectivamente. Para buscar en las páginas de 
todos los jugadores fue implementado el multithreading, cada thread busca los datos de los jugadores de un equipo.

