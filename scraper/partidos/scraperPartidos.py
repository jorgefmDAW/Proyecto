import requests
from bs4 import BeautifulSoup
import re

class Scraper:
    HEADERS = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
    }

    EQUIPOS_LALIGA = [
        "madrid", "barcelona", "villarreal", "espanyol", "sevilla", "betis", "elche",
        "oviedo", "celta", "mallorca", "atletico", "atlético", "girona", "osasuna", 
        "athletic", "sociedad", "valencia", "levante", "alaves", "alavés", "getafe", "rayo"
    ]

    @staticmethod
    def is_laliga_team(name):
        if not name: return False
        n = name.lower()
        return any(equipo in n for equipo in Scraper.EQUIPOS_LALIGA)

    @staticmethod
    def getJornadasUrls():
        urls = []
        for i in range(1, 39):
            urls.append({
                "jornada": i,
                "url": f"https://www.futbolfantasy.com/laliga/posibles-alineaciones/{i}"
            })
        return urls

    @staticmethod
    def getJornadaInfo(url: str, jornada: int):
        try:
            response = requests.get(url, headers=Scraper.HEADERS)
            if response.status_code != 200: return None
            
            soup = BeautifulSoup(response.content, 'html.parser')
            all_blocks = soup.find_all(lambda tag: tag.has_attr('class') and any(c in tag['class'] for c in ['match', 'partido', 'item']))
            
            bloques_validos = []
            for block in all_blocks:
                teams = []
                for img in block.find_all('img'):
                    nombre = img.get('alt') or img.get('title')
                    if nombre and len(nombre.strip()) > 2 and Scraper.is_laliga_team(nombre):
                        teams.append(nombre.strip())
                
                teams_unicos = []
                for t in teams:
                    if t not in teams_unicos: teams_unicos.append(t)

                if len(teams_unicos) >= 2:
                    bloques_validos.append({"bloque": block, "local": teams_unicos[0], "visitante": teams_unicos[1]})

            grupo_principal = bloques_validos[:10]
            partidos_data = []

            for item in grupo_principal:
                block = item["bloque"]
                # Usamos separator=' ' para que los números de las cajas verdes no se peguen
                block_text = block.get_text(separator=' ', strip=True)
                
                # --- FECHA (Día de la semana) ---
                fecha_final = ""
                day_match = re.search(r'(Lun|Mar|Mie|Mié|Jue|Vie|Sab|Sáb|Dom)', block_text, re.IGNORECASE)
                if day_match:
                    fecha_final = day_match.group(1).capitalize().replace('é', 'e').replace('á', 'a')
                else:
                    fecha_final = "TBD"

                # --- HORA ---
                hora = ""
                time_match = re.search(r'(\d{1,2}:\d{2})', block_text)
                if time_match: 
                    hora = time_match.group(1)

                # --- GOLES (Lógica para cajas separadas) ---
                goles_local = 0
                goles_visitante = 0
                
                # 1. Limpiamos la hora del texto para que sus números no nos molesten
                texto_limpio = re.sub(r'\d{1,2}:\d{2}', '', block_text)
                # 2. Buscamos todos los números sueltos que queden
                todos_los_numeros = re.findall(r'\b(\d)\b', texto_limpio)
                
                # En la web, si hay resultado, los dos primeros números suelen ser los goles 
                # (las cajas verdes que se ven en tu captura)
                if len(todos_los_numeros) >= 2:
                    goles_local = int(todos_los_numeros[0])
                    goles_visitante = int(todos_los_numeros[1])

                partidos_data.append({
                    "local": item["local"],
                    "visitante": item["visitante"],
                    "goles_local": goles_local,
                    "goles_visitante": goles_visitante,
                    "fecha": fecha_final,
                    "hora": hora
                })

            return {"jornada": jornada, "partidos": partidos_data}
        except Exception as e:
            return None