import requests
from bs4 import BeautifulSoup
import json
import os
import time
import re

class Scraper:
    HEADERS = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
    }

    @staticmethod
    def getPlayersUrl():
        urls = [
            "https://www.futbolfantasy.com/laliga/equipos/alaves",
            "https://www.futbolfantasy.com/laliga/equipos/athletic",
            "https://www.futbolfantasy.com/laliga/equipos/atletico",
            "https://www.futbolfantasy.com/laliga/equipos/barcelona",
            "https://www.futbolfantasy.com/laliga/equipos/betis",
            "https://www.futbolfantasy.com/laliga/equipos/celta",
            "https://www.futbolfantasy.com/laliga/equipos/espanyol",
            "https://www.futbolfantasy.com/laliga/equipos/elche",
            "https://www.futbolfantasy.com/laliga/equipos/getafe",
            "https://www.futbolfantasy.com/laliga/equipos/girona",
            "https://www.futbolfantasy.com/laliga/equipos/levante",
            "https://www.futbolfantasy.com/laliga/equipos/mallorca",
            "https://www.futbolfantasy.com/laliga/equipos/osasuna",
            "https://www.futbolfantasy.com/laliga/equipos/real-oviedo",
            "https://www.futbolfantasy.com/laliga/equipos/rayo-vallecano",
            "https://www.futbolfantasy.com/laliga/equipos/real-madrid",
            "https://www.futbolfantasy.com/laliga/equipos/real-sociedad",
            "https://www.futbolfantasy.com/laliga/equipos/sevilla",
            "https://www.futbolfantasy.com/laliga/equipos/valencia",
            "https://www.futbolfantasy.com/laliga/equipos/villarreal"
        ]
        
        if not os.path.exists("players.txt"):
            print("Generando archivo players.txt con las URLs y equipos...")
            for url in urls:
                try:
                    response = requests.get(url.rstrip(), headers=Scraper.HEADERS)
                    if response.status_code != 200:
                        continue
                except Exception as e:
                    continue
                
                soup = BeautifulSoup(response.content, 'html.parser')
                a_tags = soup.find_all('a', class_=lambda c: c and 'jugador' in c)
                hrefs = list({a['href'] for a in a_tags if 'href' in a.attrs})

                # Extraemos el nombre del equipo de la URL
                team_name = url.split('/')[-1].replace('-', ' ').title()
                
                team_data = {
                    "team": team_name,
                    "urls": hrefs
                }

                with open("players.txt", "a", encoding="utf-8") as f:
                    f.write(json.dumps(team_data, ensure_ascii=False) + '\n')
                    
                time.sleep(0.5)
            print("¡Archivo players.txt generado con éxito!")


    @staticmethod
    def getPlayersInfo(url: str, team_name: str):
        try:
            response = requests.get(url, headers=Scraper.HEADERS)
            
            if response.status_code != 200:
                return ["Error Web", "Desconocida", team_name, "Desconocida", "Desconocida", []]
            
            soup = BeautifulSoup(response.content, 'html.parser')
            
            # --- 1. NOMBRE ---
            name_tag = soup.find('h1')
            name = name_tag.text.strip() if name_tag else "Desconocido"
            if "." in name:
                name = name.split(".")[1].lstrip()
                
            # --- 2. POSICIÓN ---
            posicion = "Desconocida"
            pos_tag = soup.find(lambda tag: tag.name in ['span', 'div', 'b', 'strong'] and tag.get_text(strip=True) in ['POR', 'DEF', 'MED', 'CEN', 'DEL'])
            
            if pos_tag:
                posicion = pos_tag.get_text(strip=True)
                
            # --- 3. EQUIPO ---
            team = team_name
            
            # --- 4. EDAD Y NACIONALIDAD ---
            edad = "Desconocida"
            nacionalidad = "Desconocida"
            
            for tag in soup.find_all(['span', 'div', 'td', 'strong', 'b']):
                texto = tag.get_text(strip=True)
                if texto == "Edad":
                    sibling = tag.find_next_sibling()
                    if sibling:
                        edad_raw = sibling.get_text(strip=True)
                        # Cortamos en el primer paréntesis '(' y nos quedamos con la parte izquierda
                        edad = edad_raw.split('(')[0].strip()
                elif texto == "Nacionalidad":
                    sibling = tag.find_next_sibling()
                    if sibling:
                        nacionalidad_raw = sibling.get_text(strip=True)
                        # Cortamos el string en la primera coma (,) o barra (/) y nos quedamos con la parte 0
                        nacionalidad = re.split(r'[,\/]', nacionalidad_raw)[0].strip()
            
            # --- 5. PUNTOS (MODO TANQUE) ---
            puntos_array = [None] * 38 
            
            rows = soup.find_all(['tr', 'li'])
            
            for row in rows:
                row_text_clean = row.get_text(separator=' ', strip=True)
                row_text_lower = row_text_clean.lower()
                
                if "total" in row_text_lower:
                    continue
                
                match = re.search(r'^[^\d]*(\d{1,2})\b', row_text_clean)
                
                if not match:
                    continue 
                    
                jornada = int(match.group(1))
                
                if jornada < 1 or jornada > 38:
                    continue
                
                fantasy_span = row.find('span', class_=re.compile(r'laliga-fantasy'))
                puntos_jornada = None
                
                if fantasy_span:
                    text_val = str(fantasy_span.text).replace('\n', '').strip()
                    try:
                        puntos_jornada = int(text_val)
                    except ValueError:
                        if re.search(r'[a-zA-ZáéíóúÁÉÍÓÚñÑ]', text_val):
                            puntos_jornada = 0
                else:
                    estados = ["convocado", "lesionado", "sancionado", "descarte", "duda", "no jugó"]
                    if any(estado in row_text_lower for estado in estados):
                        puntos_jornada = 0
                
                if puntos_jornada is not None:
                    puntos_array[jornada - 1] = puntos_jornada

            max_jornada = 0
            for i, pt in enumerate(puntos_array):
                if pt is not None:
                    max_jornada = i + 1
                    
            puntos_reales = puntos_array[:max_jornada]
            puntos_reales = [p if p is not None else "" for p in puntos_reales]

            return [name, posicion, team, edad, nacionalidad, puntos_reales]
            
        except Exception as e:
            return ["Error Excepción", "Desconocida", team_name, "Desconocida", "Desconocida", []]