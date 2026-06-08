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
    "Oviedo": 8,
    "Celta": 9,
    "Mallorca": 10,
    "Atlético": 11,
    "Girona": 12,
    "Osasuna": 13,
    "Athletic": 14,
    "Real Sociedad": 15,
    "Valencia": 16,
    "Levante": 17,
    "Alavés": 18,
    "Getafe": 19,
    "Rayo": 20
}

ruta_json_entrada = 'partidosdata.json' 
ruta_json_salida = '/backend/partidosdata.json'

def procesar_partidosdata():
    try:
        with open(ruta_json_entrada, 'r', encoding='utf-8') as file:
            partidosdata = json.load(file)
            
        for jornada_data in partidosdata:
            jornada_num = jornada_data.get("jornada")
            
            for partido in jornada_data.get("partidos", []):
                equipo_local_nombre = partido["local"]
                equipo_visitante_nombre = partido["visitante"]
                
                id_local = equipos_map.get(equipo_local_nombre)
                id_visitante = equipos_map.get(equipo_visitante_nombre)
                
                if id_local is None:
                    print(f"Aviso: El equipo local '{equipo_local_nombre}' en la jornada {jornada_num} no coincide con la BD.")
                else:
                    partido["local"] = id_local  
                    
                if id_visitante is None:
                    print(f"Aviso: El equipo visitante '{equipo_visitante_nombre}' en la jornada {jornada_num} no coincide con la BD.")
                else:
                    partido["visitante"] = id_visitante 


        directorio_salida = os.path.dirname(ruta_json_salida)
        if not os.path.exists(directorio_salida):
            os.makedirs(directorio_salida)
        

        with open(ruta_json_salida, 'w', encoding='utf-8') as file:
            json.dump(partidosdata, file, ensure_ascii=False, indent=4)
            
        print(f"¡Éxito! Archivo del partidosdata procesado y guardado en: {ruta_json_salida}")

    except FileNotFoundError:
        print(f"Error: No se encontró el archivo de entrada '{ruta_json_entrada}'.")
    except Exception as e:
        print(f"Ocurrió un error inesperado: {e}")

# Ejecutar la función
if __name__ == "__main__":
    procesar_partidosdata()