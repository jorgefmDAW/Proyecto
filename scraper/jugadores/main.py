from scraperJugadores import Scraper
import threading
import json
import time

class Main:
    @staticmethod
    def main():
        Main.savePlayersData()

    @staticmethod
    def savePlayersData():
        playersdata = []
        lock = threading.Lock()

        Scraper.getPlayersUrl()

        def iterateLinks(team_data: dict):
            # Extraemos los datos del diccionario que creamos en scraper.py
            urls = team_data["urls"]
            team_name = team_data["team"] 
            
            data_list = []
            starttime = time.time()
            for url in urls:
                # Le pasamos el equipo a la función para que no tenga que buscarlo
                player_info = Scraper.getPlayersInfo(url, team_name) 
                if player_info: 
                    data_list.append(player_info)
                time.sleep(0.2) 
            
            with lock:
                playersdata.extend(data_list)
                
            endtime = time.time()
            print(f"Thread {threading.current_thread().name} ({team_name}) finished. Time: {endtime-starttime:.2f} s")

        threads = []
        
        try:
            with open("players.txt", "r", encoding="utf-8") as f:
                for line in f:
                    data = json.loads(line)
                    t = threading.Thread(target=iterateLinks, args=(data,))
                    threads.append(t)
                    t.start()
        except FileNotFoundError:
            print("Error: No se encontró players.txt")
            return

        for t in threads:
            t.join()

        with open("playersdata.json", "w", encoding="utf-8") as f:
            f.write(json.dumps(playersdata, indent=4, ensure_ascii=False))
            
        print("¡Extracción finalizada! Datos en playersdata.txt")

if __name__ == "__main__":
    Main.main()