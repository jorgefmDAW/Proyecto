import json
import os

equipos_map = {
    "Real Madrid": 1,
    "Barcelona": 2,
    "Villarreal": 3,
    "Espanyol": 4,
    "Sevilla": 5,
    "Betis": 6,
    "Elche": 7,
    "Real Oviedo": 8,
    "Celta": 9,
    "Mallorca": 10,
    "Atletico": 11,
    "Girona": 12,
    "Osasuna": 13,
    "Athletic": 14,
    "Real Sociedad": 15,
    "Valencia": 16,
    "Levante": 17,
    "Alaves": 18,
    "Getafe": 19,
    "Rayo Vallecano": 20
}

ruta_json_entrada = 'playersdata.json'
ruta_json_salida = '/home/jorge/dwes/pruebas/playersdata.json'

def procesar_jugadores():
    try:
        with open(ruta_json_entrada, 'r', encoding='utf-8') as file:
            jugadores = json.load(file)
            
        jugadores_procesados = []
        
        for jugador in jugadores:
            nombre = jugador[0]
            posicion = jugador[1]      
            equipo_nombre = jugador[2] 
            
            # --- NUEVOS CAMPOS DEL SCRAPER ---
            edad_bruta = jugador[3]  # Ej: "26 años" o "Desconocida"
            nacionalidad = jugador[4]
            puntos = jugador[5] 
            
            equipo_id = equipos_map.get(equipo_nombre)
            
            if equipo_id is None:
                print(f"⚠️ Aviso: El equipo '{equipo_nombre}' del jugador {nombre} no coincide con la base de datos.")
            
            # Nos quedamos solo con la primera palabra de la edad
            edad_str = str(edad_bruta).split(" ")[0]
            
            # Convertimos a entero si es un número (evita errores si dice "Desconocida")
            edad_limpia = int(edad_str) if edad_str.isdigit() else None
            
            # Añadimos los datos limpios al array procesado
            jugadores_procesados.append([nombre, posicion, equipo_id, edad_limpia, nacionalidad, puntos])

        directorio_salida = os.path.dirname(ruta_json_salida)
        if not os.path.exists(directorio_salida):
            os.makedirs(directorio_salida)
        
        with open(ruta_json_salida, 'w', encoding='utf-8') as file:
            json.dump(jugadores_procesados, file, ensure_ascii=False, indent=4)
            
        print(f"✅ ¡Éxito! Archivo JSON procesado y guardado en: {ruta_json_salida}")

    except FileNotFoundError:
        print(f"❌ Error: No se encontró el archivo de entrada '{ruta_json_entrada}'.")
    except Exception as e:
        print(f"❌ Ocurrió un error inesperado: {e}")

# Ejecutar la función
if __name__ == "__main__":
    procesar_jugadores()