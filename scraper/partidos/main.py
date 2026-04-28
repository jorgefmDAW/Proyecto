from scraperPartidos import Scraper
import threading
import json
import time

class MainJornadas:
    @staticmethod
    def main():
        MainJornadas.saveCalendarioData()

    @staticmethod
    def saveCalendarioData():
        calendario_data = []
        lock = threading.Lock()

        jornadas_info = Scraper.getJornadasUrls()

        def scrape_jornada(jornada_data: dict):
            jornada_num = jornada_data["jornada"]
            url = jornada_data["url"]
            
            starttime = time.time()
            data = Scraper.getJornadaInfo(url, jornada_num)
            
            if data and len(data["partidos"]) > 0:
                with lock:
                    calendario_data.append(data)
                    
            endtime = time.time()
            print(f"Jornada {jornada_num} extraída. Tiempo: {endtime-starttime:.2f} s")

        threads = []
        
        print("Iniciando extracción de las 38 jornadas...")
        
        for j_data in jornadas_info:
            t = threading.Thread(target=scrape_jornada, args=(j_data,))
            threads.append(t)
            t.start()
            time.sleep(0.1) 

        for t in threads:
            t.join()

        # Ordenamos las jornadas de la 1 a la 38
        calendario_data = sorted(calendario_data, key=lambda x: x["jornada"])

        with open("partidosdata.json", "w", encoding="utf-8") as f:
            json.dump(calendario_data, f, indent=4, ensure_ascii=False)
            
        print(f"¡Extracción finalizada! Calendario guardado en partidosdata.json con {len(calendario_data)} jornadas completas.")

if __name__ == "__main__":
    MainJornadas.main()